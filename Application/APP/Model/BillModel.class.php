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

		$data = $this->obj->where($where)->order('Bill_id')->select();

		if(false === $data){
			return false;
		}

		return $data;

	}


}