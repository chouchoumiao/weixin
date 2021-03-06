<?php
namespace APP\Controller;
use Think\Controller;
use APP\Model\ToolModel;
use Think\Log;

class IndexController extends Controller {

    public function index(){

        $weixinID = addslashes($_GET['weixinID']);

        $where['weixinStatus'] = 1;
        $where['id'] = $weixinID;

        $weixinInfo = M()->table('adminToWeiID')->where($where)->find();

        define('TOKEN', $weixinInfo['weixinToken']);
        define('AppID', $weixinInfo['weixinAppId']);

        $wechatObj = new wechatCallbackapiTest($weixinID,$weixinInfo['weixinName']);
        if (!isset($_GET['echostr'])) {
            $wechatObj->responseMsg();
        }else{
            $wechatObj->valid();
        }
    }
    public function VipBD(){

        //判断是否存在POST数据
        if(!isset($_POST)){

            ToolModel::jsonReturn(JSON_ERROR,'参数错误！');
        }

        //先判断是否已经是会员身份了
        if (D('Vip')->isVip()){
            ToolModel::jsonReturn(JSON_ERROR,'您已经是会员了！');
        }

        $thisVip_tel = I('post.tel');

        //判断新会员输入的手机号是否VIp数据表中已经存在，存在则提示错误
        if(D('Vip')->isTelExist($thisVip_tel)){

            ToolModel::jsonReturn(JSON_ERROR,'您的联系手机已经被使用，请返回确认！');
        }

        //取得该公众号的基础信息
        $config = D('Common')->getCon();

        $thisVip_integral = $config['CONFIG_INTEGRALINSERT'];
        //成为新会员获得积分数
        $newVipFirstIntegral = $config['CONFIG_INTEGRALINSERT'];
        //存在推荐人的时候，新会员注册成功时，该推荐人可获得积分数
        $plusIntegral = $config['CONFIG_INTEGRALREFERRER'];
        //存在推荐人的时候，新会员注册成功时，新会员也可以获得额外积分数
        $plusIntegralForNewVip = $config['CONFIG_INTEGRAL_REFERRER_FOR_NEW_VIP'];
        $weixinName = $config['CONFIG_VIP_NAME'];

        //推荐人
        $thisVip_referrer = I('post.referrer','');
        $thisVip_name = I('post.name','');
        $thisVip_sex = I('post.sex');

        //不存在推荐人
        if( !isset($thisVip_referrer) || ('' == $thisVip_referrer)){
            //追加会员信息
            if (!D('Vip')->addVipInfo($thisVip_name,$thisVip_sex,$thisVip_tel,$thisVip_integral,'')){

                ToolModel::jsonReturn(JSON_ERROR,'新增会员失败，请重试！');
            }

            //新增会员信息后追加写入记录表中
            D('Common')->addIntegralRecord($_SESSION['openid'],'会员绑定无推荐人',0,$thisVip_integral);

            $msg = " 亲，您已经成功绑定了会员！</br>
                        初次绑定获得$weixinName".$newVipFirstIntegral;
            ToolModel::jsonReturn(JSON_SUCCESS,$msg);
        }

        //存在推荐人
        $referrerInfo = D('Vip')->getReferrerInfo($thisVip_referrer);
        //检查是否存在该推荐人
        if( !$referrerInfo){
            ToolModel::jsonReturn(JSON_ERROR,'不存在该推荐人，请确认！');
        }

        //取得推荐人的原始积分数
        $oldVipIntegral = $referrerInfo['Vip_integral'];

        //与推荐积分数累加后生成新的积分数，并写入Vip数据表中
        $newVipIntegral = $oldVipIntegral + $plusIntegral;

        //更新推荐人的积分
        if(!D('Vip')->updateReferrerIntegral($thisVip_referrer,$newVipIntegral)){

            ToolModel::jsonReturn(JSON_ERROR,'推荐人分值更新失败，请重试！');
        }

        //追加积分变动时写入记录表中 功能
        D('Common')->addIntegralRecord($thisVip_referrer,'会员绑定推荐人加'.$weixinName,$oldVipIntegral,$plusIntegral);

        //存在推荐人的时候，新积分数 = 新会员初始积分数+额外积分数
        $thisVipIntegral = $thisVip_integral + $plusIntegralForNewVip;

        if (!D('Vip')->addVipInfo($thisVip_name,$thisVip_sex,$thisVip_tel,$thisVipIntegral,'Vip_comment')){
            ToolModel::jsonReturn(JSON_ERROR,'新增会员失败，请重试！');
        }

        //新增会员信息后追加写入记录表中
        D('Common')->addIntegralRecord($_SESSION['openid'],'会员绑定存在推荐人会员加'.$weixinName,0,$thisVipIntegral);
        //根据IP地址取得城市名称 20151215
        $city = getCity();

        //判断是否是台州地区和路桥发布公众号，满足条件写入活动表
        //if(strstr($city,'浙江') && $weixinID == 69){
        if(strstr($city,ALLOW_PROVINCE)){

            if(D('Common')->addIphoneEvent($thisVip_name,$thisVip_sex,$thisVip_tel,$thisVip_referrer)){

                $msg = "亲，您已经成功绑定了会员！</br>
                    初次绑定获得$weixinName".$newVipFirstIntegral."</br>
                    存在推荐人额外获得$weixinName:".$plusIntegralForNewVip."</br>
                    推荐人也同时获得额外$weixinName".$plusIntegral."</br>
                    推荐人还获得一个印章，积攒印章可得大奖"."</br>";
                ToolModel::jsonReturn(JSON_SUCCESS,$msg);
            }
        }

        $msg = "亲，您已经成功绑定了会员！</br>
                    初次绑定获得$weixinName".$newVipFirstIntegral."</br>
                    存在推荐人额外获得$weixinName:".$plusIntegralForNewVip."</br>
                    推荐人也同时获得额外$weixinName".$plusIntegral."</br>
                    推荐人还获得一个印章，积攒印章可得大奖"."</br>";
        ToolModel::jsonReturn(JSON_SUCCESS,$msg);
    }
}

