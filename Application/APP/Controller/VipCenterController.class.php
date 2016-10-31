<?php
/**
 * Created by PhpStorm.
 * User: wb-wjy227944
 * Date: 2016/10/13
 * Time: 14:36
 */
namespace APP\Controller;
use APP\Model\ToolModel;
use Think\Controller;

header("Content-type:text/html;charset=utf-8");

class VipCenterController extends CommonController {

    public function index(){

//        //页面调用测试用，正式需要删除
//        $_SESSION['openid'] = 'ow7PpjvH4W1C03q3G7AdruW426Sg';
//        $_SESSION['weixinID'] = 88;


        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        $_SESSION['vipInfo'] = D('Vip')->vipInfo();

        if(isset($action) && ('' != $action)){

            switch ($action){
                case 'center':
                    $this->center();
                    break;
                case 'VipInfoShow':
                    $this->VipInfoShow();
                    break;
                case 'referrerAdd':
                    $this->referrerAdd();
                    break;
                case 'vipWinningInfo':
                    $this->vipWinningInfo();
                    break;
                case 'VipAddress':
                    $this->VipAddress();
                    break;
                case 'VipAddressData':
                    $this->VipAddressData();
                    break;
                case 'VipdaliyShow':
                    $this->VipdaliyShow();
                    break;
                case 'VipdaliyData':
                    $this->VipdaliyData();
                    break;

                default:
                    $this->center();
                    break;
            }
        }

    }

    /**
     * 进行签到
     */
    private function VipdaliyData(){

        //取得该会员最新签到日期和积分数
        $signedDay = $_SESSION['vipInfo']['Vip_isSignedDayTime'];
        $integral = intval($_SESSION['vipInfo']['Vip_integral']);

        $nowDate = date("Y-m-d",time());
        if($signedDay == $nowDate){
            $arr['success'] = 0;
            $arr['msg'] = '你已经签到过啦!';
            echo json_encode($arr);
//            ToolModel::goBack('你已经签到过啦!');
            exit;
        }

        $plusIntegral = $_SESSION['config']['CONFIG_INTEGRALSETDAILY'];

        if( (isset($_POST['signidText'])) && ('' != I('post.signidText') )){

            $count = D('Common')->getCountBySignidText(I('post.signidText'));

            if(!$count || ($count <= 0)){
                $arr['success'] = 0;
                $arr['msg'] = '签到码错误，请重新输入';
                echo json_encode($arr);
                exit;
//                ToolModel::goBack('签到码错误，请重新输入');
//                exit;
            }

            $plusIntegral = $_SESSION['config']['CONFIG_DAILYPLUS'];
        }

        $newIntegral = $integral + $plusIntegral;

        //更新会员积分
        D('Vip')->updateIntegral($newIntegral);


        //追加积分变动时写入记录表中 功能
        $insertData['openid'] = $_SESSION['openid'];
        $insertData['event'] = '签到';
        $insertData['totalIntegral'] = $integral;
        $insertData['integral'] = $plusIntegral;
        $insertData['insertTime'] = date("Y-m-d H:i:s",time());

        if(D('Common')->addIntegralRecord($insertData)){

            $arr['success'] = 1;
            $arr['msg'] = '签到成功，'.$_SESSION['config']['CONFIG_VIP_NAME'].'加: '.$plusIntegral;

//            ToolModel::goToUrl('签到成功，'.$_SESSION['config']['CONFIG_VIP_NAME'].'加: '.$plusIntegral,__ROOT__."/APP/VipCenter/index/action/center/");
        }else{
            $arr['success'] = 0;
            $arr['msg'] = '签到失败';
//            ToolModel::goToUrl('签到失败，',__ROOT__."/APP/VipCenter/index/action/center/");
        }
        echo json_encode($arr);
        exit;

    }

    /**
     * 显示签到的页面
     */
    private function VipdaliyShow(){
        $this->display('VipCenter/Vipdaliy');
    }

    /**
     * 补填地区信息
     */
    private function VipAddress(){

        $data = D('Vip')->vipInfo();

        $this->assign('theVip',$data);
        $this->display('VipCenter/VipAddress');

    }

