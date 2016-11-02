<?php
namespace APP\Controller;
use Think\Controller;

include 'WechatCallbackapiController.class.php';

class IndexController extends Controller {

    public function index(){

        //获得传入的weixinID的Get值
        $weixinID = intval(I('get.weixinID'));

        //取得该weixinID的信息
        $weixinInfo = D('Common')->getAdminInfoByWeixinID($weixinID);

        //如果没有对应的信息则输出信息并终止
        if(!$weixinInfo) die('无该信息');

        //获得关于微信相关的Get值获取(微信示例代码)
        $weixinGetArr['echostr'] = $_GET['echostr'];
        $weixinGetArr['signature'] = $_GET['signature'];
        $weixinGetArr['timestamp'] = $_GET['timestamp'];
        $weixinGetArr['nonce'] = $_GET['nonce'];
        $weixinGetArr['msg_signature'] = $_GET['msg_signature'];
        $weixinGetArr['encrypt_type'] = $_GET['encrypt_type'];
        $weixinGetArr['encrypt_type'] = $_GET['msg_signature'];

        //获得微信的GLOBALS信息(微信示例代码)
        $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];

        //调用微信验证类
        $wechatObj = new WechatCallbackapi($weixinInfo,$weixinGetArr,$postStr);
        if (!isset($_GET['echostr'])) {
            $wechatObj->responseMsg();
        }else{
            $wechatObj->valid();
        }
    }
}