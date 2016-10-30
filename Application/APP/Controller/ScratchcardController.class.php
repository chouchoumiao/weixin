<?php
/**
 * Created by PhpStorm.
 * User: wb-wjy227944
 * Date: 2016/10/13
 * Time: 14:36
 */
namespace APP\Controller;
use Think\Controller;

header("Content-type:text/html;charset=utf-8");

class ScratchcardController extends CommonController {

    public function index(){

        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if(isset($action) && ('' != $action)){

            switch ($action){
                case 'showView':
                    $this->showView();
                    break;
                case 'scratchcard':
                    $this->scratchcard();
                    break;
            }
        }

    }

    /**
     * 进行刮奖逻辑
     */
    private function scratchcard(){
        $NowDate = date("Y-m-d",time());
        $nowTime  = date("Y-m-d H:i:s",time());

        //根据得到的刮刮卡ID进行查询该活动信息
        $scratchcard_id = intval(addslashes($_POST['scratchcard_id']));

        $data = D('Scratchcard')->getScratchcardDataByID($scratchcard_id);

        if(!$data){
            $arr["status"]= "noData";
            echo json_encode($arr);
            exit;
        }

        //判断是否还有刮奖次数
        //取得建言献策的抽奖次数
        $adviceCount = D('Advice')->getAdviceCount();

        if(intval($adviceCount) <= 0 ){
            $arr["status"]= "NoEnoughIntegral";
            echo json_encode($arr);
            exit;
        }

        //取得刮刮卡使用次数
        $times = D('Scratchcard')->getScratchcardUserCountByID($scratchcard_id);

        $isFirst = 0;
        //初次进行刮刮卡活动的追加记录
        if($times <= 0){
            D('Scratchcard')->addScratchcard($scratchcard_id);
            $scratchcardedTimes = 0;
            $isFirst = 1;
        }else{
            $scratchcardedTimes = $times;
        }

        $isScratchcardTimes = $adviceCount - $scratchcardedTimes;

        if($isScratchcardTimes <= 0){
            $arr["status"]= "NoEnoughIntegral";
            echo json_encode($arr);
            exit;
        }

        $detail_name = json_decode($data["scratchcard_detail_name"]);
        $detail_description = json_decode($data["scratchcard_detail_description"]);
        $detail_probability = json_decode($data["scratchcard_detail_probability"]);
        $detail_count = json_decode($data["scratchcard_detail_count"]);

        //初始化奖品对应的分数为0
        $thisIntegral = 0;

        //设置SN码
        $openidCn = "";
        $openidCn = substr($_SESSION['openid'],-4)."03";

        //设置尚未中奖的flag = NO
        $isOK = "NO";
        $detail_nameCount = count($detail_name);


        for($i = 0; $i<$detail_nameCount;$i++){
            if($detail_count[$i] >0){
            //根据取得的随机值，判断是否中奖
                if (rand(1, 100) <= $detail_probability[$i]){
                    //arr数组是中奖后需返回的数据
                    $cnCode = $this->snMaker($openidCn);
                    $arr["status"]= "ok";
                    $arr["prizelevel"]=$detail_name[$i];
                    $arr["prizedescription"]=$detail_description[$i];
                    $arr["SN"]= $cnCode;
                    $arr["adress"] = $data['scratchcard_address'];
                    $arr["expirationDate"] = $data['scratchcard_expirationDate'];

                    //中奖后新追加Bill交易表
                    $mainMaxTimes = $data['scratchcard_times'];

                    $insertData['WEIXIN_ID'] = $_SESSION['weixinID'];
                    $insertData['Bill_type'] = '003';
                    $insertData['Bill_item_id'] = $scratchcard_id;
                    $insertData['Bill_GoodsName'] = $detail_name[$i];
                    $insertData['Bill_GoodsDescription'] = $detail_description[$i];
                    $insertData['Bill_openid'] = $_SESSION['openid'];
                    $insertData['Bill_insertDate'] = $nowTime;
                    $insertData['Bill_editDate'] = $nowTime;
                    $insertData['Bill_goods_beginDate'] = $data['scratchcard_beginDate'];
                    $insertData['Bill_goods_endDate'] = $data['scratchcard_endDate'];
                    $insertData['Bill_goods_expirationDate'] = $data['scratchcard_expirationDate'];
                    $insertData['Bill_integral'] = $thisIntegral;
                    $insertData['Bill_SN'] = $cnCode;
                    $insertData['Bill_Status'] = 0;

                    if(D('Bill')->addBill($insertData)){
                        //该奖品库存减一
                        $detail_count[$i] =  strval(intval($detail_count[$i]) - 1);
                        $newCount = json_encode($detail_count);

                        $updateData['scratchcard_detail_count'] = $newCount;
                        $updateData['scratchcard_editTime'] = $nowTime;

                        $updateWhere['scratchcard_id'] = $scratchcard_id;

                        //更新刮刮卡主表
                        D('Scratchcard')->UpdateScratchcard($updateData,$updateWhere);

                        //中奖后设置flag=YES
                        $isOK = "YES";
                    }

                }
                //如果已经中奖，则跳出for循环
                if($isOK == "YES"){
                    break;
                }
            }
        }
        if($isFirst == 0){
            //抽奖结束后 刮奖次数加1
            $userUpdateData['scratchcard_userCount'] = $times +1;
            $userUpdateData['scratchcard_userEditDate'] = $nowTime;

            $userUpdateWhere['scratchcard_userIsAllow'] = 1;
            $userUpdateWhere['scratchcard_userOpenid'] = $_SESSION['openid'];
            $userUpdateWhere['WEIXIN_ID'] = $_SESSION['weixinID'];
            $userUpdateWhere['scratchcard_id'] = $scratchcard_id;

            D('Scratchcard')->UpdateScratchcardUser($userUpdateData,$userUpdateWhere);


        }

        //返回数组
        echo json_encode($arr);
        exit;
    }

    /**
     * 显示刮刮卡页面
     */
    private function showView(){
        
        //取得当前时间有效的刮刮卡Main信息
        $data = D('Scratchcard')->getScratchcardMain();

        if($data){
            $this->assign('data',$data);
            $this->assign('detail_name',json_decode($data['scratchcard_detail_name']));
            $this->assign('detail_description',json_decode($data['scratchcard_detail_description']));
            $this->assign('detail_count',json_decode($data['scratchcard_detail_count']));
        }

        $this->display('Scratchcard');
        
    }

    //生成兑换码
    private function snMaker($pre) {
        $date = date('Ymd');
        $rand = rand(1000,9999);
        $time = mb_substr(time(), 5, 5, 'utf-8');
        $serialNumber = $time.$pre.$date.$rand;
        return $serialNumber;
    }

}