    /**
     * 更新地区
     */
    private function VipAddressData(){

        if(!isset($_POST['address']) || ('' == $_POST['address'])){

            $arr['success'] = 0;
            $arr['msg'] = '参数错误';

            echo json_encode($arr);
            exit;
        }

        //防止多次提交
        if( 0 != D('Vip')->getVipAdress()){

            $arr['success'] = 2;
            $arr['msg'] = '您已经填写过地区了';

            echo json_encode($arr);
            exit;
        }


        $data['Vip_address'] = intval(I('post.address'));
        $data['Vip_edittime'] = date("Y-m-d",time());

        $where['Vip_openid'] = $_SESSION['openid'];
        $where['WEIXIN_ID'] = $_SESSION['weixinID'];
        $where['Vip_isDeleted'] = 0;

        if(D('Vip')->updateVip($data,$where)){

            $arr['success'] = 1;
            $arr['msg'] = '提交成功,3秒后跳转！';
        }else{
            $arr['success'] = 0;
            $arr['msg'] = '提交失败！';
        }
        echo json_encode($arr);
        exit;

    }

    /**
     * 显示中奖详细信息
     */
    private function vipWinningInfo(){
        $data = D('Bill')->getBillInfo();

        if(false == $data){
            $this->assign('billInfoIsNull',true);
        }else{
            $count = count($data);
            for ($i=0;$i<$count;$i++){
                $data[$i]['Bill_type'] = C('BILL_NAME_ARR')[$data[$i]['Bill_type']];

            }

            $this->assign('data',$data);
        }

        $this->display('VipCenter/vipWinningInfo');
    }

    /**
     * 追加推荐人
     */
    private function referrerAdd(){
        $referrerID = intval(I('post.referrerID',0));
        $thisVipIntegral = intval(I('post.thisVipIntegral',0));

        if($referrerID == 0 || $thisVipIntegral == 0){
            $arr['success'] = -1;
            $arr['msg'] = '参数错误！';
            echo json_encode($arr);
            exit;
        }

        if($_SESSION['vipInfo']['Vip_comment'] == 'referrer'){
            $arr['success'] = -1;
            $arr['msg'] = "你已经补登过推荐人啦！";
            echo json_encode($arr);
            exit;
        }

        $ret = D('Vip')->getVipInfoByID($referrerID);

        if(!$ret){
            $arr['success'] = -1;
            $arr['msg'] = "不存在该推荐人，请确认！";
            echo json_encode($arr);
            exit;
        }

        if($ret['Vip_openid'] == $_SESSION['openid']){
            $arr['success'] = -1;
            $arr['msg'] = "推荐人不能是本人！";
            echo json_encode($arr);
            exit;
        }

        $newIntegral = $ret['Vip_integral'] + $_SESSION['config']['CONFIG_INTEGRALREFERRER'];
        if(D('Vip')->updateVipByID($referrerID,$newIntegral)){

            $data['openid'] = $referrerID;
            $data['event'] = '补登时推荐人加'.$_SESSION['config']['CONFIG_VIP_NAME'];
            $data['totalIntegral'] = $ret['Vip_integral'];
            $data['integral'] = $_SESSION['config']['CONFIG_INTEGRALREFERRER'];
            $data['insertTime'] = date("Y-m-d H:i:s",time());

            if(D('Common')->addIntegralRecord($data)){
                //存在推荐人的时候，新积分数 = 新会员初始积分数+额外积分数
                $thisNewVipIntegral = $thisVipIntegral + $_SESSION['config']['CONFIG_INTEGRAL_REFERRER_FOR_NEW_VIP'];

                $updateVipData['Vip_integral'] = $thisNewVipIntegral;
                $updateVipData['Vip_edittime'] = date("Y-m-d H:i:s",time());
                $updateVipData['Vip_comment'] = 'referrer';

                $updateVipWhere['Vip_openid'] = $_SESSION['openid'];
                $updateVipWhere['WEIXIN_ID'] = $_SESSION['weixinID'];

                D('Vip')->updateVip($updateVipData,$updateVipWhere);

                //追加积分变动时写入记录表中 功能
                $newData['openid'] = $_SESSION['openid'];
                $newData['event'] = '补登时会员加'.$_SESSION['config']['CONFIG_VIP_NAME'];
                $newData['totalIntegral'] = $thisVipIntegral;
                $newData['integral'] = $_SESSION['config']['CONFIG_INTEGRAL_REFERRER_FOR_NEW_VIP'];;
                $newData['insertTime'] = date("Y-m-d H:i:s",time());

                if(D('Common')->addIntegralRecord($newData)){

                    $addMsg = '';

                    $arr['success'] = 0;
                    
                    //根据IP地址取得城市名称 20151215
                    $city = getCity();

                    //判断是否是台州地区和路桥发布公众号，满足条件写入活动表
                    //if(strstr($city,'浙江') && $weixinID == 69){
                    if(strstr($city,ALLOW_PROVINCE)){
                        
                        $iphoneEventData['WEIXIN_ID'] = $_SESSION['weixinID'];
                        $iphoneEventData['ipE_name'] = $_SESSION['vipInfo']['Vip_name'];;
                        $iphoneEventData['ipE_sex'] = $_SESSION['vipInfo']['Vip_sex'];
                        $iphoneEventData['ipE_tel'] = $_SESSION['vipInfo']['Vip_tel'];
                        $iphoneEventData['ipE_openid'] = $_SESSION['openid'];
                        $iphoneEventData['ipE_referee_vipID'] = $referrerID;
                        $iphoneEventData['ipE_insertTime'] = date("Y-m-d H:i:s",time());
                        
                        if(D('Common')->addIphoneEvent($iphoneEventData)){
                            $addMsg = PHP_EOL.'推荐人同时获得一个印章';
                        }
                    }

                    $arr['msg'] = '提交成功！'.PHP_EOL.' 您追加'.
                        $_SESSION['config']['CONFIG_VIP_NAME'].'：'.
                        $_SESSION['config']['CONFIG_INTEGRAL_REFERRER_FOR_NEW_VIP'].PHP_EOL.'推荐人追加：'.
                        $_SESSION['config']['CONFIG_INTEGRALREFERRER'].$city.$addMsg;

                    echo json_encode($arr);
                    exit;

                }else{
                    $arr['success'] = -1;
                    $arr['msg'] = "提交失败！";
                    echo json_encode($arr);
                    exit;
                }
            }
        }
    }

