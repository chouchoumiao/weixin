<?php
namespace Admin\Controller;
use APP\Model\CommonAdminModel;
use APP\Model\CommonModel;
use APP\Model\ToolModel;
use Think\Controller;
class IndexController extends CommonController {
    public function doAction(){
        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if(isset($action) && ('' != $action)){

            //$this->obj = D('Login');
            switch ($action){
                case 'logout':
                    $this->logout();
                    break;
                case 'editPwd':
                    $this->editPwd();
                    break;
                case 'editPwdData':
                    $this->editPwdData();
                    break;

                case 'showMainFrame':
                    $this->showMainFrame();
                    break;
                case 'showIndex':
                    $this->showIndex();
                    break;
                //显示建议的信息画面
                case 'getWeiID':
                    $this->getWeiID();
                    break;
                default:
                    
                    break;
            }
        }
    }

    private function logout(){
        $_SESSION['username'] = null;
        unset($_SESSION['username']);

        $_SESSION['weixinID'] = null;
        unset($_SESSION['weixinID']);

        $_SESSION['adminConfig'] = null;
        unset($_SESSION['adminConfig']);

        $_SESSION['flag'] = null;
        unset($_SESSION['flag']);

        $this->display('Login/login');
    }

    /**
     * 显示修改密码页面
     */
    private function editPwd(){
        $this->display('Index/adminEdit');
    }

    /**
     * 修改密码逻辑
     */
    private function editPwdData(){

//        $user = $_SESSION['username'];
//        $weixinID = $_SESSION['weixinID'];

        if(isset($_SESSION['username'])){

            $action = strval(I('post.action'));

            if($action == "newPassEdit"){

                //更新密码
                if(D('User')->updatePwd()){
                    ToolModel::jsonReturn(JSON_SUCCESS,'更新新密码成功！');
                }else{
                    ToolModel::jsonReturn(JSON_ERROR,'更新新密码失败！');
                }
            }else if($action == "addUserByAdmin"){
                //取得set页面传递过来的数据
//                $addUser = addslashes($_POST["addUser"]);
//
//                $sql = "select * from AdminUser where username='$addUser' and isdeleted = 0";
//                $addUserInfo = getlineBySql($sql);
//                if($addUserInfo['username'] != ""){
//                    $arr['success'] = 0;
//                    $arr['msg'] = "已有该用户名了，请更换";
//                    echo json_encode($arr);
//                    exit;
//
//                }else{
//                    $newPass = addslashes($_POST["newPass"]);
//
//                    $md5NewPass = md5($newPass);
//                    //$logintime = mktime();
//                    $logintime  = date("Y-m-d H:i:s",time());
//                    $ip = GetIP();
//
//                    $sql = "insert into AdminUser (username,password,loginTime,login_counts,login_ip,isdeleted) values ('$addUser','$md5NewPass','$logintime',0,'$ip',0)";
//
//                    $errono = SaeRunSql($sql);
//                    if($errono == 0){
//                        $arr['success'] = 1;
//                        $arr['msg'] = "新用户追加成功！";
//                    }else{
//                        $arr['success'] = 0;
//                        $arr['msg'] = "新用户追加失败！";
//                    }
//                }
            }
        }else{
            ToolModel::jsonReturn(JSON_ERROR,'session出错，请重新登录！');
        }
    }


    private function showMainFrame(){
        $this->display('Index/main');
    }

    /**
     * 点击切换时进行操作
     */
    private function getWeiID(){

        $weixinID = intval(I('post.weixinID'));

        if(isset($weixinID)){
            $_SESSION['weixinID'] = $weixinID;
            ToolModel::jsonReturn(JSON_SUCCESS,'');
        }else{
            ToolModel::jsonReturn(JSON_ERROR,'参数错误,请重新登录');
        }
    }


    private function showIndex(){

        //取得当前登录用户可以管理的微信一览信息


        $this->getNowUserWeiInfo();

        
        $this->display('Index/index');
    }

    /**
     * 取得当前登录用户可以操作的微信公众号所有信息
     */
    private function getNowUserWeiInfo(){

        //得到当前用户的用户名
        if(!isset($_SESSION['username'])){
            echo '无当前用户信息请重新登录';
            exit;
        }

        $data = D('Weixin')->getWeixinInfoByNowUser();

        if(!$data){
            echo "当前未设置过公众号，请添加公众号信息！";
        }else{
            if(!isset($_SESSION['weixinID'])){
                $thisWeixinID = $data[0]['id'];
                $_SESSION['weixinID'] = $thisWeixinID;
            }
            
            //取得当前微信号的基本信息
            $weixinInfo = D('Weixin')->getTheWeixinInfo();

            if($weixinInfo['eventNameList']){

                $eventNameArr = explode(",",$weixinInfo['eventNameList']);
                $eventUrlArr = explode(",",$weixinInfo['eventUrlList']);

                $this->assign('eventNameArr',$eventNameArr);
                $this->assign('eventUrlArr',$eventUrlArr);
            }

        }

        //session中追加 用于计算升序降序的计数 0：升序 1：降序，默认为0
//        $_SESSION['ASCDESCCUNT'] = 0;
        
        $config = D('CommonAdmin')->getAdminCon($thisWeixinID);
        $this->assign('weixinName',$config['CONFIG_VIP_NAME']);

        $this->assign('data',$data);


        if($weixinInfo){
            $this->assign('weixinInfo',$weixinInfo);
        }

        //获取当前微信公众号的信息
        $thisWeixinInfo = D('Weixin')->getTheWeixinNameInfo();
        $this->assign('thisWeixinName',$thisWeixinInfo['weixinName']);

    }

}