<?php
/**
 * Created by PhpStorm.
 * User: wujiayu
 * Date: 2016/11/21
 * Time: 23:48
 */

namespace Admin\Model;
header("Content-Type:text/html; charset=utf-8");

class EventModel {

    private $userName;
    private $weixinID;

    public function __construct(){
        $this->userName = $_SESSION['username'];
        $this->weixinID = $_SESSION['weixinID'];
    }

    /**
     * 取得当前微信公众号设置的事件信息
     * @return bool|mixed
     */
    public function getNowReplyInfo(){
        $where['WEIXIN_ID'] = $this->weixinID;

        $data = M()->table('replyInfo')->where($where)->select();

        if($data === false){
            return false;
        }
        return $data;
    }

    public function getInfoByEvebtText(){

        $where['WEIXIN_ID'] = $this->weixinID;
        $where['event_Text'] = addslashes($_POST["eventText"]);
        
        $data = M()->table('replyInfo')->where($where)->find();
        if($data === false){
            return false;
        }
        return $data;
    }

    /**
     * 新增事件
     * @param $imgUrl 封面图
     * @param $replyUrl 对应的前端网站地址
     * @return bool
     */
    public function addNewReply($imgUrl,$replyUrl){

        $data['WEIXIN_ID'] = $this->weixinID;
        $data['event_Text'] = I('post.eventTypeText'.'');
        $data['reply_intext'] = I('post.reply_intext'.'');
        $data['reply_title'] = I('post.reply_title'.'');
        $data['reply_ImgUrl'] = $imgUrl;
        $data['reply_url'] = $replyUrl;
        $data['reply_description'] = I('post.reply_description'.'');
        $data['reply_content'] = I('post.reply_content'.'');
        $data['record_insertTime'] = date("Y-m-d H:i:s",time());
        $data['status'] = 1;

        $ret = M()->table('replyInfo')->add($data);

        if($ret > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 更新事件
     * @param $imgUrl
     * @return bool
     */
    public function editReply($imgUrl){

        $data['reply_intext'] = I('post.reply_intext'.'');
        $data['reply_title'] = I('post.reply_title'.'');
        $data['reply_ImgUrl'] = $imgUrl;
        $data['reply_description'] = I('post.reply_description'.'');
        $data['reply_content'] = I('post.reply_content'.'');
        $data['record_editTime'] = date("Y-m-d H:i:s",time());

        $where['id'] = I('post.replyID');

        $ret = M()->table('replyInfo')->where($where)->save($data);

        if($ret == 1){
            return true;
        }else{
            return false;
        }

    }

}