<?php
/**
 * Created by PhpStorm.
 * User: wujiayu
 * Date: 2016/11/21
 * Time: 23:48
 */

namespace Admin\Model;
header("Content-Type:text/html; charset=utf-8");

class WeixinModel {

    private $userName;
    private $weixinID;

    public function __construct(){
        $this->userName = $_SESSION['username'];
    }

    /**
     * 取得当前登录用户可以管理的微信公众号一览表
     * @return bool
     */
    public function getWeixinInfoByNowUser(){

        $where['username'] = $this->userName;
        $where['weixinStatus'] = 1;

        $data = M()->table('AdminToWeiID')->where($where)->select();

        if($data === false){
            return false;
        }

        return $data;
    }

    /**
     * 取得当前微信公众号的信息
     * @return bool
     */
    public function getTheWeixinNameInfo(){
        $where['id'] = $this->weixinID;
        $where['weixinStatus'] = 1;

        $data = M()->table('AdminToWeiID')->where($where)->find();

        if($data === false){
            return false;
        }

        return $data;
    }

    /**
     * 取得对应微信ID的基本信息
     * @return bool
     */
    public function getTheWeixinInfo(){

        $this->weixinID = $_SESSION['weixinID'];

        $where['WEIXIN_ID'] = $this->weixinID;

        $data = M()->table('setEventForAdmin')->where($where)->find();

        if(false === $data){
            return false;
        }

        return $data;

    }


}