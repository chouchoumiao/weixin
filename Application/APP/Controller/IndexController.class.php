<?php
namespace APP\Controller;
use APP\Model\ToolModel;
use Think\Controller;

include 'WechatCallbackapiController.class.php';

class IndexController extends Controller {

    public function index(){

        //获得传入的weixinID的Get值
        $weixinID = intval(I('get.weixinID'));

        //取得该weixinID的信息
        $weixinInfo = D('Common')->getAdminInfoByWeixinID($weixinID);

        //如果没有对应的信息则输出信息并终止
        if(!$weixinInfo) die('无该信息');

        //获得关于微信相关的Get值获取(微信示例代码)
        $weixinGetArr['echostr'] = $_GET['echostr'];
        $weixinGetArr['signature'] = $_GET['signature'];
        $weixinGetArr['timestamp'] = $_GET['timestamp'];
        $weixinGetArr['nonce'] = $_GET['nonce'];
        $weixinGetArr['msg_signature'] = $_GET['msg_signature'];
        $weixinGetArr['encrypt_type'] = $_GET['encrypt_type'];
        $weixinGetArr['encrypt_type'] = $_GET['msg_signature'];

        //获得微信的GLOBALS信息(微信示例代码)
        $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];

        //调用微信验证类
        $wechatObj = new WechatCallbackapi($weixinInfo,$weixinGetArr,$postStr);
        if (!isset($_GET['echostr'])) {
            $wechatObj->responseMsg();
        }else{
            $wechatObj->valid();
        }
    }
    
    public function VipBD(){
        
        //判断是否存在POST数据
        if(!isset($_POST)){
            
            ToolModel::jsonReturn(JSON_ERROR,'参数错误！');
        }

        //先判断是否已经是会员身份了
        if (D('Vip')->isVip()){
            ToolModel::jsonReturn(JSON_ERROR,'您已经是会员了！');
        }

        $thisVip_tel = I('post.tel');

        //判断新会员输入的手机号是否VIp数据表中已经存在，存在则提示错误
        if(D('Vip')->isTelExist($thisVip_tel)){

            ToolModel::jsonReturn(JSON_ERROR,'您的联系手机已经被使用，请返回确认！');
        }

        //取得该公众号的基础信息
        $config = D('Common')->getCon();

        $thisVip_integral = $config['CONFIG_INTEGRALINSERT'];
        //成为新会员获得积分数
        $newVipFirstIntegral = $config['CONFIG_INTEGRALINSERT'];
        //存在推荐人的时候，新会员注册成功时，该推荐人可获得积分数
        $plusIntegral = $config['CONFIG_INTEGRALREFERRER'];
        //存在推荐人的时候，新会员注册成功时，新会员也可以获得额外积分数
        $plusIntegralForNewVip = $config['CONFIG_INTEGRAL_REFERRER_FOR_NEW_VIP'];
        $weixinName = $config['CONFIG_VIP_NAME'];

        //推荐人
        $thisVip_referrer = I('post.referrer','');
        $thisVip_name = I('post.name','');
        $thisVip_sex = I('post.sex');

        //不存在推荐人
        if( !isset($thisVip_referrer) || ('' == $thisVip_referrer)){
            //追加会员信息
            if (!D('Vip')->addVipInfo($thisVip_name,$thisVip_sex,$thisVip_tel,$thisVip_integral,'')){

                ToolModel::jsonReturn(JSON_ERROR,'新增会员失败，请重试！');
            }

            //新增会员信息后追加写入记录表中
            $this->addRecord($_SESSION['openid'],'会员绑定无推荐人',0,$thisVip_integral);

            $msg = " 亲，您已经成功绑定了会员！</br>
                        初次绑定获得$weixinName".$newVipFirstIntegral;
            ToolModel::jsonReturn(JSON_SUCCESS,$msg);

        }

        //存在推荐人
        $referrerInfo = D('Vip')->getReferrerInfo($thisVip_referrer);
        //检查是否存在该推荐人
        if( !$referrerInfo){
            ToolModel::jsonReturn(JSON_ERROR,'不存在该推荐人，请确认！');
        }

        //取得推荐人的原始积分数
        $oldVipIntegral = $referrerInfo['Vip_integral'];

        //与推荐积分数累加后生成新的积分数，并写入Vip数据表中
        $newVipIntegral = $oldVipIntegral + $plusIntegral;

        //更新推荐人的积分
        if(!D('Vip')->updateReferrerIntegral($thisVip_referrer,$newVipIntegral)){

            ToolModel::jsonReturn(JSON_ERROR,'推荐人分值更新失败，请重试！');
        }

        //追加积分变动时写入记录表中 功能
        $this->addRecord($thisVip_referrer,'会员绑定推荐人加'.$weixinName,$oldVipIntegral,$plusIntegral);

        //存在推荐人的时候，新积分数 = 新会员初始积分数+额外积分数
        $thisVipIntegral = $thisVip_integral + $plusIntegralForNewVip;

        if (!D('Vip')->addVipInfo($thisVip_name,$thisVip_sex,$thisVip_tel,$thisVipIntegral,'Vip_comment')){
            ToolModel::jsonReturn(JSON_ERROR,'新增会员失败，请重试！');
        }

        //新增会员信息后追加写入记录表中
        $this->addRecord($_SESSION['openid'],'会员绑定存在推荐人会员加'.$weixinName,0,$thisVipIntegral);
        //根据IP地址取得城市名称 20151215
        $city = getCity();

        //判断是否是台州地区和路桥发布公众号，满足条件写入活动表
        //if(strstr($city,'浙江') && $weixinID == 69){
        if(strstr($city,ALLOW_PROVINCE)){

            if(D('Common')->addIphoneEvent($thisVip_name,$thisVip_sex,$thisVip_tel,$thisVip_referrer)){

                $msg = "亲，您已经成功绑定了会员！</br>
                    初次绑定获得$weixinName".$newVipFirstIntegral."</br>
                    存在推荐人额外获得$weixinName:".$plusIntegralForNewVip."</br>
                    推荐人也同时获得额外$weixinName".$plusIntegral."</br>
                    推荐人还获得一个印章，积攒印章可得大奖"."</br>";
                ToolModel::jsonReturn(JSON_SUCCESS,$msg);
            }
        }

        $msg = "亲，您已经成功绑定了会员！</br>
                    初次绑定获得$weixinName".$newVipFirstIntegral."</br>
                    存在推荐人额外获得$weixinName:".$plusIntegralForNewVip."</br>
                    推荐人也同时获得额外$weixinName".$plusIntegral."</br>
                    推荐人还获得一个印章，积攒印章可得大奖"."</br>";
        ToolModel::jsonReturn(JSON_SUCCESS,$msg);
    }

    /**
     * 设置追加积分记录的数据
     * @param $openid
     * @param $event
     * @param $oldIntegral
     * @param $plusIntegral
     * @return mixed
     */
    private function addRecord($openid,$event,$oldIntegral,$plusIntegral){
        $data['openid'] = $openid;
        $data['event'] = $event;
        $data['totalIntegral'] = $oldIntegral;
        $data['integral'] = $plusIntegral;
        $data['insertTime'] = date("Y-m-d H:i:s",time());

        D('Common')->addIntegralRecord($data);

    }
}