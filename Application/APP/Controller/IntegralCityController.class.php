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

class IntegralCityController extends CommonController {

    public function index(){

        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if(isset($action) && ('' != $action)){

            switch ($action){
                case 'integralCity':
                    $this->integralCity();
                    break;
                case 'integralJudge':
                    $this->integralJudge();
                    break;
            }
        }

    }

    /**
     * 进行积分商城逻辑
     */
    private function integralCity(){
        //取得当前时间内积分商城活动的商品信息
        $data = D('IntegralCity')->getIntegralCityEvent();

        if(!$data){
            $this->assign('noData',true);
        }else{
            $this->assign('data',$data);
        }
        $this->display('IntegralCity/IntegralCity');
    }

    /**
     * 显示积分商城页面
     */
    private function integralJudge(){


    }

}