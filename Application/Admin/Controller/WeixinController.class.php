<?php
namespace Admin\Controller;
use Admin\Controller;
use APP\Model\ToolModel;

class WeixinController extends CommonController {

    private $userName,$userPwd;
    private $flag;

    public function doAction(){
        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if(isset($action) && ('' != $action)){

            //用于普通登录
            switch ($action){
                case 'getToken':
                    $this->getToken();
                    break;
                case 'weixinIDAddNew':
                    $this->weixinIDAddNew();
                    break;
                case 'weixinIDAddNewData':
                    $this->weixinIDAddNewData();
                    break;
                default:
                    break;

            }
        }
    }

    private function weixinIDAddNewData(){

        //数据微信公众号信息取得
//        $data['username'] = $this->userName;
//        $data['weixinName'] = addslashes($_REQUEST['weixinName']);
//        $data['weixinType'] = addslashes($_REQUEST['weixinType']);
//        $data['weixinUrl'] = addslashes($_REQUEST['weixinUrl']);
//        $data['weixinToken'] = addslashes($_REQUEST['weixinToken']);
//        $data['weixinAppId'] = addslashes($_REQUEST['weixinAppId']);
//        $data['weixinAppSecret'] = addslashes($_REQUEST['weixinAppSecret']);
//        $data['weixinCode'] = addslashes($_REQUEST['weixinCode']);
//        $data['weixinOldID'] = addslashes($_REQUEST['weixinOldID']);
//
//        $weixin_id = addslashes($_REQUEST['weixin_id']);
//
//        $data{'weixinInsertTime'}  = date("Y-m-d H:i:s",time());
//        $data['weixinStatus'] = 1;

        //新增
        if(!isset($_POST['weixin_id'])){

//            if(D('Weixin')->addNewWeixinIDInfo()){
//                //将图片信息保存
//                //分上传图片和未上传图片使用默认图片两种情况
//
//
//                //图片上传
//                //设置删除图片的相关配置项
//                \CommonToolModel::doUploadImg(FOLDER_NAME_ADMIN_WEIXIN);
//
//                foreach ($ret as $name){
//                    $imgPathArr[] = $name['imgPath'];
//                }
//                //5.4版本后,加JSON_UNESCAPED_SLASHES可以取消自动转义
//                $imgPathJson = json_encode($imgPathArr,JSON_UNESCAPED_SLASHES);
//
//                dump($imgPathJson);exit;
//
//                $updateSql = "update adminToWeiID set weixinUrl = '$newWeixinUrl',weixinQRCodeUrl = '$QRUrl', weixinHeadUrl = '$headUrl',
//                    weixinEditTime = '$nowTime' where id = $thisID";
//                $resultErrorNo = SaeRunSql($updateSql);
//
//                if($resultErrorNo == 0){
//                    $msg = "提交成功！1秒后跳转到主页面";
//                    echo "<script>setTimeout(function(){window.parent.location='../login/main.php';},1000);  </script>";
//
//                    //echo $msg.$updateSql;
//                    //exit;
//                }else{
//                    //处理失败的情况下，将原先插入的数据删除
//                    $deleteSql = "delete from adminToWeiID where id = $thisID";
//                    SaeRunSql($deleteSql);
//                    $msg = "提交失败！";
//                    //echo $msg.$updateSql;
//                    //exit;
//                }
//
//            }
//
//
//            if($resultErrorNo == 0)
//            {
//
//            }else{
//                $msg = "追加新公众号失败！";
//            }
        }else{

            //图片上传
            //设置删除图片的相关配置项
            $retArrs =   D('CommonAdmin')->doAdminUploadImg(FOLDER_NAME_ADMIN_WEIXIN.'/'.$_SESSION['weixinID']);

            $uploadCount = count($retArrs);

            switch ($uploadCount){
                case 0:
                    $QRUrl = I('post.hidden_QR_Img');
                    $headUrl = I('post.hidden_Head_Img');
                    break;
                case 1:
                    foreach ($retArrs as $key => $retArr) {
                        if ($key == 'up_img') {
                            $QRUrl = IMG_NET_PATH.$retArr['imgPath'];
                            $headUrl = I('post.hidden_Head_Img');
                        } else {
                            $QRUrl = I('post.hidden_QR_Img');
                            $headUrl = IMG_NET_PATH.$retArr['imgPath'];
                        }
                    }
                    break;
                case 2:
                    $QRUrl = IMG_NET_PATH.$retArrs['up_img']['imgPath'];
                    $headUrl = IMG_NET_PATH.$retArrs['up_imgMin']['imgPath'];
                    break;
            }

            //进行更新数据库
            $ret = D('Weixin')->updateWeixinIDInfo($QRUrl,$headUrl);

            if($ret){
                $msg = "更新成功！";
            }else{
                $msg = "更新失败！";
            }
        }
        echoInfo($msg);
        exit;
    }

    /**
     * 点击重新生成Token
     */
    private function getToken(){
        $newToken =  getToken();
        if($newToken){
            ToolModel::jsonReturn(JSON_SUCCESS,$newToken);
        }else{
            ToolModel::jsonReturn(JSON_ERROR,'未能生成新的Token');
        }

    }

    /**
     * 显示公众号信息编辑画面
     */
    private function weixinIDAddNew(){
        
        $nowTime  = date("Y-m-d H:i:s",time());
        
        $action=addslashes($_GET["action"]);
        
//        if($action == "delete"){
//            $deleteSql = "update adminToWeiID set weixinEditTime = '$nowTime',weixinStatus = 0 where id=$weixinID";
//            $errono = SaeRunSql($deleteSql);
//            if($errono == 0){
//                $msg = "删除成功！";
//            }else{
//                $msg = "删除失败！";
//            }
//            echoInfo($msg);
//            exit;
//        }

        $weixinInfo = D('Weixin')->getTheWeixinNameInfo();

        $this->assign('weixinInfo',$weixinInfo);

       $this->display('Weixin/weixinIDAddNew');

    }

    /**
     * 原来登录界面
     */
    private function login(){
        $this->display('Login/login');
    }

    private function checkData(){
        //检查用户名
        $this->userName = I('post.userName','');
        if( ('' == strval($this->userName)) ){
            return '用户名不能为空';
        }

        if( (ToolModel::getStrLen(strval($this->userName))) > 20 ){
            return '用户名不能大于20位';
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



    /**************************新登录界面用于区长与书记建言献策用**************************/
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
        $ret = $this->checkData2();
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

    private function checkData2(){
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
    /**************************新登录界面用于区长与书记建言献策用**************************/
}