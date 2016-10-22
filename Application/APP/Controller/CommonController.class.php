<?php
/**
 * Created by wujiayu.
 * User: Administrator
 * Date: 2016/5/19
 * Time: 13:43
 */

namespace APP\Controller;
use Think\Controller;
use APP\Model\VipModel;


header("Content-type:text/html;charset=utf-8");

class CommonController extends Controller{

    private $VipModel;

    /**
     * 构造方法
     */
    public function __construct(){

        //使用__construct方法时，需先调用父类的__construct方法先
        parent::__construct();

        //只能使用微信内置浏览器进行访问 20160123
        if(!is_weixin()){
            echoWarning('功能只能在微信内置浏览器进行访问噢');exit;
        }

        //判断是否存在openid和weixinID的session值，没有则重新尝试获取
        if(!isset($_SESSION['openid']) || !isset($_SESSION['weixinID'])){

            //判断传入的参数openid和weixinID的长度正确性
            isOpenIDWeixinIDOK($_GET['openid'],$_GET['weixinID']);

            $_SESSION['openid']  = base64_decode($_GET['openid']);
            $_SESSION['weixinID'] = intval(base64_decode($_GET['weixinID']));

        }

        //如果当前的Controller是在需要判断会员的列表中，则进行是否会员判断，如果不是则显示会员绑定按钮
        if( in_array(CONTROLLER_NAME,C('IS_VIP_ACTION_ARR')) ){

            //如果不存在或者为空则创建，否则直接使用
            if(!isset($this->VipModel) || ('' == $this->VipModel) ){
                $this->VipModel = new VipModel();
            }

            //判断是否是会员
            if( !($this->VipModel->isVip()) ){

                echoWarning('必须是会员才能使用该功能，请先注册为会员！');

                linkToVipBD();

                exit;
            }

            //将会员信息存入session
            if(!isset($_SESSION['vipInfo']) || ('' == $_SESSION['vipInfo'])){
                $_SESSION['vipInfo'] = $this->VipModel->vipInfo();
            }

            //取得该公众号的基础信息，写入session
            $_SESSION['config'] = D('Common')->getCon();


        }

    }

    /**
     * 空方法
     * 如果没有session或者session为0，没有对应的方法名的情况下默认进去登录页面，
     * 如果有session，没有对应的方法名的情况下默认进去后台主页
     *
     */
    public function _empty(){
        echoWarning('没有对应的方法，请确认！');exit;
    }
}