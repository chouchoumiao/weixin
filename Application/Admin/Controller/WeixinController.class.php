<?php
namespace Admin\Controller;
use Admin\Controller;
use APP\Model\ToolModel;

class WeixinController extends CommonController {

    private $userName,$userPwd;
    private $APPID,$APPSECRET;

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
                //自定义菜单
                case 'menuSet':
                    $this->menuSet();
                    break;
                case 'menuSetData':
                    $this->menuSetData();
                    break;
                default:
                    break;

            }
        }
    }

    //设置自定义菜单提交时操作
    private function menuSetData(){

        //取得当前公众号的信息
        $weixinInfo = D('Weixin')->getTheWeixinNameInfo();

        $this->APPID = $weixinInfo['weixinAppId'];
        $this->APPSECRET = $weixinInfo['weixinAppSecret'];

        if(($this->APPID == "") || ($this->APPSECRET == "")){
            $msg = "请先设置该公众号的appID和appSecret";
            echoInfo($msg);
            exit;
        }


        //小按钮分类（最大五个） 属于按钮分类1
        $arrSunBtnNameForBtn1 = array();
        $arrSunBtnTypeForBtn1 = array();
        $arrSunBtnContentForBtn1 = array();

        //小按钮分类（最大五个） 属于按钮分类2
        $arrSunBtnNameForBtn2 = array();
        $arrSunBtnTypeForBtn2 = array();
        $arrSunBtnContentForBtn2 = array();

        //小按钮分类（最大五个） 属于按钮分类3
        $arrSunBtnNameForBtn3 = array();
        $arrSunBtnTypeForBtn3 = array();
        $arrSunBtnContentForBtn3 = array();

        //获取大按钮分类名称，种类，内容
        $arrBtnName1 = I('post.titleName1','');
        $arrBtnType1 = I('post.menutype1','');
        if($arrBtnType1 == "view"){
            $arrBtnContent1 = I('post.linkName1','');
        }else if($arrBtnType1 == "click"){
            $arrBtnContent1 = I('post.clickName1','');
        }else{
            $arrBtnContent1 = '';
        }

        $arrBtnName2 = I('post.titleName2','');
        $arrBtnType2 = I('post.menutype2','');

        if($arrBtnType2 == "view"){

            $arrBtnContent2 = I('post.linkName2','');
        }else if($arrBtnType2 == "click"){
            $arrBtnContent2 = I('post.clickName2','');
        }else{
            $arrBtnContent2 = '';
        }

        $arrBtnName3 = I('post.titleName3','');
        $arrBtnType3 = I('post.menutype3','');


        if($arrBtnType3 == "view"){
            $arrBtnContent3 = I('post.linkName3','');
        }else if($arrBtnType3 == "click"){
            $arrBtnContent3 = I('post.clickName3','');
        }else{
            $arrBtnContent3 = '';
        }

        //获取各个大按钮分类的小分类的 名称，种类，内容
        for($j = 1; $j<=15; $j++){
            if((addslashes($_REQUEST['subTitleName'.$j])) && (addslashes($_REQUEST['subMenutype'.$j]))){
                if($j>=1 && $j <=5){
                    $arrSunBtnNameForBtn1[] = addslashes($_REQUEST['subTitleName'.$j]);
                    $arrSunBtnTypeForBtn1[] = addslashes($_REQUEST['subMenutype'.$j]);
                    if(addslashes($_REQUEST['subMenutype'.$j]) == "view"){
                        $arrSunBtnContentForBtn1[] = addslashes($_REQUEST['subLinkName'.$j]);
                    }else if(addslashes($_REQUEST['subMenutype'.$j]) == "click"){
                        $arrSunBtnContentForBtn1[] = addslashes($_REQUEST['subClickName'.$j]);
                    }
                }else if($j>=6 && $j <=10){
                    $arrSunBtnNameForBtn2[] = addslashes($_REQUEST['subTitleName'.$j]);
                    $arrSunBtnTypeForBtn2[] = addslashes($_REQUEST['subMenutype'.$j]);
                    if(addslashes($_REQUEST['subMenutype'.$j]) == "view"){
                        $arrSunBtnContentForBtn2[] = addslashes($_REQUEST['subLinkName'.$j]);
                    }else if(addslashes($_REQUEST['subMenutype'.$j]) == "click"){
                        $arrSunBtnContentForBtn2[] = addslashes($_REQUEST['subClickName'.$j]);
                    }
                }else{
                    $arrSunBtnNameForBtn3[] = addslashes($_REQUEST['subTitleName'.$j]);
                    $arrSunBtnTypeForBtn3[] = addslashes($_REQUEST['subMenutype'.$j]);
                    if(addslashes($_REQUEST['subMenutype'.$j]) == "view"){
                        $arrSunBtnContentForBtn3[] = addslashes($_REQUEST['subLinkName'.$j]);
                    }else if(addslashes($_REQUEST['subMenutype'.$j]) == "click"){
                        $arrSunBtnContentForBtn3[] = addslashes($_REQUEST['subClickName'.$j]);
                    }
                }
            }else if((addslashes($_REQUEST['subTitleName'.$j]) == "") && (addslashes($_REQUEST['subMenutype'.$j]) == "")){
            }else{
                $msg = "设置第".$j."个分按钮时错误，分按钮的名称和内容必须同时设置".addslashes($_REQUEST['subTitleName'.$j]).addslashes($_REQUEST['subMenutype'.$j]);
                echoInfo($msg);
                exit;
            }
        }

        if(!$arrBtnName1 && !$arrBtnName2 && !$arrBtnName3){
            $msg = "请设置主按钮的内容";
            echoInfo($msg);
            exit;
        }else{
            if($arrBtnName1){
                if(count($arrSunBtnNameForBtn1) == 0){
                    if(!$arrBtnType1){
                        $msg = "第一个主按钮设置错误(主按，分按钮都没有设置完)";
                        echoInfo($msg);
                        exit;
                    }
                }else{
                    if($arrBtnType1){
                        $msg = "第一个主按钮设置错误(设置分按钮时，请将主按钮的链接去除，只留下名称)";
                        echoInfo($msg);
                        exit;
                    }
                }
            }else{
                if($arrBtnType1){
                    $msg = "第一个主按钮设置错误(有分按的情况下，需设置主按钮名称)";
                    echoInfo($msg);
                    exit;
                }
            }
            if($arrBtnName2){
                if(count($arrSunBtnNameForBtn2) == 0){
                    if(!$arrBtnType2){
                        $msg = "第二个主按钮设置错误(主按，分按钮都没有设置完)";
                        echoInfo($msg);
                        exit;
                    }
                }else{
                    if($arrBtnType2){
                        $msg = "第二个主按钮设置错误(设置分按钮时，请将主按钮的链接去除，只留下名称)";
                        echoInfo($msg);
                        exit;
                    }
                }
            }else{
                if($arrBtnType2){
                    $msg = "第二个主按钮设置错误(有分按的情况下，需设置主按钮名称)";
                    echoInfo($msg);
                    exit;
                }
            }
            if($arrBtnName3){
                if(count($arrSunBtnNameForBtn3) == 0){
                    if(!$arrBtnType3){
                        $msg = "第三个主按钮设置错误(主按，分按钮都没有设置完)";
                        echoInfo($msg);
                        exit;
                    }
                }else{
                    if($arrBtnType3){
                        $msg = "第三个主按钮设置错误(设置分按钮时，请将主按钮的链接去除，只留下名称)";
                        echoInfo($msg);
                        exit;
                    }
                }
            }else{
                if($arrBtnType3){
                    $msg = "第三个主按钮设置错误(有分按的情况下，需设置主按钮名称)";
                    echoInfo($msg);
                    exit;
                }
            }
        }

        $arrSunBtnNameJson1 = json_encode($arrSunBtnNameForBtn1);
        $arrSunBtnTypeJson1 = json_encode($arrSunBtnTypeForBtn1);
        $arrSunBtnContentJson1 = json_encode($arrSunBtnContentForBtn1);

        $arrSunBtnNameJson2 = json_encode($arrSunBtnNameForBtn2);
        $arrSunBtnTypeJson2 = json_encode($arrSunBtnTypeForBtn2);
        $arrSunBtnContentJson2 = json_encode($arrSunBtnContentForBtn2);

        $arrSunBtnNameJson3 = json_encode($arrSunBtnNameForBtn3);
        $arrSunBtnTypeJson3 = json_encode($arrSunBtnTypeForBtn3);
        $arrSunBtnContentJson3 = json_encode($arrSunBtnContentForBtn3);

        //先判断是否存在该公众号的自定义菜单，如果有再删除

        $data = D('Weixin')->getMenuInfo();

        if($data){
            $isdelete = D('Weixin')->delMenuInfo();

            if( !$isdelete){
                $msg = "原先存在自定义菜单，删除原先菜单失败，请重试！";
                echoInfo($msg);
                exit;
            }
        }

        $menu['menu_name1'] = $arrBtnName1;
        $menu['menu_msgType1'] = $arrBtnType1;
        $menu['menu_content1'] = $arrBtnContent1;

        $menu['menu_name2'] = $arrBtnName2;
        $menu['menu_msgType2'] = $arrBtnType2;
        $menu['menu_content2'] = $arrBtnContent2;

        $menu['menu_name3'] = $arrBtnName3;
        $menu['menu_msgType3'] = $arrBtnType3;
        $menu['menu_content3'] = $arrBtnContent3;

        $menu['menu_subNameForBtn1'] = $arrSunBtnNameJson1;
        $menu['menu_subMsgTypeForBtn1'] = $arrSunBtnTypeJson1;
        $menu['menu_subContentForBtn1'] = $arrSunBtnContentJson1;

        $menu['menu_subNameForBtn2'] = $arrSunBtnNameJson2;
        $menu['menu_subMsgTypeForBtn2'] = $arrSunBtnTypeJson2;
        $menu['menu_subContentForBtn2'] = $arrSunBtnContentJson2;

        $menu['menu_subNameForBtn3'] = $arrSunBtnNameJson3;
        $menu['menu_subMsgTypeForBtn3'] = $arrSunBtnTypeJson3;
        $menu['menu_subContentForBtn3'] = $arrSunBtnContentJson3;

        $addRet = D('Weixin')->addMenuInfo($menu);

        if(!$addRet)
        {
            $msg = "数据库新增数据失败！";
        }else{

            $menuCode = $this->activeMenuInfo();

            if($menuCode->errcode == 0){
                $msg = "菜单创建成功";
            }else{
                $msg = "菜单创建失败".$menuCode->errmsg;
                $errorInfo = D('CommonAdmin')->getSetedError();
                if($errorInfo){
                    $errorInfoCount = count($errorInfo);
                    for($i = 0;$i<$errorInfoCount;$i++){
                        if($menuCode->errcode == $errorInfo[$i]['errorCode']){
                            $msg = "菜单创建失败,原因：".$errorInfo[$i]['errorMsg'];
                            break;
                        }
                    }
                }


            }
        }
        echoInfo($msg);
        exit;
    }

    //显示自定义菜单页面
    private function menuSet(){

        $data = D('Event')->getNowReplyInfo();

        if($data){
            $this->assign('data',$data);
        }

        $this->display('Weixin/menuSet');
    }

    /**
     * 设置公众号
     */
    private function weixinIDAddNewData(){

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

        //新增
        if(!isset($_POST['weixin_id'])){
            $ret = D('Weixin')->addNewWeixinIDInfo($QRUrl,$headUrl);

        }else{
            //进行更新数据库
            $ret = D('Weixin')->updateWeixinIDInfo($QRUrl,$headUrl);
        }

        if($ret){
            $msg = "操作成功！";
        }else{
            $msg = "操作失败！";
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

        //如果QRCodeUrl和HeadUrl为空的话则设置为默认值
        if($weixinInfo['weixinQRCodeUrl'] == ''){
            $weixinInfo['weixinQRCodeUrl'] = IMG_NET_PATH.'/img/Admin/default_QR.png';
        }

        if($weixinInfo['weixinHeadUrl'] == ''){
            $weixinInfo['weixinHeadUrl'] = IMG_NET_PATH.'/img/Admin/default_head.png';
        }

        $this->assign('weixinInfo',$weixinInfo);

       $this->display('Weixin/weixinIDAddNew');

    }

    private function activeMenuInfo(){

        $TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->APPID."&secret=".$this->APPSECRET;

        $json=file_get_contents($TOKEN_URL);
        $result=json_decode($json,true);

        $ACC_TOKEN= $result['access_token'];

        $menuInfo = D('Weixin')->getMenuInfo();

        $menu_name1 = $menuInfo['menu_name1'];
        $menu_msgType1 = $menuInfo['menu_msgType1'];
        $menu_content1 = $menuInfo['menu_content1'];

        $menu_name2 = $menuInfo['menu_name2'];
        $menu_msgType2 = $menuInfo['menu_msgType2'];
        $menu_content2 = $menuInfo['menu_content2'];

        $menu_name3 = $menuInfo['menu_name3'];
        $menu_msgType3 = $menuInfo['menu_msgType3'];
        $menu_content3 = $menuInfo['menu_content3'];


        $menu_subNameForBtn1 = json_decode($menuInfo['menu_subNameForBtn1']);
        $menu_subMsgTypeForBtn1 = json_decode($menuInfo['menu_subMsgTypeForBtn1']);
        $menu_subContentForBtn1 = json_decode($menuInfo['menu_subContentForBtn1']);

        $menu_subNameForBtn2 = json_decode($menuInfo['menu_subNameForBtn2']);
        $menu_subMsgTypeForBtn2 = json_decode($menuInfo['menu_subMsgTypeForBtn2']);
        $menu_subContentForBtn2 = json_decode($menuInfo['menu_subContentForBtn2']);

        $menu_subNameForBtn3 = json_decode($menuInfo['menu_subNameForBtn3']);
        $menu_subMsgTypeForBtn3 = json_decode($menuInfo['menu_subMsgTypeForBtn3']);
        $menu_subContentForBtn3 = json_decode($menuInfo['menu_subContentForBtn3']);

        $str = '{"button":[';

        $menu_subMsgTypeForBtn1Count = count($menu_subMsgTypeForBtn1);
        $menu_subMsgTypeForBtn2Count = count($menu_subMsgTypeForBtn2);
        $menu_subMsgTypeForBtn3Count = count($menu_subMsgTypeForBtn3);
        if($menu_subMsgTypeForBtn1Count == 0){
            if($menu_msgType1){
                if($menu_msgType1=='click'){
                    $str.='{
                    "type":"'.$menu_msgType1.'",
                    "name":"'.$menu_name1.'",
                    "key":"'.$menu_content1.'"
                    }';
                }else if ($menu_msgType1=='view'){
                    $str.='{
                    "type":"'.$menu_msgType1.'",
                    "name":"'.$menu_name1.'",
                    "url":"'.$menu_content1.'"
                    }';
                }
                if($menu_msgType2 || $menu_subMsgTypeForBtn2 || $menu_msgType3 || $menu_subMsgTypeForBtn3){
                    $str.=',';
                }
            }
        }else{
            $str.='{"name":"'.$menu_name1.'","sub_button":[';

            for($i=0; $i<$menu_subMsgTypeForBtn1Count; $i++){

                if($menu_subMsgTypeForBtn1[$i]=='click'){
                    $str.='{
                    "type":"'.$menu_subMsgTypeForBtn1[$i].'",
                    "name":"'.$menu_subNameForBtn1[$i].'",
                    "key":"'.$menu_subContentForBtn1[$i].'"
                    }';
                }else if ($menu_subMsgTypeForBtn1[$i]=='view'){
                    $str.='{
                    "type":"'.$menu_subMsgTypeForBtn1[$i].'",
                    "name":"'.$menu_subNameForBtn1[$i].'",
                    "url":"'.$menu_subContentForBtn1[$i].'"
                    }';
                }
                if($i != ($menu_subMsgTypeForBtn1Count-1) ){
                    $str.=',';
                }else{
                    $str.=']}';
                    if($menu_msgType2 || $menu_subMsgTypeForBtn2 || $menu_msgType3 || $menu_subMsgTypeForBtn3){
                        $str.=',';
                    }
                }
            }
        }

        if($menu_subMsgTypeForBtn2Count == 0){
            if($menu_msgType2){
                if($menu_msgType2=='click'){
                    $str.='{
                    "type":"'.$menu_msgType2.'",
                    "name":"'.$menu_name2.'",
                    "key":"'.$menu_content2.'"
                    }';
                }else if ($menu_msgType2=='view'){
                    $str.='{
                    "type":"'.$menu_msgType2.'",
                    "name":"'.$menu_name2.'",
                    "url":"'.$menu_content2.'"
                    }';
                }
                if($menu_msgType3 || $menu_subMsgTypeForBtn3 ){
                    $str.=',';
                }
            }
        }else{
            $str.='{"name":"'.$menu_name2.'","sub_button":[';

            for($i=0; $i<$menu_subMsgTypeForBtn2Count; $i++){

                if($menu_subMsgTypeForBtn2[$i]=='click'){
                    $str.='{
                    "type":"'.$menu_subMsgTypeForBtn2[$i].'",
                    "name":"'.$menu_subNameForBtn2[$i].'",
                    "key":"'.$menu_subContentForBtn2[$i].'"
                    }';
                }else if ($menu_subMsgTypeForBtn2[$i]=='view'){
                    $str.='{
                    "type":"'.$menu_subMsgTypeForBtn2[$i].'",
                    "name":"'.$menu_subNameForBtn2[$i].'",
                    "url":"'.$menu_subContentForBtn2[$i].'"
                    }';
                }
                if($i != ($menu_subMsgTypeForBtn2Count - 1) ){
                    $str.=',';
                }else{
                    $str.=']}';
                    if($menu_msgType3 || $menu_subMsgTypeForBtn3){
                        $str.=',';
                    }
                }
            }
        }

        if($menu_subMsgTypeForBtn3Count == 0){
            if($menu_msgType3){
                if($menu_msgType3=='click'){
                    $str.='{
                    "type":"'.$menu_msgType3.'",
                    "name":"'.$menu_name3.'",
                    "key":"'.$menu_content3.'"
                    }';
                }else if ($menu_msgType3=='view'){
                    $str.='{
                    "type":"'.$menu_msgType3.'",
                    "name":"'.$menu_name3.'",
                    "url":"'.$menu_content3.'"
                    }';
                }
            }
        }else{
            $str.='{"name":"'.$menu_name3.'","sub_button":[';

            for($i=0; $i<$menu_subMsgTypeForBtn3Count; $i++){

                if($menu_subMsgTypeForBtn3[$i]=='click'){
                    $str.='{
                    "type":"'.$menu_subMsgTypeForBtn3[$i].'",
                    "name":"'.$menu_subNameForBtn3[$i].'",
                    "key":"'.$menu_subContentForBtn3[$i].'"
                    }';
                }else if ($menu_subMsgTypeForBtn3[$i]=='view'){
                    $str.='{
                    "type":"'.$menu_subMsgTypeForBtn3[$i].'",
                    "name":"'.$menu_subNameForBtn3[$i].'",
                    "url":"'.$menu_subContentForBtn3[$i].'"
                    }';
                }
                if($i != ($menu_subMsgTypeForBtn3Count-1)){
                    $str.=',';
                }else{
                    $str.=']}';
                }
            }
        }
        $str .=']}';
        $MENU_URL= "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$ACC_TOKEN;

        $ch = curl_init($MENU_URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length:'.strlen($str)));

        $info = curl_exec($ch);

        //创建成功返回：{"errcode":0,"errmsg":"ok"}
        $menu = json_decode($info);

        return $menu;
    }
}