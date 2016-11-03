<?php
/**
 * Created by PhpStorm.
 * User: wb-wjy227944
 * Date: 2016/10/14
 * Time: 11:19
 */

namespace APP\Model;
header("Content-Type:text/html; charset=utf-8");

class ForwardingGiftModel {

	private $openid,$weixinID;

	public function __construct(){
		$this->openid = $_SESSION['openid'];
		$this->weixinID = $_SESSION['weixinID'];
	}

    /**
     * 根据传入的状态获取信息
     * @param $flag
     * @return bool|mixed
     */
	public function getForwardingGiftInfoByFlag($flag){

        $where['FORWARDINGGIFT_OPENID'] = $this->openid;
        $where['WEIXIN_ID'] = $this->weixinID;
        $where['FORWARDINGGIFT_ISOK'] = $flag;

        $data = M()->table('forwardingGift')->where($where)->select();

        if(false === $data){
            return false;
        }

        return $data;
    }

    /**
     * 取得当前用户的分享有礼信息
     * @return bool|mixed
     */
    public function getForwardingGiftInfo(){

        $where['FORWARDINGGIFT_OPENID'] = $this->openid;
        $where['WEIXIN_ID'] = $this->weixinID;

        $data = M()->table('forwardingGift')->where($where)->find();

        if(false === $data){
            return false;
        }

        return $data;
    }

}