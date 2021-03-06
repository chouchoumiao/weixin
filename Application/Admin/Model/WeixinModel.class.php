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
        $this->weixinID = $_SESSION['weixinID'];
    }


    /**
     * 根据提交的表单进行基础设置
     * @return bool
     */
    public function updateConfig(){

        //来之签到设置画面提交的数据
        if(isset($_POST['thisIntegral']) && ('' != I('post.thisIntegral'))){
            $data['CONFIG_INTEGRALSETDAILY'] = I('post.thisIntegral');
        }

        //来之签到设置画面提交的数据
        if(isset($_POST['dailyCodeIntegral']) && ('' != I('post.dailyCodeIntegral'))){
            $data['CONFIG_DAILYPLUS'] = I('post.dailyCodeIntegral');
        }

        //来之初始化设置画面提交的数据
        if(isset($_POST['integralNewInsert']) && ('' != I('post.integralNewInsert'))){
            $data['CONFIG_INTEGRALINSERT'] = I('post.integralNewInsert');
        }

        //来之初始化设置画面提交的数据
        if(isset($_POST['integralReferrerForNewVip']) && ('' != I('post.integralReferrerForNewVip'))){
            $data['CONFIG_INTEGRAL_REFERRER_FOR_NEW_VIP'] = I('post.integralReferrerForNewVip');
        }

        //来之初始化设置画面提交的数据
        if(isset($_POST['integralReferrer']) && ('' != I('post.integralReferrer'))){
            $data['CONFIG_INTEGRALREFERRER'] = I('post.integralReferrer');
        }

        $where['WEIXIN_ID'] = $this->weixinID;

        $ret = M()->table('configSet')->where($where)->save($data);

        if($ret>0){
            return true;
        }

        return false;


        }
    /**
     * 追加事件设置LIST
     * @return bool
     */
    public function setEventList(){

        $data['WEIXIN_ID'] = $this->weixinID;
        $data['eventNameList'] = I('post.eventNameList');
        $data['eventUrlList'] = I('post.eventBackUrlList');
        $data['eventForwardUrlList'] = I('post.eventForwardUrlList');
        $data['editDateTime'] = date('Y-m-d H:i:s',time());

        $ret = M()->table('setEventForAdmin')->add($data);

        if($ret > 0){
            return true;
        }

        return false;
    }

    /**
     * 删除指定的公众号信息
     * @return bool
     */
    public function delWeixinInfo(){

        $where['id'] = I('get.WeiID');

        $ret = M()->table('adminToWeiID')->where($where)->delete();

        if($ret == 1){
            return true;
        }

        return false;


    }

    /**
     * 获取当前公众号设置的自定义菜单信息
     * @return bool|mixed
     */
    public function getMenuInfo(){
        $where['WEIXIN_ID'] = $this->weixinID;

        $data = M('')->table('menuInfo')->where($where)->find($data);

        if( false === $data){
            return false;
        }
        return $data;
    }

    /**
     * 根据传入的数据追加新自定义菜单信息
     * @param $data
     * @return bool
     */
    public function addMenuInfo($data){
        $data['WEIXIN_ID'] = $this->weixinID;

        $data['menu_insertTime'] = date("Y/m/d H:i:s",time());

        $ret = M('')->table('menuInfo')->add($data);

        if($ret >= 1){
            return true;
        }
        return false;
    }

    /**
     * 删除当前公众号的自定义菜单信息
     * @return bool
     */
    public function delMenuInfo(){
        $where['WEIXIN_ID'] = $this->weixinID;

        $ret = M('')->table('menuInfo')->where($where)->delete();

        if($ret >= 1){
            return true;
        }
        return false;

    }

    /**
     * 取得当前登录用户可以管理的微信公众号一览表
     * @return bool
     */
    public function getWeixinInfoByNowUser(){

        $where['username'] = $this->userName;
        $where['weixinStatus'] = 1;

        $data = M()->table('adminToWeiID')->where($where)->select();

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

        //判断是不是通过编辑公众号过来的传递，如果是需要编辑对应的公众号
        if( isset($_GET['WeiID']) && ( '' != I('get.WeiID')) ){
            $where['id'] = I('get.WeiID');
        }else{
            $where['id'] = $this->weixinID;
        }


        $where['weixinStatus'] = 1;

        $data = M()->table('adminToWeiID')->where($where)->find();

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

    /**
     * 新增公众号基本信息
     * @param $QRUrl
     * @param $headUrl
     * @return bool
     */
    public function addNewWeixinIDInfo($QRUrl,$headUrl){
        //数据微信公众号信息取得
        $data['username'] = $this->userName;
        $data['weixinName'] = addslashes($_REQUEST['weixinName']);
        $data['weixinType'] = addslashes($_REQUEST['weixinType']);
        $data['weixinUrl'] = addslashes($_REQUEST['weixinUrl']);
        $data['weixinToken'] = addslashes($_REQUEST['weixinToken']);
        $data['weixinAppId'] = addslashes($_REQUEST['weixinAppId']);
        $data['weixinAppSecret'] = addslashes($_REQUEST['weixinAppSecret']);
        $data['weixinCode'] = addslashes($_REQUEST['weixinCode']);
        $data['weixinOldID'] = addslashes($_REQUEST['weixinOldID']);

        $data['weixinQRCodeUrl'] = $QRUrl;
        $data['weixinHeadUrl'] = $headUrl;

        $data{'weixinInsertTime'}  = date("Y-m-d H:i:s",time());
        $data['weixinStatus'] = 1;

        $data = M()->table('adminToWeiID')->add($data);

        if($data >= 1){
            return true;
        }
        return false;
    }


    /**
     * 更新微信公众号的基本信息
     * @param $QRUrl
     * @param $headUrl
     * @return bool
     */
    public function updateWeixinIDInfo($QRUrl,$headUrl){

        $data['username'] = $this->userName;
        $data['weixinName'] = I('post.weixinName');
        $data['weixinType'] = I('post.weixinType');
        $data['weixinToken'] = I('post.weixinToken');
        $data['weixinAppId'] = I('post.weixinAppId');

        $data['weixinAppSecret'] = I('post.weixinAppSecret');
        $data['weixinCode'] = I('post.weixinCode');

        $data['weixinOldID'] = I('post.weixinOldID');
        $data['weixinEditTime'] = date("Y-m-d H:i:s",time());

        $data['weixinQRCodeUrl'] = $QRUrl;
        $data['weixinHeadUrl'] = $headUrl;

        $where['id'] = $this->weixinID;

        $ret = M()->table('adminToWeiID')->where($where)->save($data);

        if($ret == 1){
            return true;
        }
        return false;

    }


}