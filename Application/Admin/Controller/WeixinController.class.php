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

            if(D('Weixin')->addNewWeixinIDInfo()){
                //将图片信息保存
                //分上传图片和未上传图片使用默认图片两种情况

                //1.上传图片 TbC

            }


            if($resultErrorNo == 0)
            {

                $selectIDSql = "select MAX(id) from AdminToWeiID where weixinStatus = 1";
                $thisID = getVarBySql($selectIDSql);
                //$newWeixinUrl = "http://1.zglqxwwtest.sinaapp.com/?weixinID=".$thisID;
                $newWeixinUrl = 'http://'.$_SERVER['HTTP_HOST'].'/?weixinID='.$thisID;

                $domain = "weixincourse";
                $filename = 'up_img';
                $files = $_FILES[$filename];
                $fileSize = $files['size'];
                if ($fileSize > 0){
                    $name= '/weixin/QRImg-'.$thisID.'.jpg';
                    $form_data =$files['tmp_name'];
                    $s2 = new SaeStorage();
                    $img = new SaeImage();
                    $img_data = file_get_contents($form_data);//获取本地上传的图片数据
                    $img->setData($img_data);
                    //$img->resize(180,180); //图片缩放为180*180
                    $img->improve();       //提高图片质量的函数
                    $new_data = $img->exec(); // 执行处理并返回处理后的二进制数据
                    $s2->write($domain,$name,$new_data);//将public修改为自己的storage 名称
                    $QRUrl= $s2->getUrl($domain,$name);//将public修改为自己的storage 名称echo "文件名：".$name."<br/>"
                }else{
                    $QRUrl = "url error";
                }
                if($QRUrl == "url error"){
                    $QRUrl = "img/default_QR.png";
                }

                $filename = 'up_imgMin';
                $files = $_FILES[$filename];
                $fileSize = $files['size'];
                if ($fileSize > 0){
                    $name= '/weixin/HeadImg-'.$thisID.'.jpg';
                    $form_data =$files['tmp_name'];
                    $s2 = new SaeStorage();
                    $img = new SaeImage();
                    $img_data = file_get_contents($form_data);//获取本地上传的图片数据
                    $img->setData($img_data);
                    //$img->resize(180,180); //图片缩放为180*180
                    $img->improve();       //提高图片质量的函数
                    $new_data = $img->exec(); // 执行处理并返回处理后的二进制数据
                    $s2->write($domain,$name,$new_data);//将public修改为自己的storage 名称
                    $headUrl= $s2->getUrl($domain,$name);//将public修改为自己的storage 名称echo "文件名：".$name."<br/>"
                }else{
                    $headUrl = "url error";
                }
                if($headUrl == "url error"){
                    $headUrl = "img/default_head.png";
                }

                $updateSql = "update AdminToWeiID set weixinUrl = '$newWeixinUrl',weixinQRCodeUrl = '$QRUrl', weixinHeadUrl = '$headUrl',
                    weixinEditTime = '$nowTime' where id = $thisID";
                $resultErrorNo = SaeRunSql($updateSql);

                if($resultErrorNo == 0){
                    $msg = "提交成功！1秒后跳转到主页面";
                    echo "<script>setTimeout(function(){window.parent.location='../login/main.php';},1000);  </script>";

                    //echo $msg.$updateSql;
                    //exit;
                }else{
                    //处理失败的情况下，将原先插入的数据删除
                    $deleteSql = "delete from AdminToWeiID where id = $thisID";
                    SaeRunSql($deleteSql);
                    $msg = "提交失败！";
                    //echo $msg.$updateSql;
                    //exit;
                }

            }else{
                $msg = "追加新公众号失败！";
            }
        }else{

            $domain = 'weixincourse';
            $filename = 'up_img';
            $files = $_FILES[$filename];
            $fileSize = $files['size'];
            if ($fileSize > 0){
                $name= '/weixin/QRImg-'.$weixin_id.'-'.time().'.jpg';
                $form_data =$files['tmp_name'];
                $s2 = new SaeStorage();
                $img = new SaeImage();
                $img_data = file_get_contents($form_data);//获取本地上传的图片数据
                $img->setData($img_data);
                //$img->resize(180,180); //图片缩放为180*180
                $img->improve();       //提高图片质量的函数
                $new_data = $img->exec(); // 执行处理并返回处理后的二进制数据
                $s2->write($domain,$name,$new_data);//将public修改为自己的storage 名称
                $QRUrl= $s2->getUrl($domain,$name);//将public修改为自己的storage 名称echo "文件名：".$name."<br/>"
            }else{
                $QRUrl = "url error";
            }

            $filename = 'up_imgMin';
            $files = $_FILES[$filename];
            $fileSize = $files['size'];
            if ($fileSize > 0){
                $name= '/weixin/HeadImg-'.$weixin_id.'-'.time().'.jpg';
                $form_data =$files['tmp_name'];
                $s2 = new SaeStorage();
                //if(fileExists($domain, $name)){
                $r = $s2->delete($domain,$name);
                //}
                $img = new SaeImage();
                $img_data = file_get_contents($form_data);//获取本地上传的图片数据
                $img->setData($img_data);
                //$img->resize(180,180); //图片缩放为180*180
                $img->improve();       //提高图片质量的函数
                $new_data = $img->exec(); // 执行处理并返回处理后的二进制数据
                $s2->write($domain,$name,$new_data);//将public修改为自己的storage 名称
                $headUrl= $s2->getUrl($domain,$name);//将public修改为自己的storage 名称echo "文件名：".$name."<br/>"
            }else{
                $headUrl = "url error";
            }

            if( ($QRUrl == "url error") &&  ($headUrl == "url error") ){
                $sql = "update AdminToWeiID set username = '$user',weixinName = '$weixinName',weixinType = '$weixinType',weixinToken = '$weixinToken',
            weixinAppId = '$weixinAppId',weixinAppSecret = '$weixinAppSecret',weixinCode = '$weixinCode',weixinOldID = '$weixinOldID',
            weixinEditTime = '$nowTime' where id = $weixin_id";
            }else if(($QRUrl != "url error") &&  ($headUrl == "url error")){
                $sql = "update AdminToWeiID set username = '$user',weixinName = '$weixinName',weixinType = '$weixinType',weixinToken = '$weixinToken',
            weixinAppId = '$weixinAppId',weixinAppSecret = '$weixinAppSecret',weixinCode = '$weixinCode',weixinOldID = '$weixinOldID',
            weixinQRCodeUrl = '$QRUrl',weixinEditTime = '$nowTime' where id = $weixin_id";
            }else if(($QRUrl == "url error") &&  ($headUrl != "url error")){
                $sql = "update AdminToWeiID set username = '$user',weixinName = '$weixinName',weixinType = '$weixinType',weixinToken = '$weixinToken',
            weixinAppId = '$weixinAppId',weixinAppSecret = '$weixinAppSecret',weixinCode = '$weixinCode',weixinOldID = '$weixinOldID',
            weixinHeadUrl = '$headUrl',weixinEditTime = '$nowTime' where id = $weixin_id";
            }else{
                $sql = "update AdminToWeiID set username = '$user',weixinName = '$weixinName',weixinType = '$weixinType',weixinToken = '$weixinToken',
            weixinAppId = '$weixinAppId',weixinAppSecret = '$weixinAppSecret',weixinCode = '$weixinCode',weixinOldID = '$weixinOldID',
            weixinQRCodeUrl = '$QRUrl',weixinHeadUrl = '$headUrl' weixinEditTime = '$nowTime' where id = $weixin_id";
            }
            $resultErrorNo = SaeRunSql($sql);
            if($resultErrorNo == 0){
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
//            $deleteSql = "update AdminToWeiID set weixinEditTime = '$nowTime',weixinStatus = 0 where id=$weixinID";
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