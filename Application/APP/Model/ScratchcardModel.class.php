<?php
/**
 * Created by PhpStorm.
 * User: wb-wjy227944
 * Date: 2016/10/14
 * Time: 11:19
 */

namespace APP\Model;
header("Content-Type:text/html; charset=utf-8");

class ScratchcardModel {

	private $openid,$weixinID;

	public function __construct(){
		$this->openid = $_SESSION['openid'];
		$this->weixinID = $_SESSION['weixinID'];
	}

	/**
	 * 获得当前公众号的刮刮卡活动信息
	 * @return bool
	 */
	public function getScratchcardMain(){
		$nowDate = date("Y-m-d",time());

		$where['scratchcard_beginDate'] = array('elt',$nowDate);
		$where['scratchcard_endDate'] = array('egt',$nowDate);
		$where['scratchcard_isDeleted'] = 0;
		$where['WEIXIN_ID'] = $this->weixinID;

		$data = M()->table('scratchcard_main')->where($where)->order('scratchcard_id DESC')->find();

		if(false === $data){
			return false;
		}

		return $data;
	}

	

}