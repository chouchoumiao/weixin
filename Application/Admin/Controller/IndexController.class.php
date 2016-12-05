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

        $date = D('Suggest')->getSuggestDate();

        if($date){
            $count = count($date);
            $this->assign('count',$count);

            for($i=0;$i<$count;$i++){
                $data = D('Suggest')->getSuggestInfoByDate($date[$i]["create_date"]);

                foreach ($data as $key=>$vo){

                    $thumbPathArr = json_decode($vo['thumbPath']);
                    $imgPathArr = json_decode($vo['imgPath']);
                    $imgCount = count($thumbPathArr);

                    if( '' == strval($vo['reply1'])){
                        $data[$key]['replyFlag1'] = true;
                    }else{
                        if('' == strval($vo['reply2'])){
                            $data[$key]['replyFlag1'] = true;
                            $data[$key]['replyFlag1Readonly'] = "readonly='readonly'";
                            $data[$key]['replyFlag2'] = true;
                        }else{
                            if('' == strval($vo['reply3'])){
                                $data[$key]['replyFlag1'] = true;
                                $data[$key]['replyFlag1Readonly'] = "readonly='readonly'";
                                $data[$key]['replyFlag2'] = true;
                                $data[$key]['replyFlag2Readonly'] = "readonly='readonly'";
                                $data[$key]['replyFlag3'] = true;
                            }else{
                                $data[$key]['replyFlag1'] = true;
                                $data[$key]['replyFlag1Readonly'] = "readonly='readonly'";
                                $data[$key]['replyFlag2'] = true;
                                $data[$key]['replyFlag2Readonly'] = "readonly='readonly'";
                                $data[$key]['replyFlag3'] = true;
                                $data[$key]['replyFlag3Readonly'] = "readonly='readonly'";
                            }
                        }
                    }
                    $vo['reply2'] = strval($vo['reply2']);
                    $vo['reply3'] = strval($vo['reply3']);

                    switch ($imgCount){
                        case 1:
                            $data[$key]['imgData'] = true;
                            $data[$key]['thumbPath1'] = $thumbPathArr[0];
                            
                            $data[$key]['imgPath1'] = $imgPathArr[0];
                            break;
                        case 2:
                            $data[$key]['imgData'] = true;
                            $data[$key]['thumbPath1'] = $thumbPathArr[0];
                            $data[$key]['thumbPath2'] = $thumbPathArr[1];

                            $data[$key]['imgPath1'] = $imgPathArr[0];
                            $data[$key]['imgPath2'] = $imgPathArr[1];
                            break;
                        case 3:
                            $data[$key]['imgData'] = true;
                            $data[$key]['thumbPath1'] = $thumbPathArr[0];
                            $data[$key]['thumbPath2'] = $thumbPathArr[1];
                            $data[$key]['thumbPath3'] = $thumbPathArr[2];

                            $data[$key]['imgPath1'] = $imgPathArr[0];
                            $data[$key]['imgPath2'] = $imgPathArr[1];
                            $data[$key]['imgPath3'] = $imgPathArr[2];
                            break;
                        default:
                            break;
                    }
                }
                $date[$i]['data'] = $data;
            }
            $this->assign('date',$date);
        }
        //dump($date);exit;

        $this->display('Suggest/suggest');
    }
}