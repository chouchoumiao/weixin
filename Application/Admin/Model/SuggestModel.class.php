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
     * @param $reply1
     * @param $reply2
     * @param $reply3
     * @param $id
     * @return bool
     */
    public function updateReply($id,$reply1,$reply2,$reply3){

        if( ('' == $reply2) || ('undefined' == $reply2) ){
            $data['reply1'] = $reply1;
            $data['reply_date1'] = date("Y-m-d", time());
            $data['reply_time1'] = date("H:i:s", time());
        }else{
            if( ('' == $reply3) || ('undefined' == $reply3) ){
                $data['reply2'] = $reply2;
                $data['reply_date2'] = date("Y-m-d", time());
                $data['reply_time2'] = date("H:i:s", time());
            }else{
                $data['reply3'] = $reply3;
                $data['reply_date3'] = date("Y-m-d", time());
                $data['reply_time3'] = date("H:i:s", time());
            }
        }

        $where['id'] = $id;

        if(count(M()->table('suggest_info')->where($where)->save($data)) == 1){
            return true;
        }
        return false;


    }

    public function getSuggestDate(){

        $where['flag'] = $_SESSION['flag'];
        $where['WEIXIN_ID'] = $this->weixinID;
        $data = M()->table('suggest_info')
                    ->distinct(true)
                    ->where($where)
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
        $where['flag'] = $_SESSION['flag'];
        $where['WEIXIN_ID'] = $this->weixinID;

        $data = M()->table('suggest_info')->where($where)->order($order)->select();

        if($data){
            return $data;
        }
        return false;
    }
}