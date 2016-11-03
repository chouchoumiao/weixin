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

        //设置删除图片的相关配置项
        $config = ToolModel::imgUploadConfig('ForwardingGift');
        //上传文件

        $retArr = ToolModel::uploadImg($config);

        if($retArr['success']){

            //设置缩略图
            $image = new \Think\Image();
            $image->open($_SERVER['DOCUMENT_ROOT'].'/weixin/Public/'.$retArr['msg']);
            // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
            $image->thumb(150, 150)->save($_SERVER['DOCUMENT_ROOT'].'/weixin/Public/Uploads/ForwardingGift/thumb.jpg');

            ToolModel::goBack('提交成功，请耐心等待审核!');
        }else{
            ToolModel::goBack('提交时出现错误:'.$retArr['msg'].'请重新提交!');
        }
    }


}