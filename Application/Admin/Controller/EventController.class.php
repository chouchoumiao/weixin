<?php
namespace Admin\Controller;
use Admin\Controller;
use APP\Model\ToolModel;

class EventController extends CommonController {

    private $userName,$userPwd;
    private $flag;

    public function doAction(){
        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if(isset($action) && ('' != $action)){

            //用于普通登录
            switch ($action){
                case 'showView':
                    $this->showView();
                    break;
                case 'getNowData':
                    $this->getNowData();
                    break;
                case 'setReply':
                    $this->setReply();
                    break;

                //大转盘后台设置操作
                case 'bigWheelManger':
                    $this->bigWheelManger();
                    break;


                case 'bigWheelSet':
                    $this->showBigWheelSet();
                    break;

                //大转盘主表相关
                case 'bigWheelMainInfo':
                    $this->bigWheelMainInfo();
                    break;

                //大转盘明细相关
                case 'bigWheelDetailInsert':
                    $this->bigWheelDetailInsert();
                    break;

                default:
                    break;

            }
        }
    }

    /**
     * 设置大转盘主页面
     */
    private function bigWheelManger(){

        //获得建言的总条数
        $count = D('BigWheel')->getBigWheelCount();

        //分页
        import('ORG.Util.Page');
        $Page = new \Org\Util\Page($count, PAGE_SHOW_COUNT_10);

        $nowPage = intval($Page->parameter['p']);

        $limit = $Page->firstRow . ',' . $Page->listRows;

        //取得指定条数的信息
        $data = D('BigWheel')->getAllBigWheelInfo($limit);

        $show = $Page->show();// 分页显示输出

        //如果有数据的情况
        if ($count > 0) {
            $this->assign('data', $data); //用户信息注入模板
            $this->assign('page', $show);    //赋值分页输出
            $this->assign('nowPage',$nowPage);
        }

        $this->display('Event/forBigWheel/bigWheelManger');

    }

    /**
     * 设置大转盘明细表相关
     */
    private function bigWheelDetailInsert(){

        //取得set页面传递过来的数据
        $mainInfoStr = I('post.mainInfoStr','');
        $bigWheel_id = I('post.bigWheel_id',0);

        //addslashes函数不能再数组中使用 20150925
        $titleArr = I('post.titleArr');
        $descriptionArr = I('post.descriptionArr');
        $probabilityArr = I('post.probabilityArr');
        $countArr = I('post.countArr');

        $newTitle = getPreg_replace($titleArr);
        $newDescription = getPreg_replace($descriptionArr);
        $newProbability = json_encode($probabilityArr);
        $newCount = json_encode($countArr);

        //先将Main表插入数据
        $mainInfoArr = explode(".",$mainInfoStr);

        $thisbigWheel_title = $mainInfoArr[0];
        $thisbigWheel_description = $mainInfoArr[1];
        $thisbigWheel_times = $mainInfoArr[2];
        $thisbigWheel_Integral = $mainInfoArr[3];
        $thisbigWheel_beginDate = $mainInfoArr[4];
        $thisbigWheel_endDate = $mainInfoArr[5];
        $thisbigWheel_expirationDate = $mainInfoArr[6];
        $thisbigWheel_address = $mainInfoArr[7];
        $thisbigWheel_imgPath = "img/activity-lottery-7.png";
        $thisbigWheel_migPathInner = "img/activity-lottery-2.png";
        $thisbigWheel_count = $mainInfoArr[8];


        $data['bigWheel_title'] = $thisbigWheel_title;
        $data['bigWheel_description'] = $thisbigWheel_description;
        $data['bigWheel_times'] = $thisbigWheel_times;
        $data['bigWheel_Integral'] = $thisbigWheel_Integral;
        $data['bigWheel_beginDate'] = $thisbigWheel_beginDate;
        $data['bigWheel_endDate'] = $thisbigWheel_endDate;
        $data['bigWheel_expirationDate'] = $thisbigWheel_expirationDate;
        $data['bigWheel_address'] = $thisbigWheel_address;
        $data['bigWheel_migPath'] = $thisbigWheel_imgPath;
        $data['bigWheel_migPathInner'] = $thisbigWheel_migPathInner;
        $data['bigWheel_count'] = $thisbigWheel_count;

        $data['bigWheel_detailInfo_title'] = $newTitle;
        $data['bigWheel_detailInfo_description'] = $newDescription;
        $data['bigWheel_detailInfo_probability'] = $newProbability;

        $data['bigWheel_detailInfo_title'] = $newCount;
        $data['bigWheel_insertTime'] = date("Y/m/d H:i:s",time());


        if($bigWheel_id > 0){       //更新
            $ret = D('BigWheel')->UpdateMainBigWheelInfo($data);
        }else{                      //新增
            $ret = D('BigWheel')->insertMainBigWheelInfo($data);
        }

        if(!$ret)
        {
            $arr['success'] = "NG";
            $arr['msg'] = "设置失败！";
        }else{
            $arr['success'] = "OK";
            $arr['msg'] = "设置成功！2秒后跳转到主页面";
        }
        echo json_encode($arr);
        exit;
    }

