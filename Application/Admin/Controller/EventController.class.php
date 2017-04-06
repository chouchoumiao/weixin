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
                default:
                    break;

            }
        }
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