/**
 * 微信验证方法类
 * Class wechatCallbackapiTest
 * @package APP\Controller
 */
class wechatCallbackapiTest
{
    private $weixinID;
    private $weixinName;
    private $openid;

    public function __construct($weixinID,$weixinName)
    {
        $this->weixinID = $weixinID;
        $this->weixinName = $weixinName;

        //并将weixinID和openid存入session中

        unset($_SESSION['weixinID']);
        $_SESSION['weixinID'] = $this->weixinID;

    }

    /**
     *  验证签名
     */
    public function valid()
    {
        $echoStr = $_GET['echostr'];
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $signature){
            echo $echoStr;
            exit;
        }
    }

    /**
     *  响应消息
     */
    public function responseMsg()
    {
        $timestamp  = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $msg_signature  = $_GET['msg_signature'];
        $encrypt_type = (isset($_GET['encrypt_type']) && ($_GET['encrypt_type'] == 'aes')) ? 'aes' : 'raw';

        $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
        if (!empty($postStr)){

            //2014/11/01 解密
            if ($encrypt_type == 'aes'){

                $pc = new \WXBizMsgCrypt(TOKEN, EncodingAESKey, AppID);
                $this->logger(' D \r\n'.$postStr);
                $decryptMsg = '';  //解密后的明文
                $errCode = $pc->DecryptMsg($msg_signature, $timestamp, $nonce, $postStr, $decryptMsg);
                $postStr = $decryptMsg;
            }
            //$this->logger("R ".$postStr); 2014/11/01
            $this->logger(' R \r\n'.$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            $this->openid = strval($postObj->FromUserName);
            //获得的opendid设置到session中
            unset($_SESSION['openid']);
            $_SESSION['openid'] = $this->openid;

            //消息类型分离
            switch ($RX_TYPE)
            {
                case 'event':
                    $result = $this->receiveEvent($postObj);
                    break;
                case 'text':
                    $result = $this->receiveText($postObj);
                    break;
                case 'image':
                    $result = $this->receiveImage($postObj);
                    break;
                case 'location':
                    $result = $this->receiveLocation($postObj);
                    break;
                case 'voice':
                    $result = $this->receiveVoice($postObj);
                    break;
                case 'video':
                    $result = $this->receiveVideo($postObj);
                    break;
                case 'link':
                    $result = $this->receiveLink($postObj);
                    break;
                default:
                    $result = 'unknown msg type: '.$RX_TYPE;
                    break;
            }
            $this->logger(' R \r\n'.$result);
            //2014/11/01 加密
            if ($encrypt_type == 'aes'){
                $encryptMsg = ''; //加密后的密文
                $errCode = $pc->encryptMsg($result, $timestamp, $nonce, $encryptMsg);
                $result = $encryptMsg;
                $this->logger(' E \r\n'.$result);
            }
            echo $result;
        }else {
            echo '';
            exit;
        }
    }

    /**
     * 接收事件消息
     * @param $object
     * @return string|void
     */
    private function receiveEvent($object)
    {

        $content = '';
        //公众号ID分类  臭臭喵工作室54,*******
        $thisTime = date('Y-m-d H:i:s', time());

        switch ($object->Event)
        {

            case 'subscribe':

                $subscribeOldSqlWhere['WEIXIN_ID'] = $this->weixinID;
                $subscribeOldSqlWhere['subscribe_openid'] = $this->openid;
                $subscribeOld = M()->table('subscribeInfo')->where($subscribeOldSqlWhere)->find();

                if(!$subscribeOld){

                    $insertData['subscribe_openid'] = $this->openid;
                    $insertData['subscribe_insertTime'] = $thisTime;
                    $insertData['WEIXIN_ID'] = $this->weixinID;
                    $insertData['subscribe_status'] = 1;
                    $insertData['subscribe_editTime'] = $thisTime;

                    M()->table('subscribeInfo')->add($insertData);

                }else{
                    if($subscribeOld['subscribe_status'] == 0){

                        $updateData['subscribe_status'] = 1;
                        $updateData['subscribe_editTime'] = $thisTime;
                        $updateData['WEIXIN_ID'] = $this->weixinID;

                        M()->table('subscribeInfo')->save($updateData);
                    }
                }
                $content = "欢迎关注【".$this->weixinName."】，谢谢。";
                $content .= (!empty($object->EventKey))?("\n来自二维码场景 ".str_replace("qrscene_","",$object->EventKey)):"";

                break;
            case 'unsubscribe':

                $updateData2['subscribe_status'] = 0;
                $updateData2['subscribe_editTime'] = $thisTime;

                $updateWhere2['subscribe_openid'] = $this->openid;
                $updateWhere2['WEIXIN_ID'] = $this->weixinID;

                M()->table('subscribeInfo')->where($updateWhere2)->save($updateData2);

                $content = '取消关注';
                break;
            case 'SCAN':
                $content = '扫描场景 '.$object->EventKey;
                break;
            case 'CLICK':

                //判断是否是从浙江在线绑定过来的(含有zjolTitleName)
                $keyNameArr = explode('=',$object->EventKey);
                $keyNameArrCount = count($keyNameArr) - 1;
                if($keyNameArrCount > 0){
                    $thisKeyName = $keyNameArr[$keyNameArrCount];
                }else{
                    $thisKeyName = $object->EventKey;
                }


//                $content = '取消关注'.$object->EventKey;
                $replyInfo = D('Common')->getReplyInfo();

                $replyInfoCount = count($replyInfo);
                if($replyInfoCount > 0){
                    //传入的时候使用base64编码加密
                    $newOpenid = base64_encode($this->openid);
                    $newweixinID = base64_encode($this->weixinID);
                    for($i = 0;$i<$replyInfoCount;$i++){
                        if ($thisKeyName  == $replyInfo[$i]['reply_intext']){
                            $content = array();
                            $content[] = array(
                                'Title'=>$replyInfo[$i]['reply_title'],
                                'Description'=>$replyInfo[$i]['reply_description'],
                                'PicUrl'=>$replyInfo[$i]['reply_ImgUrl'],
                                'Url' =>$replyInfo[$i]['reply_url']."/openid/".$newOpenid."/weixinID/=".$newweixinID);
                        }
                    }
                }

//                $replyInfoWhwere['WEIXIN_ID'] = $this->weixinID;
//                $replyInfo = M()->table('replyInfo')->where($replyInfoWhwere)->select();
//
//                $replyCount = count($replyInfo);
//                for($i = 0; $i<$replyCount; $i++){
//                    if($object->EventKey == $replyInfo[$i]['reply_intext']){
//                        $content = array();
//                        $content[] = array(
//                            "Title"=>$replyInfo[$i]['reply_title'],
//                            "Description"=>$replyInfo[$i]['reply_description'],
//                            "PicUrl"=>$replyInfo[$i]['reply_ImgUrl'],
//                            "Url" =>$replyInfo[$i]['reply_url']."?openid=".$this->openid."&weixinID=".$this->weixinID
//                        );
//                    }
//                }
                break;
            case 'LOCATION':
                $content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
                break;
            case 'VIEW':
                $content = "跳转链接 ".$object->EventKey;
                break;
            case 'MASSSENDJOBFINISH':
                $content = "消息ID：".$object->MsgID."，结果：".$object->Status."，粉丝数：".$object->TotalCount."，过滤：".
                    $object->FilterCount."，发送成功：".$object->SentCount."，发送失败：".$object->ErrorCount;
                break;
            default:
                $content = "receive a new event: ".$object->Event;
                break;
        }
        if(is_array($content)){
            if (isset($content[0])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }

        return $result;
    }

    //接收文本消息
    private function receiveText($object)
    {

        $nowTime = date('Y-m-d H:i:s', time());
        $keyword = trim($object->Content);

        //多客服人工回复模式
        $hongbaoWhere['WEIXIN_ID'] = $this->weixinID;
        $hongbaoWhere['hongbao_Status'] = 1;

        $hongbaoInfo = M()->table('hongbaoInfo')->where($hongbaoWhere)->find();

        if($hongbaoInfo){
            $hongbaoID = $hongbaoInfo['hongbao_id'];
            //根据WeixinID和hongbaoID取得回复内容

            $where['hongbao_id'] = $hongbaoID;
            $hongbaoReply = M()->table('replyInfo')->where($where)->find();

            $replyintext = $hongbaoReply['reply_intext'];
        }else{
            $replyintext = 'Nothing is OKSSSSDdWWWWWWf';
        }
        //红包活动
        if (strstr($keyword, $replyintext)){
            if($hongbaoInfo){
                if($hongbaoInfo['hongbao_beginTime'] > $nowTime){
                    $content = array();
                    $content[] = array(
                        'Title'=>$hongbaoReply['reply_title'],
                        'PicUrl'=>$hongbaoReply['reply_ImgUrl']
                    );
                    $content[] = array('Title'=>"活动将于".$hongbaoInfo['hongbao_beginTime'].'开始');
                }else if($hongbaoInfo['hongbao_endTime'] < $nowTime){
                    $content = array();
                    $content[] = array(
                        'Title'=>$hongbaoReply['reply_title'],
                        'PicUrl'=>$hongbaoReply['reply_ImgUrl']
                    );
                    $content[] = array('Title'=>'本次活动已经结束，敬请关注下次活动，谢谢！');
                }else{

                    //防止刷屏  将用户回复红包次数超过10次的  不让其进行活动
                    //先查询回复红包的用户是否已经写入数据表hongbaoTimes中
                    $where['subscribe_openid'] = $this->openid;
                    $where['status'] = 1;
                    $where['hongbao_id'] = $hongbaoID;

                    $hongbaoTimes = M()->table('hongbaoTimes')->where($where)->find();

                    //如果不存在的情况下，插入新的记录
                    if(!$hongbaoTimes){

                        $data['hongbao_id'] = $hongbaoID;
                        $data['subscribe_openid'] = $this->openid;
                        $data['times'] = 1;
                        $data['insertime'] = $nowTime;
                        $data['status'] = 1;

                        M()->table('hongbaoTimes')->add($data);

                        //存在数据的情况下，再分析是否回复次数超过8次（超过则回复次数过多，不能进行,，然后返回；没有超过，就把次数加1写入数据库，继续）
                    }else{
                        $oldTimes = $hongbaoTimes['times'];
                        if($oldTimes >8){
                            $content = '您刷红包次数过多，请等待下次活动！';
                            $result = $this->transmitText($object, $content);
                            return $result;
                        }else{
                            $newTimes = $hongbaoTimes['times'] + 1;

                            $hongbaoTimesUpdateData['times'] = $newTimes;
                            $hongbaoTimesUpdateData['editTime'] = $nowTime;

                            $hongbaoTimesWhere['subscribe_openid'] = $this->openid;
                            $hongbaoTimesWhere['hongbao_id'] = $hongbaoID;

                            M()->table('hongbaoTimes')->where($hongbaoWhere)->save($hongbaoTimesUpdateData);
                        }
                    }


                    $hongbaoRecordWhere['record_UserOpenid'] = $this->openid;
                    $hongbaoRecordWhere['record_Status'] = 1;
                    $hongbaoRecordWhere['hongbao_id'] = $hongbaoID;

                    $hongbaoUserRecord = M()->table('hongbaoRecord')->where($hongbaoRecordWhere)->find();

                    if(!$hongbaoUserRecord){
                        $hongbaoCord = $hongbaoInfo['hongbao_password'];
                        $pswCount = strlen($hongbaoCord) - 1;
                        $p_num=rand(0,$pswCount);
                        $p_num2=rand(0,$pswCount);
                        while($p_num==$p_num2){
                            $p_num2=rand(0,$pswCount);
                        }
                        $pp_num=array();
                        $pp_num[]=$p_num;
                        $pp_num[]=$p_num2;
                        //设置该Openid的位数信息，并通过json_encode方法转化为json数据存入数据库
                        $pp_numNew = json_encode($pp_num);

                        $user_num=array();


                        $user_num[]=substr($hongbaoCord,$p_num,1);
                        $user_num[]=substr($hongbaoCord,$p_num2,1);
                        //设置该Openid的对应位数的数字信息，并通过json_encode方法转化为json数据存入数据库
                        $user_numNew = json_encode($user_num);

                        $hongbaoRecordInsertData['hongbao_id'] = $hongbaoID;
                        $hongbaoRecordInsertData['record_UserOpenid'] = $this->openid;
                        $hongbaoRecordInsertData['record_Pn'] = $pp_numNew;
                        $hongbaoRecordInsertData['record_num'] = $user_numNew;
                        $hongbaoRecordInsertData['record_insertDate'] = $nowTime;
                        $hongbaoRecordInsertData['record_Status'] = 1;

                        $errorNo = M()->table('hongbaoRecord')->add($hongbaoRecordInsertData);

                        if( false !== $errorNo){
                            $pn=$p_num+1;
                            $pn2=$p_num2+1;
                            $content = array();
                            $content[] = array('Title'=>$hongbaoReply['reply_title'],
                                'PicUrl'=>$hongbaoReply['reply_ImgUrl']);
                            $content[] = array('Title'=>$hongbaoReply['reply_description']);
                            $content[] = array('Title'=>"第".$pn."位幸运数字：".$user_num[0]);
                            $content[] = array('Title'=>"第".$pn2."位幸运数字：".$user_num[1]);
                        }
                    }else{
                        //取得该Openid的位数信息，并通过json_decode方法转化为数组
                        $pn=json_decode($hongbaoUserRecord['record_Pn']);
                        $pn[0]=$pn[0]+1;
                        $pn[1]=$pn[1]+1;

                        //取得该Openid的对应位数的数字信息，并通过json_decode方法转化为数组
                        $user_num=json_decode($hongbaoUserRecord['record_num']);
                        $content = array();
                        $content[] = array('Title'=>$hongbaoReply['reply_title'],
                            'PicUrl'=>$hongbaoReply['reply_ImgUrl']);
                        $content[] = array('Title'=>$hongbaoReply['reply_description']);
                        $content[] = array('Title'=>"第".$pn[0]."位幸运数字：".$user_num[0]);
                        $content[] = array('Title'=>"第".$pn[1]."位幸运数字：".$user_num[1]);
                    }
                }
            }else{
                $content = '当前没有该活动，敬请关注！';
            }
            $result = $this->transmitText($object, $content);
        }else{
            //红包以外活动

            $isSeted = 'NO';

            $replyInfo = D('Common')->getReplyInfo();

            $replyInfoCount = count($replyInfo);
            if($replyInfoCount > 0){
                //传入的时候使用base64编码加密
                $newOpenid = base64_encode($this->openid);
                $newweixinID = base64_encode($this->weixinID);
                for($i = 0;$i<$replyInfoCount;$i++){
                    if ($keyword == $replyInfo[$i]['reply_intext']){
                        $content = array();
                        $content[] = array(
                            'Title'=>$replyInfo[$i]['reply_title'],
                            'Description'=>$replyInfo[$i]['reply_description'],
                            'PicUrl'=>$replyInfo[$i]['reply_ImgUrl'],
                            'Url' =>$replyInfo[$i]['reply_url']."/openid/".$newOpenid."/weixinID/=".$newweixinID);
                        $isSeted = 'YES';
                    }
                }
            }
            //追加会员中心画面
//            if ($keyword ==  "会员中心"){
//
//                $openidUrlEncode = base64_encode($this->openid);
//                $weixinIDUrlEncode = base64_encode($this->weixinID);
//
//                $content = array();
//                $content[] = array("Title"=>"路桥发布”会员中心",  "Description"=>"微”言大义，居“信”为政！“路桥发布”期待与您一起携手打造风清气朗的网络空间。",
//                    "PicUrl"=>"http://zglqxww-weixincourse.stor.sinaapp.com/VipCenter/vipCard.png",
//                    //"Url" =>"http://3.zglqxww.sinaapp.com/01_vipCenter/VipCennter.php?openid=".$encodeOpenid."&weixinID=".$encodeWeixinID);
//                    //"Url" =>"http://www.chouchoumiao.com/weixin/APP/VipCenter/index/openid/".$openid."/weixinID/".$weixinID);
//                    "Url" =>"http://www.chouchoumiao.com/weixin/APP/VipCenter/index/action/center/openid/".$openidUrlEncode."/weixinID/".$weixinIDUrlEncode);
//                $isSeted = "YES";
//            }
            /*测试用*/
            if($isSeted == 'NO'){

                $content = '请继续关注我们，谢谢!';

            }
        }
        if(is_array($content)){
            if (isset($content[0]['PicUrl'])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }

    //接收图片消息
    private function receiveImage($object)
    {
        $content = array('MediaId'=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    //接收位置消息
    private function receiveLocation($object)
    {
        $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".
            $object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收语音消息
    private function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array('MediaId'=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }

        return $result;
    }

    //接收视频消息
    private function receiveVideo($object)
    {
        $content = array('MediaId'=>$object->MediaId,
            'ThumbMediaId'=>$object->ThumbMediaId,
            'Title'=>'',
            'Description'=>'');
        $result = $this->transmitVideo($object, $content);
        return $result;
    }

    //接收链接消息
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //回复文本消息
    private function transmitText($object, $content)
    {
        //防止回复时 显示【该公众号暂时无法提供服务请稍后再试】的提醒
        if (!isset($content) || empty($content)){
            return '';
        }
        $xmlTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                   </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复图片消息
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $xmlTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[image]]></MsgType>
                        $item_str
                  </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复语音消息
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

        $xmlTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[voice]]></MsgType>
                        $item_str
                   </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复视频消息
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
                        <MediaId><![CDATA[%s]]></MediaId>
                        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                    </Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $xmlTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[video]]></MsgType>
                        $item_str
                  </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "<item>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                    </item>";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[news]]></MsgType>
                        <ArticleCount>%s</ArticleCount>
                        <Articles>
                        $item_str</Articles>
                  </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    //回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <MusicUrl><![CDATA[%s]]></MusicUrl>
                        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                    </Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $xmlTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[music]]></MsgType>
                        $item_str
                  </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复多客服消息
    private function transmitService($object)
    {
        $xmlTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[transfer_customer_service]]></MsgType>
                  </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //日志记录
    private function logger($log_content)
    {
//        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
//            sae_set_display_errors(false);
//            sae_debug($log_content);
//            sae_set_display_errors(true);
//        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
        $max_size = 10000;
        $log_filename = "log.xml";
        if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
        file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);
//        }
    }

}