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
     * 判断当前用户当天是否已经提交过记录了
     * @return bool
     * 提交过了则返回true 否则返回false
     */
    public function isTodayExist(){

        $where['FORWARDINGGIFT_OPENID'] = $this->openid;
        $where['WEIXIN_ID'] = $this->weixinID;
        $where['FORWARDINGGIFT_COMMITDATE'] = date("Y-m-d",time());

        if( intval(M()->table('forwardingGift')->where($where)->count()) >= 1){
            return true;
        }
        return false;

    }

    /**
     * 追加分享有礼记录
     * @param $thumbPath
     * 缩略图文件路径
     * @param $imgPath
     * 原图文件路径
     * @return bool
     */
    public function addForwardingGift($thumbPath,$imgPath){

        $data['WEIXIN_ID'] = $this->weixinID;
        $data['FORWARDINGGIFT_OPENID'] = $this->openid;
        $data['FORWARDINGGIFT_IMGURL'] = $thumbPath;
        $data['FORWARDINGGIFT_BIGIMGURL'] = $imgPath;
        $data['FORWARDINGGIFT_INTEGRAL'] = 0;
        $data['FORWARDINGGIFT_REPLY'] = '';
        $data['FORWARDINGGIFT_CREATETIME'] = date("Y-m-d H:i:s",time());
        $data['FORWARDINGGIFT_COMMITDATE'] = date("Y-m-d",time());
        $data['FORWARDINGGIFT_EDITETIME'] = date("Y-m-d H:i:s",time());
        $data['FORWARDINGGIFT_ISOK'] = 0;

        if(false === M()->table('forwardingGift')->add($data)){
            return false;
        }

        return true;
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