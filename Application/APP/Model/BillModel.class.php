<?php
/**
 * Created by PhpStorm.
 * User: wb-wjy227944
 * Date: 2016/10/14
 * Time: 11:19
 */

namespace APP\Model;
header("Content-Type:text/html; charset=utf-8");

class BillModel {

	private $openid,$weixinID;
	private $obj;

	public function __construct(){
		$this->openid = $_SESSION['openid'];
		$this->weixinID = $_SESSION['weixinID'];
		$this->obj = M('bill');

	}

	/**
	 * 根据传入的类型，取得当前用户的中奖积分
	 * @param $typeID
	 * @return bool
	 */
	public function getBillIntegralByTypeID($typeID){


		$where['Bill_openid'] = $this->openid;
		$where['WEIXIN_ID'] = $this->weixinID;
		$where['Bill_type'] = $typeID;

		$sumIntegral = $this->obj->where($where)->sum("Bill_integral");

		if( false === $sumIntegral ){
			return false;
		}
		return $sumIntegral;

	}

	/**
	 * 查询当前用户的中奖信息
	 * @return mixed
	 */
	public function getBillInfo(){

		$where['Bill_Status'] = 0;
		$where['Bill_openid'] = $this->openid;
		$where['WEIXIN_ID'] = $this->weixinID;

		$data = $this->obj->where($where)->order('Bill_id DESC')->select();

		if(false === $data){
			return false;
		}

		return $data;
	}

	/**
	 * 追加新的中奖记录
	 * @param $data
	 * @return bool
	 */
	public function addBill($data){
		if( false === $this->obj->add($data)){
			return false;
		}

		return true;
	}

    /**
     * 根据传入的ID查询当前用户是否已经在Bill中存在该商品的记录(已领过该商品至少一次)
     * @param $id
     * @return bool
     */
    public function isBilledByID($id){

        $where['Bill_item_id'] = $id;
        $where['Bill_openid'] = $this->openid;
        $where['WEIXIN_ID'] = $this->weixinID;
        $where['bill_type'] = C('BILL_TYPE_ARR')['SEAL'];   //印章

        $data = M()->table('bill')->where($where)->find();

        if( $data ){
            return true;
        }
        return false;
    }

    /**
     * 兑换印章商品后追加bill记录
     * @param $id
     * @param $SealData
     * @param $cnCode
     * @return bool
     */
    public function addSealBillInfo($id,$SealData,$cnCode){

        $data['WEIXIN_ID']= $this->weixinID;
        $data['Bill_type']= C('BILL_TYPE_ARR')['SEAL'];
        $data['Bill_item_id']= $id;

        $data['Bill_GoodsName']= $SealData["flowerCity_name"];
        $data['Bill_GoodsDescription']= $SealData["flowerCity_name"];
        $data['Bill_openid']= $this->openid;

        $data['Bill_insertDate']= date("Y-m-d H:i:s",time());
        $data['Bill_editDate']= date("Y-m-d H:i:s",time());

        $data['Bill_goods_beginDate']= $SealData["flowerCity_fromDate"];
        $data['Bill_goods_endDate']= $SealData["flowerCity_endDate"];
        $data['Bill_goods_expirationDate']= $SealData["flowerCity_expirationDate"];

        $data['Bill_integral']= $SealData["flowerCity_flowerNum"];
        $data['Bill_SN']= $cnCode;
        $data['Bill_Status']= 0;

        if(false === M()->table('bill')->add($data)){
            return false;
        }

        return true;
    }


}