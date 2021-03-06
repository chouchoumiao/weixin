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
 * Class AdviceController
 * @package APP\Controller
 */
class AdviceController extends CommonController {
    private $obj;

    public function index(){

        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);
        if(isset($_GET['weixinID'])){
            $_SESSION['weixinID'] = $_GET['weixinID'];
        }

        if(isset($action) && ('' != $action)){

            $this->obj = D('Advice');
            switch ($action){
                case 'showView':
                    $this->showView();
                    break;
                case 'adviceMain':
                    $this->adviceMain();
                    break;
                case 'adviceShow':
                    $this->adviceShow();
                    break;
                case 'adviceScratchcard':
                    $this->adviceScratchcard();
                    break;
                case 'adviced':
                    $this->adviced();
                    break;
            }
        }

    }

    /**
     * 进行建言献策逻辑
     */
    private function adviced(){
        //过滤参数
        $ret = $this->checkPostVal();
        if( 1 != $ret){
            ToolModel::goToUrl($ret,__ROOT__."/APP/Advice/index/action/showView/");
            exit;
        }

        //通用户每天只能建言两次
//        if($this->obj->getTodayAdvicedCounts() >= 2){
//            ToolModel::goToUrl('每天只能建言2次!',__ROOT__."/APP/Advice/index/action/showView/");
//            exit;
//        }

        //判断是否已经存在相同的建言了
//        $advice = I('post.textinputAdvice','');
//        if($this->obj->isSameAdvice($advice)){
//            ToolModel::goToUrl('已经有相同内容的建言了，请不要重复提交!',__ROOT__."/APP/Advice/index/action/showView/");
//            exit;
//        }

        //追加图片 20180327
        $ret = D('Common')->doUploadImg(FOLDER_NAME_ADVICE,true);

        //追加建言记录
        if(!$this->obj->addAdvice($ret[0]['thumbPath'],$ret[0]['imgPath'],$_POST)){ //追加图片 20180327
            ToolModel::goToUrl('提交时出现错误，请重新提交！',__ROOT__."/APP/Advice/index/action/showView/");
            exit;
        }

        ToolModel::goToUrl('感谢上传，我们会认真审核',__ROOT__."/APP/Advice/index/action/showView/");
    }

    /**
     * 显示建言献策页面
     */
    private function showView(){

        $this->display('Advice');
        
    }

    /**
     * 显示建言献策主画面
     */
    private function adviceMain(){

        $this->display('AdviceMain');
    }

    /**
     * 显示最新建言画面
     */
    private function adviceShow(){

        //查询通过审核的记录
        $ret = D('Advice')->getAccessInfo();

        if( 0 == $ret){
            //无数据
            $this->assign('noData',true);
        }else if(!$ret){
            //取数据错误
            $this->assign('error',true);
        }else{
            //存在数据
            $this->assign('data',$ret);
        }
        
        $this->display('AdviceShow');
    }

    /**
     * 显示建言献策抽奖画面
     */
    private function adviceScratchcard(){

        //取得建言献策的抽奖次数
        $adviceCount = intval(D('Advice')->getAdviceCount());

        //取得刮刮卡使用次数
        $scratchcardedTimes = intval(D('Scratchcard')->getScratchcardUserCountByID(DATI_GUAGUAKA_EVENT_ID));

        $adviceCount = $adviceCount - $scratchcardedTimes;

        if($adviceCount <= 0){
            $this->assign('noData',true);
        }else{
            $this->assign('count',$adviceCount);
        }

        $this->display('AdviceScratchcard');
    }
    /**
     * 检查传入的参数的正确定
     * @return int|string
     */
    private function checkPostVal(){

        if($_FILES['up_img']['size'] == 0){
            return '必须上传图片';
        }

        $name = $_POST['textinputName'];
        $tel = $_POST['textinputTel'];
        $advice = $_POST['textinputAdvice'];

        if(!isset($name) || !isset($tel) || !isset($advice)){
                return '传参错误';
        }

        if( ('' == strval($name)) ){
            return '姓名不能为空';
        }

        if( (ToolModel::getStrLen(strval($name))) > 8 ){
            return '姓名不能超过哦8个字符';
        }

        if( ('' == strval($advice)) ){
            return '图片描述不能为空';
        }

        if(!is_mobile($tel)){
            return '手机格式错误';
        }

        return 1;



    }
}