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

/**
 * 建言献策
 * Class suggestController
 * @package APP\Controller
 */
class SuggestController extends CommonController {
    private $obj;

    public function index(){

        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if(isset($action) && ('' != $action)){

            $this->obj = D('Suggest');
            switch ($action){
                case 'showView':
                    $this->showView();
                    break;
                case 'suggested':
                    $this->suggested();
                    break;
            }
        }

    }

    /**
     * 进行建议Suggest逻辑
     * flag 1:区长 2:书记
     */
    private function suggested(){

        $flag = I('post.flag',0);
        if( ($flag != QUZHANG) && ($flag != SHUJI) ){
            ToolModel::goBack('参数错误');
        }

        //过滤参数
        $ret = $this->checkPostVal();
        if( 1 != $ret){
            ToolModel::goBack($ret);
            exit;
        }

        //判断是否已经存在相同的建言了
        $suggest = I('post.content','');
        if($this->obj->isSameSuggest($suggest)){
            ToolModel::goBack('已经有相同内容的建议了，请不要重复提交!');
            exit;
        }

        //通用户每天只能建言两次
        if($this->obj->getTodaySuggestdCounts($flag) >= 2){
            ToolModel::goBack('一天只能提交两次哦,请明天再继续提交您的建议');
            exit;
        }

        //上传多张图片
        //判断是否有上传图片
        $imgPathJson = '';
        $thumbPathJson = '';
        if(!isArrayNull($_FILES['up_img']['name'])){
            $ret = D('Common')->doUploadImg(FOLDER_NAME_SUGGEST,true);

            foreach ($ret as $name){
                $imgPathArr[] = $name['imgPath'];
                $thumbPathArr[] = $name['thumbPath'];
            }
            //5.4版本后,加JSON_UNESCAPED_SLASHES可以取消自动转义
            $imgPathJson = json_encode($imgPathArr,JSON_UNESCAPED_SLASHES);
            $thumbPathJson = json_encode($thumbPathArr,JSON_UNESCAPED_SLASHES);
        }
        //追加建言记录
        if(!$this->obj->addSuggest($flag,$imgPathJson,$thumbPathJson)){
            ToolModel::goBack('提交时出现错误，请重新提交！');
            exit;
        }

        ToolModel::goToUrl('提交成功！感谢您的建议,我们会认真详读','showView/flag/'.$flag);
    }

    /**
     * 显示建议页面
     * flag 1:区长 2:书记
     */
    private function showView(){

        $flag = I('get.flag',0);
        if( ( QUZHANG != $flag) && ( SHUJI != $flag)){
            echo '参数错误';
            exit;
        }

        if($flag == QUZHANG){
            $this->assign('quzhang',true);
        }
        //查询当前用户的建议回复信息
        $data = D('Suggest')->getReplyInfoByFlag($flag);
        if($data){
           $this->assign('data',$data);
        }

        $this->display('Suggest');
        
    }

    /**
     * 检查传入的参数的正确定
     * @return int|string
     */
    private function checkPostVal(){


        $name = $_POST['name'];
        $tel = $_POST['tel'];
        $content = $_POST['content'];

        if(!isset($name) || !isset($tel) || !isset($content)){
                return '传参错误';
        }

        if( ('' == strval($name)) ){
            return '姓名不能为空';
        }

        if( (ToolModel::getStrLen(strval($name))) > 8 ){
            return '姓名不能超过哦8个字符';
        }

        if( ('' == strval($content)) ){
            return '建议内容不能为空';
        }

        if(!is_mobile($tel)){
            return '手机格式错误';
        }
        return 1;
    }
}