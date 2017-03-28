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

    public function getInfoByEvebtText(){

        $where['WEIXIN_ID'] = $this->weixinID;
        $where['event_Text'] = addslashes($_POST["eventText"]);
        
        $data = M()->table('replyInfo')->where($where)->find();
        if($data === false){
            return false;
        }
        return $data;

    }

}