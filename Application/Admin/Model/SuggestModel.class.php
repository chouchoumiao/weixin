<?php
/**
 * Created by PhpStorm.
 * User: wujiayu
 * Date: 2016/11/21
 * Time: 23:48
 */

namespace Admin\Model;
header("Content-Type:text/html; charset=utf-8");

class SuggestModel {

    private $weixinID;
    private $name,$tel,$suggest,$createTime;

    public function __construct(){
        $this->weixinID = $_SESSION['weixinID'];
    }

    /**
     * 根据传入的id删除建议
     * @param $id
     * @return bool
     */
    public function deleteSuggest($id){
        $where['id'] = $id;

        if( M()->table('suggest_info')->where($where)->delete() == 1){
            return true;
        }
        return false;
    }

    /**
     * 对用户提交的建议进行回复
     * @param $reply
     * @param $id
     * @return bool
     */
    public function updateReply($id,$reply){
        $data['reply'] = $reply;
        $data['reply_date'] = date("Y-m-d", time());
        $data['reply_time'] = date("H:i:s", time());

        $where['id'] = $id;

        if(count(M()->table('suggest_info')->where($where)->save($data)) == 1){
            return true;
        }
        return false;


    }

    public function getSuggestDate(){

        $data = M()->table('suggest_info')
                    ->distinct(true)
                    ->order('create_date Desc')
                    ->field('create_date')
                    ->select();

        if($data){
            return $data;
        }
        return false;
    }

    /**
     * 取得建议所有信息
     * @param $date 提交日期
     * @return bool
     */
    public function getSuggestInfoByDate($date){

        $order = 'create_time Desc';
        $where['create_date'] = $date;
        $where['WEIXIN_ID'] = $this->weixinID;

        $data = M()->table('suggest_info')->where($where)->order($order)->select();

        if($data){
            return $data;
        }
        return false;
    }
}