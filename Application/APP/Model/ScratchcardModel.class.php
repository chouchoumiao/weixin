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


	/**
	 * 更新刮奖次数
	 * @param $data
	 * @param $where
	 * @return bool
	 */
	public function UpdateScratchcardUser($data,$where){

		 if( false === M()->table('scratchcard_user')->where($where)->save($data)){
			 return false;
		 }
		return true;
	}

	/**
	 * 更新刮刮卡主表
	 * @param $data
	 * @param $where
	 * @return bool
	 */
	public function UpdateScratchcard($data,$where){

		 if( false === M()->table('scratchcard_main')->where($where)->save($data)){
			 return false;
		 }
		return true;
	}

	/**
	 * 根据传入的ID取得活动信息
	 * @param $id
	 * @return bool
	 */
	public function getScratchcardDataByID($id){
		$where['scratchcard_isDeleted'] = 0;
		$where['scratchcard_id'] = $id;
		$where['WEIXIN_ID'] = $this->weixinID;

		$data = M()->table('scratchcard_main')->where($where)->find();
		if(false === $data){
			return false;
		}
		return $data;
	}

//	/**
//	 * 根据传入的ID判断该活动是否存在
//	 * @param $id
//	 * @return bool
//	 */
//	public function isExistByID($id){
//
//		$where['scratchcard_isDeleted'] = 0;
//		$where['scratchcard_id'] = $id;
//		$where['WEIXIN_ID'] = $this->weixinID;
//		$count = M()->table('scratchcard_main')->where($where)->count();
//		if($count  == 1){
//			return true;
//		}
//		return false;
//	}

	/**
	 * 根据传入的人ID，判断当前用户进行刮奖的次数
	 * @param $id
	 * @return bool
	 */
	public function getScratchcardUserCountByID($id){

		$where['scratchcard_userIsAllow'] = 1;
		$where['scratchcard_userOpenid'] = $this->openid;
		$where['scratchcard_id'] = $id;

		$data = M()->table('scratchcard_user')->field('scratchcard_userCount')->where($where)->find();

		if(false === $data){
			return false;
		}

		return $data['scratchcard_userCount'];
	}

	/**
	 * 新增刮刮卡记录
	 * @param $id
	 * @return bool
	 */
	public function addScratchcard($id){

		$data['scratchcard_id'] = $id;
		$data['WEIXIN_ID'] = $this->weixinID;
		$data['scratchcard_userOpenid'] = $this->openid;
		$data['scratchcard_userCount'] = 1;
		$data['scratchcard_userEditDate'] = date("Y-m-d H:i:s",time());
		$data['scratchcard_userIsAllow'] = 1;

		if( false === M()->table('scratchcard_user')->add($data)){
			return false;
		}

		return true;
	}

	

}