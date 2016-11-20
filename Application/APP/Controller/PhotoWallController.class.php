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
class PhotoWallController extends CommonController {
    private $obj;

    public function index(){

        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if(isset($action) && ('' != $action)){
            $this->obj = D('PhotoWall');
            switch ($action){
                case 'photoWall':
                    $this->photoWall();
                    break;
                case 'photoWallMain':
                    $this->photoWallMain();
                    break;
                case 'photoWallShow':
                    $this->photoWallShow();
                    break;
                case 'adviceScratchcard':
                    $this->adviceScratchcard();
                    break;
                case 'photoWallData':
                    $this->photoWallData();
                    break;
            }
        }

    }

    /**
     * 上传图片逻辑
     */
    private function photoWallData(){

        if($this->obj->isTodayUploaded()){
            ToolModel::goBack('今天你已经提交过了,明天再来吧!');
            exit;
        }

        //过滤参数
        $ret = $this->checkPostVal();
        if( 1 != $ret){
            ToolModel::goBack($ret);
            exit;
        }

        //图片上传
        //设置删除图片的相关配置项
        $ret = D('Common')->doUploadImg(FOLDER_NAME_PHOTOWALL);

        //追加数据库记录
        if(!$this->obj->addPhotoWall($_POST,$ret['imgPath'])){
            ToolModel::goBack('提交时出现错误，请重新提交!');
            exit;
        }

        ToolModel::goBack('照片提交成功，请耐心等待审核！!');

    }

    /**
     * 显示照片墙页面
     */
    private function photoWall(){
        $this->display('PhotoWall');
        
    }

    /**
     * 显示照片墙主画面
     */
    private function photoWallMain(){

        $this->display('PhotoWallMain');
    }

    /**
     * 显示通过审核的照片墙画面
     */
    private function photoWallShow(){

        //查询通过审核的记录
        $ret = $this->obj->getAccessPhotoWallInfo();
        if( 0 == $ret){
            //无数据
            $this->assign('noData',true);
        }else if(!$ret){
            //取数据错误
            $this->assign('error',true);
        }else{
            //存在数据

            //修改图片为能显示的url格式
            for($i=0;$i<count($ret);$i++){
                $ret[$i]['PHOTOWALL_IMGURL'] = PUBIC_URL_PATH.$ret[$i]['PHOTOWALL_IMGURL'];
            }

            $this->assign('data',$ret);
        }
        
        $this->display('PhotoWallShow');
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


        $name = $_POST['textinputName'];
        $tel = $_POST['textinputTel'];

        if(!isset($name) || !isset($tel) ){
                return '传参错误';
        }

        if( ('' == strval($name)) ){
            return '姓名不能为空';
        }

        if( (ToolModel::getStrLen(strval($name))) > 8 ){
            return '姓名不能超过哦8个字符';
        }

        if(!is_mobile($tel)){
            return '手机格式错误';
        }

        return 1;



    }
}