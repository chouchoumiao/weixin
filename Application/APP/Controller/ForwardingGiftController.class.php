<?php
/**
 * Created by PhpStorm.
 * User: wb-wjy227944
 * Date: 2016/10/13
 * Time: 14:36
 */
namespace APP\Controller;
use APP\Model\ToolModel;
use Think\Controller;

header("Content-type:text/html;charset=utf-8");

class ForwardingGiftController extends CommonController {

    public function index(){

        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if(isset($action) && ('' != $action)){

            switch ($action){
                case 'showView':
                    $this->showView();
                    break;
                case 'forwardingGift':
                    $this->forwardingGift();
                    break;

                case 'forwardingGiftData':
                    $this->upload();
                    break;
            }
        }
    }


    /**
     * 进行刮奖逻辑
     */
    private function forwardingGift(){


        $info = D('ForwardingGift')->getForwardingGiftInfo();

        if($info){
            $arr['success'] = 1;
        }else{
            $arr['success'] = -1;
            $arr['msg'] = "未取得信息";
        }
        echo json_encode($arr);
        exit;
    }

    /**
     * 显示刮刮卡页面
     */
    private function showView(){

        $OKInfo = D('ForwardingGift')->getForwardingGiftInfoByFlag(1);

        if($OKInfo){
            $this->assign('OKInfo',$OKInfo);
        }

        $NGInfo = D('ForwardingGift')->getForwardingGiftInfoByFlag(2);
        if($NGInfo){
            $this->assign('NGInfo',$NGInfo);
        }

        $this->display('ForwardingGift');
    }

    /**
     * 上传图片
     */
    private function upload(){
        
        //每天只能提交一次,所以判断当前的记录条数,限制用户多次提交
        if(D('ForwardingGift')->isTodayExist()){
            ToolModel::goBack('不要多次提交');
            exit;
        }

        $ret = D('Common')->doUploadImg(FOLDER_NAME_FORWARDINGGIFT,true);

        //追加数据库记录
        if(!D('ForwardingGift')->addForwardingGift($ret[0]['thumbPath'],$ret[0]['imgPath'])){
            ToolModel::goBack('提交存入数据库出错，请重新提交!');
            exit;
        }

        ToolModel::goBack('提交成功，请耐心等待审核!');
    }
}