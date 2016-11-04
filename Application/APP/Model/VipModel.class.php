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
     * 取得limit名的会员信息
     * @param $limit
     * @return bool
     */
	public function getVipInfoByLimit($limit){

        $field = 'Vip_name,Vip_integral';

        $where['WEIXIN_ID'] = $this->weixinID;
        $where['Vip_isDeleted'] = 0;

        $order = 'Vip_integral DESC,Vip_createtime ASC';

        $data = M()->table('Vip')->field($field)->where($where)->order($order)->limit($limit)->select();

        if(false === $data){
            return false;
        }
        return $data;

    }

	/**
	 * 查询当前用户是否是会员
	 * @return bool
	 */
	public function isVip(){
		$where['Vip_openid'] = $this->openid;
		$where['WEIXIN_ID'] = $this->weixinID;
		$where['Vip_isDeleted'] = 0;

        $count = M()->table('Vip')->where($where)->count();

		if( false === ($count) ){
			return false;
		}
		return intval($count);

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

	public function getVipAdress(){
		$where['Vip_openid'] = $this->openid;
		$where['WEIXIN_ID'] = $this->weixinID;
		$where['Vip_isDeleted'] = 0;

		$data = M()->table('Vip')->field('Vip_address')->where($where)->find();

		if(false === $data){
			return false;
		}

		return $data['Vip_address'];
	}

	/**
	 * 更新Vip
	 * @param $data
	 * @param $where
	 * @return bool
	 */
	public function updateVip($data,$where){

		$data = M()->table('Vip')->where($where)->save($data);
		if( false === $data){
			return false;
		}

		//更新Session
		$_SESSION['vipInfo'] = self::vipInfo();

		return true;
	}

	/**
	 * 格局传入的新积分，更新会员表，并更新session
	 * @param $newIntegral
	 * @return bool
	 */
	public function updateIntegral($newIntegral){

        $data['Vip_integral'] = $newIntegral;
        $data['Vip_isSignedDayTime'] = date("Y-m-d",time());;
        $data['Vip_edittime'] = date("Y-m-d H:i:s",time());

		$where['Vip_openid'] = $this->openid;
		$where['WEIXIN_ID'] = $this->weixinID;

		$data = M()->table('Vip')->where($where)->save($data);
		if( false === $data){
			return false;
		}

		//更新Session
		$_SESSION['vipInfo'] = self::vipInfo();
		return true;
	}

}