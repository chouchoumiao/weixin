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
     * 新增会员信息
     * @param $name
     * @param $sex
     * @param $tel
     * @param $integral
     * @param $comment
     * @return bool
     */
    public function addVipInfo($name,$sex,$tel,$integral,$comment){


        $data['WEIXIN_ID'] = $this->weixinID;
        $data['Vip_name'] = $name;
        $data['Vip_sex'] = $sex;
        $data['Vip_tel'] = $tel;
        $data['Vip_address'] = 0;       //新增会员时 默认地区未选择,需要补填
        $data['Vip_openid'] = $this->openid;
        $data['Vip_integral'] = $integral;
        $data['Vip_createtime'] = date("Y-m-d H:i:s",time());
        $data['Vip_edittime'] = date("Y-m-d H:i:s",time());
        $data['Vip_isSignedDayTime'] = null;
        $data['Vip_isDeleted'] = 0;
        $data['Vip_isSubscribed'] = 1;
        $data['Vip_comment'] = $comment;

        if( false === M()->table('Vip')->add($data)){
            return false;
        }

        return true;

    }

    /**
     * 根据传入的分值和推荐人ID,更新对应ID的推荐人的积分数
     * @param $id
     * @param $integral
     * @return bool
     */
    public function updateReferrerIntegral($id,$integral){

        $data['Vip_integral'] = $integral;
        $data['Vip_edittime'] = date("Y-m-d H:i:s",time());

        $where['Vip_id'] = $id;
        $where['WEIXIN_ID'] = $this->weixinID;

        if(false === M()->table('Vip')->where($where)->save($data)){
            return false;
        }

        return true;
    }

    /**
     * 查询传入的ID号查询该会员是否已经存在
     * @param $referrerId
     * @return bool
     */
    public function getReferrerInfo($referrerId){

        $where['Vip_id'] = $referrerId;
        $where['Vip_isDeleted'] = 0;
        $where['WEIXIN_ID'] = $this->weixinID;

        return M()->table('Vip')->where($where)->find();
    }

    /**
     * 查询传入的手机号是否已经存在
     * @param $tel
     * @return bool
     */
    public function isTelExist($tel){

        $where['Vip_tel'] = $tel;
        $where['Vip_isDeleted'] = 0;
        $where['WEIXIN_ID'] = $this->weixinID;

        if(M()->table('Vip')->where($where)->find()){
            return true;
        }
        return false;

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

        $data = M()->table('Vip')->where($where)->find();

		if($data){
			return true;
		}
		return false;

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

    /**
     * 获得印章排名
     * @return bool
     */
    public function getSealRank(){
        $sql = "SELECT A.Vip_id,
               A.Vip_openid,
               A.Vip_name,
               A.Vip_tel,
               B.flowerCount,
               @rownum := @rownum +1 rownum
        FROM Vip AS A
        INNER JOIN(
            SELECT @rownum :=0,ipE_referee_vipID,count(*) AS flowerCount
            FROM iphoneEvent
            GROUP BY ipE_referee_vipID
            ORDER BY flowerCount DESC
        )  AS B
        WHERE A.Vip_id = B.ipE_referee_vipID
        LIMIT 20";

        $data = M()->table('Vip')->query($sql);

        if(false === $data){
            return false;
        }
        return $data;
    }

}