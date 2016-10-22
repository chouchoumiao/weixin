<?php
/**
 * Created by PhpStorm.
 * User: wb-wjy227944
 * Date: 2016/10/14
 * Time: 11:19
 */

namespace APP\Model;
header("Content-Type:text/html; charset=utf-8");

class VipModel {

	private $openid,$weixinID;

	public function __construct(){
		$this->openid = $_SESSION['openid'];
		$this->weixinID = $_SESSION['weixinID'];

	}

	/**
	 * 查询当前用户是否是会员
	 * @return bool
	 */
	public function isVip(){
		$where['Vip_openid'] = $this->openid;
		$where['WEIXIN_ID'] = $this->weixinID;
		$where['Vip_isDeleted'] = 0;

		if( false === (M()->table('Vip')->where($where)->count()) ){
			return false;
		}
		return true;

	}

	/**
	 * 获得当前会员信息
	 * @return bool|mixed
	 */
	public function vipInfo()
	{
		$where['Vip_openid'] = $this->openid;
		$where['WEIXIN_ID'] = $this->weixinID;
		$where['Vip_isDeleted'] = 0;

		$lineData = M()->table('Vip')->where($where)->find();

		if(false === $lineData){
			return false;
		}

		if($lineData['Vip_sex'] == 1){
			$lineData['Vip_sex_text'] = '男';
		}else if($lineData['Vip_sex'] == 0){
			$lineData['Vip_sex_text'] = '女';
		}else{
			$lineData['Vip_sex_text'] = '未知';
		}

		if($lineData['Vip_comment'] == 'referrer'){
			$lineData['Vip_comment_text'] = '存在推荐人';
		}else{
			$lineData['Vip_comment_text'] = '没有推荐人';
		}
		
		return $lineData;
	}

	/**
	 * 根据传入的id取得该会员信息
	 * @param $id
	 * @return bool|mixed
	 */
	public function getVipInfoByID($id){

		$where['Vip_id'] = $id;
		$where['Vip_isDeleted'] = 0;
		$where['WEIXIN_ID'] = $this->weixinID;

		$data = M()->table('Vip')->where($where)->find();

		if( false === $data){
			return false;
		}
		return $data;
	}

	/**
	 * 更新传入的ID对应的积分(推荐人)
	 * @param $id
	 * @param $integral
	 * @return bool
	 */
	public function updateVipByID($id,$integral){

		$data['Vip_integral'] = $integral;
		$data['Vip_edittime'] = date("Y-m-d H:i:s",time());

		$where['Vip_id'] = $id;
		$where['WEIXIN_ID'] = $this->weixinID;

		if( false === M()->table('Vip')->where($where)->save($data)){
			return false;
		}

		return true;

	}

	/**
	 * 更新Vip
	 * @param $data
	 * @param $where
	 * @return bool
	 */
	public function updateVip($data,$where){

		if( false === M()->table('Vip')->where($where)->save($data)){
			return false;
		}

		return true;

	}

}