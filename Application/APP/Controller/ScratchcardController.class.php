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
        
        //是会员的情况下，进行奖品主信息查询
        
        //取得当前时间有效的刮刮卡Main信息
        $nowDate = date("Y-m-d",time());
        $sql = "SELECT * FROM  scratchcard_main
                WHERE scratchcard_beginDate <= '$nowDate'
                AND scratchcard_endDate >= '$nowDate'
                AND scratchcard_isDeleted = 0
                AND WEIXIN_ID = $weixinID
                ORDER BY scratchcard_id DESC";
        $scratchcardMainInfo = getlineBySql($sql);
                
        $this->display('Scratchcard');
    }

}