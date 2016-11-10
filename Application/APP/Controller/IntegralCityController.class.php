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
                case 'showView':
                    $this->showView();
                    break;
                case 'integralCity':
                    $this->integralCity();
                    break;
            }
        }

    }

    /**
     * 进行积分商城逻辑
     */
    private function integralCity(){

    }

    /**
     * 显示积分商城页面
     */
    private function showView(){
        

    }

    //生成兑换码
    private function snMaker($pre) {
        $date = date('Ymd');
        $rand = rand(1000,9999);
        $time = mb_substr(time(), 5, 5, 'utf-8');
        $serialNumber = $time.$pre.$date.$rand;
        return $serialNumber;
    }

}