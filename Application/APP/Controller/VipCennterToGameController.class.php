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

class VipCennterToGameController extends CommonController {

    public function doAction(){

        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if(isset($action) && ('' != $action)){

            switch ($action){
                case 'rank':
                    $this->rank();
                    break;
                case 'sealCity':
                    $this->sealCity();
                    break;
                case 'sealCityJudge':
                    $this->sealCityJudge();
                    break;
                default:
                    
                    break;
            }
        }

    }

    /**
     * 点击兑换按钮后提交
     */
    private function sealCityJudge(){

        //判断传入参数是否存在,不存在则返回
        if(!isset($_POST["sealID"])){
            ToolModel::jsonReturn(JSON_ERROR,'参数错误,请确认!');
        }

        //接收传过来的商品ID，查询是否存在该参数对应ID的商品信息
        $thisIntegralID = intval(I('post.sealID'));

        $sealInfoByID = D('Seal')->getSealInfoByID($thisIntegralID);
        if(!$sealInfoByID){
            ToolModel::jsonReturn(JSON_ERROR,'无该商品,请确认！');
        }

        //取得库存 如果库存<=0 则不能进行 20151222
        $thisIntegralCtiyStock = intval($sealInfoByID["flowerCity_stockCount"]);

        if($thisIntegralCtiyStock <= 0){
            ToolModel::jsonReturn(JSON_ERROR,'库存不足，无法兑换！');
        }

        //追加功能 如果同一商品只能兑换一次
        if( D('Bill')->isBilledByID($thisIntegralID)){
            ToolModel::jsonReturn(JSON_ERROR,'您已经领取过该商品，不能再次领取！');
        }

        //获得当前会员的印章总数
        $beforeBill = D('Common')->getAllSealCount($_SESSION['vipInfo']['Vip_id']);

        //当前会员的印章兑换个数
        $afterBill = D('Bill')->getBillIntegralByTypeID(C('BILL_TYPE_ARR')['SEAL']);

        //剩余可用印章数
        $flowerCount = intval($beforeBill) - intval($afterBill);

        //取得兑换该商品所需要的印章数
        $thisFlowerNum = intval($sealInfoByID["flowerCity_flowerNum"]);
        $newVipFlowerNum = $flowerCount - $thisFlowerNum;

        //当该会员印章总量小于商品所需印章数时，提示不可以进行兑换
        if($newVipFlowerNum < 0){
            ToolModel::jsonReturn(JSON_ERROR,'您的印章数不足兑换该商品');
        }

        //当该会员印章总量大于商品印章时，可以进行兑换
        //库存减一
        $newthisIntegralCtiyStock = $thisIntegralCtiyStock - 1;

        //将该商品的库存减一,更新该商品信息
        if(!D('Seal')->updateStockCount($thisIntegralID,$newthisIntegralCtiyStock)){
            ToolModel::jsonReturn(JSON_ERROR,'更新库存失败，无法兑换！');
        }

        //生成兑换码
        $openidCn = substr($_SESSION['openid'],-4)."01";
        $cnCode = ToolModel::snMaker($openidCn);

        //追加兑换记录
        if (!D('Bill')->addSealBillInfo($thisIntegralID,$sealInfoByID,$cnCode)){
            ToolModel::jsonReturn(JSON_ERROR,'生成交易记录时失败，无法兑换');
        }

        D('Common')->addIntegralRecord($_SESSION['openid'],'印章兑换记录(不减印章）',0,$thisFlowerNum);

        //正常流程走完后返回
        $msg = "兑换成功,兑换码:"."\n".$cnCode."\n"."请记下兑换码或者在会员中心画面查询";
        ToolModel::jsonReturn(JSON_SUCCESS,$msg);
    }

    /**
     * 印章可以兑换商品显示
     */
    private function sealCity(){
        //取得当前时间内活动的商品信息
        $data = D('Seal')->getSealEvent();

        if(!$data){
            $this->assign('noData',true);
        }else{
            $this->assign('data',$data);
        }
        $this->display('Seal/SealCity');
    }

    /**
     * 印章排行
     */
    private function rank(){
        //根据iphone活动表中的印章数量进行排名，并关联Vip表取出会员信息
        $data = D('Vip')->getSealRank();
        
        if($data){
            $count = count($data);
            for( $i=0; $i<$count; $i++ ){
                if( $_SESSION['openid'] == $data[$i]['Vip_openid']){
                    $this->assign('vipCount',$data[$i]['flowerCount']);
                    $this->assign('rank',$data[$i]['rownum']);
                    break;
                }
            }

            //手机号码隐藏中间五位
            for( $i=0; $i<$count; $i++ ){
                $data[$i]['Vip_tel'] = ToolModel::hideTel($data[$i]['Vip_tel']);
            }

            $this->assign('data',$data);
        }
        
        $this->display('Seal/SealRank');
        
    }

}