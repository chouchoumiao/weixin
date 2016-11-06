<?php
namespace APP\Controller;
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
            $arr['success'] = -1;
            $arr['msg'] = "参数错误！";
            echo json_encode($arr);
            exit;
        }

        //先判断是否已经是会员身份了
        if (D('Vip')->isVip()){
            $arr['success'] = -1;
            $arr['msg'] = "您已经是会员了！";
            echo json_encode($arr);
            exit;
        }

        $thisVip_tel = I('post.tel');

        //判断新会员输入的手机号是否VIp数据表中已经存在，存在则提示错误
        if(D('Vip')->isTelExist($thisVip_tel)){
            $arr['success'] = -1;
            $arr['msg'] = "您的联系手机已经被使用，请返回确认！";
            echo json_encode($arr);
            exit;
        }

        //推荐人
        $thisVip_referrer = I('post.referrer','');

        //存在推荐人
        if( (isset($thisVip_referrer)) && ('' != $thisVip_referrer) ){

            $referrerInfo = D('Vip')->getReferrerInfo($thisVip_referrer);
            //检查是否存在该推荐人
            if( !$referrerInfo){
                $arr['success'] = -1;
                $arr['msg'] = "不存在该推荐人，请确认！";
                echo json_encode($arr);
                exit;
            }

            
        }

        $thisVip_name = I('post.name','');
        $thisVip_sex = addslashes($_POST['sex']);
        $nowTime = date("Y-m-d H:i:s",time());

        $thisVip_isDeleted = 0;
        $thisVip_isSubscribed = 1;
        $thisVip_comment = "";
        $isInsertOK = "";

        $msg = "";


        //取得该公众号的基础信息，写入session
        $config = D('Common')->getCon();

        $thisVip_integral = $config['CONFIG_INTEGRALINSERT'];
        //成为新会员获得积分数
        $newVipFirstIntegral = $config['CONFIG_INTEGRALINSERT'];
        //存在推荐人的时候，新会员注册成功时，该推荐人可获得积分数
        $plusIntegral = $config['CONFIG_INTEGRALREFERRER'];
        //存在推荐人的时候，新会员注册成功时，新会员也可以获得额外积分数
        $plusIntegralForNewVip = $config['CONFIG_INTEGRAL_REFERRER_FOR_NEW_VIP'];
        $weixinName = $config['CONFIG_VIP_NAME'];





    }
}