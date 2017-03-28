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
                default:
                    break;

            }
        }
    }

    public function showView(){

        $eventInfo = D('Weixin')->getTheWeixinInfo();
        if($eventInfo){

            $eventListText = explode(",",$eventInfo['eventNameList']);

            $this->assign('eventListText',$eventListText);
            $this->assign('eventInfo',$eventInfo);
        }
        $this->display('eventReplySet');
    }

    public function getNowData(){

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