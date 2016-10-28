<?php
/**
 * Created by PhpStorm.
 * User: wb-wjy227944
 * Date: 2016/10/14
 * Time: 11:19
 */

namespace APP\Model;
header("Content-Type:text/html; charset=utf-8");

class AdviceModel {

	private $openid,$weixinID;

	public function __construct(){
		$this->openid = $_SESSION['openid'];
		$this->weixinID = $_SESSION['weixinID'];

	}

	/**
	 * 判断当前用户的建言献策的剩余次数
	 * @return bool
	 */
	public function getAdviceCount(){

		$where['WEIXIN_ID'] = $this->weixinID;
		$where['ADVICE_OPENID'] = $this->openid;
		$where['ADVICE_EVENT'] = 1;

		$count = M()->table('adviceInfo')->where($where)->count();

		if(false === $count){
			return false;
		}
		return $count;
	}

}