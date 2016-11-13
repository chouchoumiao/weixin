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

class IntegralCityController extends CommonController {

    public function index(){

        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if(isset($action) && ('' != $action)){

            switch ($action){
                case 'integralCity':
                    $this->integralCity();
                    break;
                case 'integralJudge':
                    $this->integralJudge();
                    break;
            }
        }

    }

    /**
     * 进行积分商城逻辑
     */
    private function integralCity(){
        //取得当前时间内积分商城活动的商品信息
        $data = D('IntegralCity')->getIntegralCityEvent();

        if(!$data){
            $this->assign('noData',true);
        }else{
            $this->assign('data',$data);
        }
        $this->display('IntegralCity/IntegralCity');
    }

    /**
     * 显示积分商城页面
     */
    private function integralJudge(){
        //判断传入参数是否存在,不存在则返回
        if(!isset($_POST["fromIntegralID"])){
            ToolModel::jsonReturn(JSON_ERROR,'参数错误,请确认!');
        }

        //接收传过来的商品ID，查询是否存在该参数对应ID的商品信息
        $thisIntegralID = intval(I('post.fromIntegralID'));

        $integralCityInfoByID = D('IntegralCity')->getIntegralCityInfoByID($thisIntegralID);
        if(!$integralCityInfoByID){
            ToolModel::jsonReturn(JSON_ERROR,'无该商品,请确认！');
        }

        //取得库存 如果库存<=0 则不能进行 20151222
        $thisIntegralCtiyStock = intval($integralCityInfoByID["integralCity_stockCount"]);

        if($thisIntegralCtiyStock <= 0){
            ToolModel::jsonReturn(JSON_ERROR,'库存不足，无法兑换！'.$thisIntegralID);
        }

        //追加功能 如果同一商品今天已经兑换 2次则不能在进行兑换
        if(D('Bill')->isBilledSameDay($thisIntegralID)){
            ToolModel::jsonReturn(JSON_ERROR,'同一商品每天只能兑换一次');
        }

        //取得会员当前积分
        $thsVipIntegralNum = $_SESSION['Vip_integral'];

        //该商品所需的积分数
        $thisIntegralNum = $integralCityInfoByID["integralCity_integralNum"];

        //会员是否有兑换该商品的积分
        $newVipIntegralNum = $thsVipIntegralNum - $thisIntegralNum;

        $weixinName = $_SESSION['config']['CONFIG_VIP_NAME'];


        //不够积分
        if($newVipIntegralNum < 0){
            ToolModel::jsonReturn(JSON_ERROR,'您的'.$weixinName.'不足兑换该商品');
        }

        //够积分可以兑换的逻辑

        //更新当前会员的积分数
        $data['Vip_integral'] = $newVipIntegralNum;
        $where['Vip_openid'] = $_SESSION['openid'];
        $where['WEIXIN_ID'] = $_SESSION['weixinID'];

        if( !D('Vip')->updateVip($data,$where)){
            ToolModel::jsonReturn(JSON_ERROR,'会员信息更新失败');
        }

        //积分变动追加到integralRecord表
        D('Common')->addIntegralRecord($_SESSION['openid'],$weixinName.'兑换后减少的'.$weixinName,$thsVipIntegralNum,$thisIntegralNum);

        //取得该商品库存数
        $thisIntegralCtiyStock = $integralCityInfoByID["integralCity_stockCount"];
        $newthisIntegralCtiyStock = $thisIntegralCtiyStock - 1;

        //将该商品的库存减一
        //将该商品的库存减一,更新该商品信息
        if(!D('IntegralCity')->updateIntegralCityStockCount($thisIntegralID,$newthisIntegralCtiyStock)){
            ToolModel::jsonReturn(JSON_ERROR,'更新库存失败，无法兑换！');
        }

        //生成兑换码
        $cnCode = ToolModel::snMaker('01');
        if(!D('Bill')->addIntegralCityBillInfo($thisIntegralID,$integralCityInfoByID,$cnCode)){
            ToolModel::jsonReturn(JSON_ERROR,'生成交易记录时失败，无法兑换');
        }

        //最后返回正确信息
        ToolModel::jsonReturn(JSON_SUCCESS,"兑换成功,兑换码:"."\n".$cnCode."\n"."请记下兑换码或者在会员中心画面查询");
    }

}