<?php
/**
 * Created by wujiayu.
 * User: Administrator
 * Date: 2016/5/19
 * Time: 13:43
 */

namespace Admin\Controller;
use Think\Controller;


header("Content-type:text/html;charset=utf-8");

class CommonController extends Controller{

    /**
     * 构造方法
     */
    public function __construct(){

        //使用__construct方法时，需先调用父类的__construct方法先
        parent::__construct();
        
        //判断是否已经登录
//        if( (!isset($_SESSION['username'])) || ('' == $_SESSION['username']) ){
//            if(( 'login' != ACTION_NAME) &&  ('login2' != ACTION_NAME) ){
//
//                //$this->dispaly('Login/login2');
//            }
//        }

    }

    /**
     * 空方法
     * 如果没有session或者session为0，没有对应的方法名的情况下默认进去登录页面，
     * 如果有session，没有对应的方法名的情况下默认进去后台主页
     *
     */
    public function _empty(){
        if( (!isset($_SESSION['username'])) || ('' == $_SESSION['username']) ){
            //无session则进入后台主页面
            $this->redirect('Login/login');
        }else{
            //有session则进入后台主页面
            $this->redirect('Index/index');
        }
    }
}