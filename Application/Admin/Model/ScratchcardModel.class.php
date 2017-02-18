<?php
/**
 * Created by PhpStorm.
 * User: wujiayu
 * Date: 2016/11/21
 * Time: 23:48
 */

namespace Admin\Model;
header("Content-Type:text/html; charset=utf-8");

class ScratchcardModel {

    private $weixinID;

    public function __construct(){
        $this->weixinID = $_SESSION['weixinID'];
    }

    /**
     * 根据传入的openid取得当前公众号该用户的抽奖次数
     * @param $openid
     * @param $scratchcardID
     * @return bool
     */
    public function getScratchcardUserCountByOpenid($openid,$scratchcardID){

        $where['scratchcard_id'] = $scratchcardID;
        $where['scratchcard_userOpenid'] = $openid;
        $where['WEIXIN_ID'] = $this->weixinID;

        $data = M()->table('scratchcard_user')->field('scratchcard_userCount')->where($where)->find();

        if(false === $data){
            return false;
        }

        return $data['scratchcard_userCount'];
    }

    /**
     * 获取当前公众号的最新有效的刮刮卡ID
     * @return bool
     */
    public function getMaxEventID(){
        $nowDate = date("Y-m-d",time());

        $where['scratchcard_beginDate'] = array('elt',$nowDate);
        $where['scratchcard_endDate'] = array('egt',$nowDate);
        $where['scratchcard_isDeleted'] = 0;
        $where['WEIXIN_ID'] = $this->weixinID;

        $data = M()->table('scratchcard_main')->where($where)->max('scratchcard_id');

        if(false === $data){
            return false;
        }

        return $data;
    }
}