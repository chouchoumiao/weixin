<?php
namespace Admin\Controller;
use Admin\Controller;
use APP\Model\ToolModel;

class LoginController extends CommonController {

    private $userName,$userPwd;
    private $flag;

    public function doLogin(){
        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if(isset($action) && ('' != $action)){

            //$this->obj = D('Login');
            switch ($action){
                case 'login2':
                    $this->login2();
                    break;
                case 'login2Data':
                    $this->login2Data();
                    break;
                case 'logout2':
                    $this->logout2();
                    break;

            }
        }


    }

    //登出
    private function logout2(){
        $_SESSION['username2'] = null;
        unset($_SESSION['username2']);

        $_SESSION['weixinID'] = null;
        unset($_SESSION['weixinID']);

        $_SESSION['flag'] = null;
        unset($_SESSION['flag']);
        $this->display('Login/login2');
    }

    /**
     * 新登录是验证用户名 密码正确性
     */
    private function login2Data(){

        if(!isset($_POST)){
            ToolModel::jsonReturn(JSON_ERROR,'参数错误');
            exit;
        }

        //验证数据正确性
        $ret = $this->checkData();
        if( 1 != $ret){
            ToolModel::jsonReturn(JSON_ERROR,$ret);
            exit;
        }


        if(strval($this->userName) == 'quzhang'){
            $_SESSION['flag'] = QUZHANG;
        }else{
            $_SESSION['flag'] = SHUJI;
        }

        //验证正确性
        $data = D('Login')->checkLogin($this->userName,$this->userPwd);
        if(!$data){
            ToolModel::jsonReturn(JSON_ERROR,'用户名或密码错误');
            exit;
        }

        //将用户名写入session
        $_SESSION['username2'] = $data['username'];

        $_SESSION['weixinID'] = 69; //设置为路桥发布,需改进

        ToolModel::jsonReturn(JSON_SUCCESS,'');


    }
   
    /**
     * 新作登录界面
     */
    private function login2(){
        $this->display('Login/login2');
    }

    private function checkData(){
        //检查用户名
        $this->userName = I('post.userName','');
        if( ('' == strval($this->userName)) ){
            return '用户名不能为空';
        }

        if( 'quzhang' != strval($this->userName) && ('shuji' != strval($this->userName))){
            return '用户名错误';
        }

        if( (ToolModel::getStrLen(strval($this->userName))) > 8 ){
            return '用户名不能大于8位';
        }

        $this->userPwd= I('post.pwd','');
        if( ('' == strval($this->userPwd)) ){
            return '密码不能为空';
        }

        if(ToolModel::getStrLen(strval($this->userPwd)) < 6){
            return '密码不能小于6位';
        }

        return 1;
    }
}