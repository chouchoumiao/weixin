<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function doAction(){
        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if(isset($action) && ('' != $action)){

            //$this->obj = D('Login');
            switch ($action){
                case 'showMain':
                    $this->showMain();
                    break;
                //显示建议的信息画面
                case 'showSuggest':
                    $this->showSuggest();
                    break;


            }
        }

    }

    private function showMain(){
        $this->display('index');
    }


    private function showSuggest(){
        echo 'ddd';exit;
    }
}