    /**
     * 设置大转盘主表相关
     */
    private function bigWheelMainInfo(){

        if(!isset($_POST['bigWheel_id']) || ( 0 == I('post.bigWheel_id',0))){
            $arr['success'] = 0;
            echo json_encode($arr);
            exit;
        }

        $bigWheel_id = I('post.bigWheel_id');

        $arr['detailInfoTitle'] = "";
        $arr['detailInfoDescription'] = "";
        $arr['detailInfoProbability'] = "";
        $arr['detailInfoCount'] = "";

        $bigWheelMainInfoArr = D('BigWheel')->getMainBigWheelInfoByID( $bigWheel_id );

        if($bigWheelMainInfoArr){
            $bigWheel_detailInfo_description = $bigWheelMainInfoArr['bigWheel_detailInfo_description'];
            $bigWheel_detailInfo_probability = $bigWheelMainInfoArr['bigWheel_detailInfo_probability'];
            $bigWheel_detailInfo_count = $bigWheelMainInfoArr['bigWheel_detailInfo_count'];

            //修正bug 取出的数据不是数据或者为空的情况下,返回值为空 20150925
            if($bigWheel_detailInfo_description){
                $detailInfoDescription = json_decode($bigWheel_detailInfo_description);
            }else{
                $detailInfoDescription = "";
            }
            if($bigWheel_detailInfo_probability){
                $detailInfoProbability = json_decode($bigWheel_detailInfo_probability);
            }else{
                $detailInfoProbability = "";
            }
            if($bigWheel_detailInfo_count){
                $detailInfoCount = json_decode($bigWheel_detailInfo_count);
            }else{
                $detailInfoCount = "";
            }
            $arr['detailInfoDescription'] = $detailInfoDescription;
            $arr['detailInfoProbability'] = $detailInfoProbability;
            $arr['detailInfoCount'] = $detailInfoCount;
        }

        $arr['success'] = 1;
        echo json_encode($arr);
        exit;

    }

    /**
     * 显示大转盘后台操作的页面
     */
    private function showBigWheelSet(){

        //判断是否有传入的大转盘ID
        if( isset($_GET['bigWheel_id']) && (0 != I('get.bigWheel_id',0))){
            //根据传入的ID取得该ID的大转盘信息
            $bigWheel_id = I('get.bigWheel_id',0);

            $data = D('BigWheel')->getBigWheelInfoByID($bigWheel_id);

            $this->assign('data',$data);


        }

        $this->display('Event/forBigWheel/bigWheelSet');

    }

    /**
     * 提交设置
     */
    private function setReply(){

        //上传封面图片
        $retArrs =   D('CommonAdmin')->doAdminUploadImg(FOLDER_NAME_ADMIN_EVENT);

        //判断是新增还是修改
        if( '' == I('post.replyID')){   //新增
            if (count($retArrs) <= 0){
                ToolModel::goBack('需要上传图片才能提交');
                exit;
            }else{
                $imgUrl = IMG_NET_PATH.$retArrs['up_img']['imgPath'];
            }

            //进行新增操作
            $ret = D('Event')->addNewReply($imgUrl,$this->getForwardUrl());
        }else{                          //修改
            if(count($retArrs) <= 0){
                $imgUrl = I('post.imgUrl','');
            }else{
                $imgUrl = IMG_NET_PATH.$retArrs['up_img']['imgPath'];
            }

            $ret = D('Event')->editReply($imgUrl);
        }
        if($ret){
            $msg = "设置成功！";
        }else{
            $msg = "设置失败！";
        }
        echoInfo($msg);
        exit;
    }


    private function showView(){

        $eventInfo = D('Weixin')->getTheWeixinInfo();
        if($eventInfo){

            $eventListText = explode(",",$eventInfo['eventNameList']);

            $this->assign('eventListText',$eventListText);
            $this->assign('eventInfo',$eventInfo);
        }
        $this->display('eventReplySet');
    }

    /**
     * 根据获得的事件选择返回对应的前端网页地址
     * @return string
     */
    private function getForwardUrl(){

        $flag = I('post.eventTypeText','');

        //需要更改
        switch ($flag)
        {
            case "会员中心":
                $eventUrl = "01_vipCenter/VipCennter.php";
                break;
            case "积分商城":
                $eventUrl = "02_integralCity/integralCity.php";
                break;
            case "会员答题":
                $eventUrl = "03_integralAnswer/answerMain.php";
                break;
            case "红黑榜":
                $eventUrl = "04_bbs/bbsMain.php";
                break;
            case "照片墙":
                $eventUrl = "05_photoWall/photoWallMain.php";
                break;
            case "建言献策":
                $eventUrl = "06_advice/adviceMain.php";
                break;
            case "大转盘":
                $eventUrl = "94_bigwheel/bigWheel.php";
                break;
            case "刮刮卡":
                $eventUrl = "95_scratchcard/scratchcard.php";
                break;
            //答题刮刮卡追加 20151104
            case "答题刮刮卡":
                $eventUrl = "http://1.datiguaguaka.sinaapp.com/03_integralAnswer/answerMain.php";
                break;
            default:
                $eventUrl = '传入参数错误，为'.$flag;
                break;
        }
        return $eventUrl;
    }

    private function getNowData(){

        $dataInfo = D('Event')->getInfoByEvebtText();

        if($dataInfo){
            $arr['reply_intext'] = $dataInfo['reply_intext'];
            $arr['reply_title'] = $dataInfo['reply_title'];
            $arr['reply_ImgUrl'] = $dataInfo['reply_ImgUrl'];
            $arr['reply_description'] = $dataInfo['reply_description'];
            $arr['reply_content'] = $dataInfo['reply_content'];
            $arr['replyID'] = $dataInfo['id'];
        }else{
            $arr['reply_intext'] = "";
            $arr['reply_title'] = "";
            $arr['reply_ImgUrl'] = "/weixin/Public/img/upload.jpg";
            $arr['reply_description'] = "";
            $arr['reply_content'] = "";
            $arr['replyID'] = "";
        }

        ToolModel::jsonReturn(JSON_SUCCESS,$arr);

//        if($dataInfo){
//            $this->assign('data',$dataInfo);
//            $this->assign('imgUrl',$dataInfo['reply_ImgUrl']);
//        }else{
//            $this->assign('imgUrl','../../img/upload.jpg');
//        }



    }
}