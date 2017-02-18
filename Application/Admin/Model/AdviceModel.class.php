<?php
/**
 * Created by PhpStorm.
 * User: wujiayu
 * Date: 2016/11/21
 * Time: 23:48
 */

namespace Admin\Model;
header("Content-Type:text/html; charset=utf-8");

class AdviceModel {

    private $weixinID;

    public function __construct(){
        $this->weixinID = $_SESSION['weixinID'];
    }

    /**
     * 取得当前公众号的所有建言信息的总数
     * @return mixed
     */
    public function getAdviceCunt(){
        $where['WEIXIN_ID'] = $this->weixinID;
        $data = M()->table('adviceInfo')->where($where)->count();
        if(false === $data){
            return false;
        }
        return intval($data);
    }


    /**
     * 获得当前公众号的对应Limit条数的建言信息
     * @param $limit
     * @return bool
     */
    public function getAllAdviceInfo($limit){
        $where['WEIXIN_ID'] = $this->weixinID;
        $order = 'id DESC';
        $data = M()->table('adviceInfo')->where($where)->limit($limit)->order($order)->select();

        if(false === $data){
            return false;
        }
        return $data;
    }

    /**
     * 获取Vip的总可刮奖次数
     * @param $openid
     * @return bool
     */
    public function getAdviceCountByOpenid($openid){

        $where['WEIXIN_ID'] = $this->weixinID;
        $where['ADVICE_OPENID'] = $openid;
        $where['ADVICE_EVENT'] = 1;

        $data = M()->table('adviceInfo')->where($where)->count();

        if(false === $data){
            return false;
        }
        return intval($data);

    }

    /**
     * 根据传入的adviceID获取对象的检验献策信息
     * @param $adviceID
     * @return bool
     */
    public function getTheAdviceInfo($adviceID){

        $where['id'] = $adviceID;
        $where['WEIXIN_ID'] = $this->weixinID;

        $data = M()->table('adviceInfo')->where($where)->find();
        if(false === $data){
            return false;
        }

        return $data;
    }

    /**
     * 根据传入的ID和信息更新
     * @param $adviceID
     * @param $data
     * @return bool
     */
    public function updateAdviceInfo($adviceID,$data){
        $where['id'] = $adviceID;
        $where['WEIXIN_ID'] = $this->weixinID;

        if(M()->table('adviceInfo')->where($where)->save($data)> 0){
            return true;
        }
        return false;
    }
}