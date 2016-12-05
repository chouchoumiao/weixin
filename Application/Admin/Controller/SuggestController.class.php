<?php
namespace Admin\Controller;
use Admin\Controller;
use APP\Model\ToolModel;

class SuggestController extends CommonController {

    public function doAction(){
        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if(isset($action) && ('' != $action)){

            //$this->obj = D('Login');
            switch ($action){
                case 'reply':
                    $this->reply();
                    break;
                case 'delete':
                    $this->delete();
                    break;
            }
        }
    }

    private function delete(){
        if(!isset($_POST['id'])){
            ToolModel::jsonReturn(JSON_ERROR,'参数错误');
            exit;
        }

        $id = I('post.id',0);

        if(!D('Suggest')->deleteSuggest($id)){
            ToolModel::jsonReturn(JSON_ERROR,'删除失败');
            exit;
        }
        ToolModel::jsonReturn(JSON_SUCCESS,'删除成功');
    }

    //进行回复
    private function reply(){
        if(!isset($_POST)){
            ToolModel::jsonReturn(JSON_ERROR,'参数错误');
            exit;
        }

        $reply1 = I('post.reply1','');
        if($reply1 == ''){
            ToolModel::jsonReturn(JSON_ERROR,'回复内容不能为空');
            exit;
        }
        $id = I('post.id',0);
        if($id == 0){
            ToolModel::jsonReturn(JSON_ERROR,'参数错误');
            exit;
        }

        $reply2 = I('post.reply2','');
        $reply3 = I('post.reply3','');

        //写入数据库中
        if(!D('Suggest')->updateReply($id,$reply1,$reply2,$reply3)){
            ToolModel::jsonReturn(JSON_ERROR,'提交失败,请重新提交');
            exit;
        }
        ToolModel::jsonReturn(JSON_SUCCESS,'提交成功');
        
    }
}