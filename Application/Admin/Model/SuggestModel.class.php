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