    /**
     * 会员个人信息展示页
     */
    private function VipInfoShow(){

        //$_SESSION['vipInfo'] = D('Vip')->vipInfo();
        //获得印章总数
        $flowerCount = D('Common')->getAllSealCount($_SESSION['vipInfo']['Vip_id']);


        //印章中奖个数
        $afterBill = D('Bill')->getBillIntegralByTypeID(C('BILL_TYPE_ARR')['SEAL']);

        //剩余印章数
        $nowflowerCount = $flowerCount - $afterBill;

        //取得建言献策的抽奖次数
        $adviceCount = D('Common')->getAdviceCount();

        //取得答题刮刮卡获得的使用次数
        $scratchcardedTimes = D('Common')->getScratchcardUserCount();

        $count = intval($adviceCount) - intval($scratchcardedTimes);

        if($count>0){
            $this->assign('count',$count);
        }

        if( ($_SESSION['vipInfo']['Vip_comment']) != 'referrer'){
            $this->assign('noComment',true);
        }

        $this->assign('flowerCount',$flowerCount);
        $this->assign('nowflowerCount',$nowflowerCount);

        if( 0 != $_SESSION['vipInfo']['Vip_address']){
            $_SESSION['vipInfo']['Vip_address_text'] = C('AREA_NAME_ARR')[$_SESSION['vipInfo']['Vip_address']];
            $this->assign('area',true);
        }

        $this->assign('vipInfo',$_SESSION['vipInfo']);

        $this->assign('config_vipName',ToolModel::do3lenUtf8($_SESSION['config']['CONFIG_VIP_NAME']));

        $this->display('VipCenter/VipInfoShow');


    }

    /**
     * 显示会员中心主页面
     */
    private function center(){

        $thisVipSignedDayTime = $_SESSION['vipInfo']['Vip_isSignedDayTime'];
        $thisSignedDate = date("Y-m-d",time());

        //判断当前日期和最新的日期是否超过一天,超过一天则显示可以签到,不然显示已经签到过了
        if ((strtotime($thisSignedDate) - strtotime($thisVipSignedDayTime))/86400 < 1){
            $this->assign('isSigned',true);
        }

        $sql = "select rowno from
			  (select Vip_openid,
					  Vip_id,
					  Vip_integral,
					  (@rowno:=@rowno+1) as rowno
			  from Vip,
			  (select (@rowno:=0)) b
			where WEIXIN_ID = ".$_SESSION['weixinID']."
			AND Vip_isDeleted = 0
			order by Vip_integral desc,
			Vip_createtime asc) c
			where Vip_openid ='".$_SESSION['openid']."'";

       $ret = M()->table('Vip')->query($sql);

       $getIntegralRank = $ret[0]['rowno'];

       $this->assign('vipInfo',$_SESSION['vipInfo']);
       $this->assign('getIntegralRank',$getIntegralRank);

       $this->display('VipCenter');


    }

}