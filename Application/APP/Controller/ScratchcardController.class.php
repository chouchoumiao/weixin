<?php
/**
 * Created by PhpStorm.
 * User: wb-wjy227944
 * Date: 2016/10/13
 * Time: 14:36
 */
namespace APP\Controller;
use Think\Controller;

header("Content-type:text/html;charset=utf-8");

class ScratchcardController extends CommonController {

    public function index(){

        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if(isset($action) && ('' != $action)){

            switch ($action){
                case 'scratchcard':
                    $this->scratchcard();
                    break;
            }
        }

    }

    private function scratchcard(){
        
        //取得当前时间有效的刮刮卡Main信息
        $data = D('Scratchcard')->getScratchcardMain();

        if($data){
            $this->assign('data',$data);
            $this->assign('detail_name',json_decode($data['scratchcard_detail_name']));
            $this->assign('detail_description',json_decode($data['scratchcard_detail_description']));
            $this->assign('detail_count',json_decode($data['scratchcard_detail_count']));
        }

        $this->display('Scratchcard');
        
    }

}