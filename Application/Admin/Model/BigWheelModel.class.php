<?php
/**
 * Created by PhpStorm.
 * User: wujiayu
 * Date: 2016/11/21
 * Time: 23:48
 */

namespace Admin\Model;
header("Content-Type:text/html; charset=utf-8");

class BigWheelModel {

    private $weixinID;

    public function __construct(){
        $this->weixinID = $_SESSION['weixinID'];
    }

    /**
     * 取得所有大转盘的信息总数
     * @return bool|int
     */
    public function getBigWheelCount(){

        $where['WEIXIN_ID'] = $this->weixinID;
        $where['bigWheel_isDeleted'] = 0;

        $data = M()->table('bigWheel_main')->where($where)->count();
        if(false === $data){
            return false;
        }
        return intval($data);

    }

    /**
     * 取得所有大转盘的信息
     * @param $limit
     * @return bool
     */
    public function getAllBigWheelInfo( $limit ){

        $where['WEIXIN_ID'] = $this->weixinID;
        $where['bigWheel_isDeleted'] = 0;
        $order = 'bigWheel_id DESC';
        $data = M()->table('bigWheel_main')->where($where)->limit($limit)->order($order)->select();

        if(false === $data){
            return false;
        }
        return $data;

    }

    /**
     * 根据传入的ID查询该ID的大转盘信息
     * @param $bigWheelID
     * @return bool|mixed
     */
    public function getBigWheelInfoByID( $bigWheelID ){

        $where['WEIXIN_ID'] = $this->weixinID;
        $where['bigWheel_isDeleted'] = 0;
        $where['bigWheel_id'] = $bigWheelID;

        $data = M()->table('setEventForAdmin')->where($where)->find();
        if(false === $data){
            return false;
        }
        return $data;

    }

    /**
     * 根据传入的ID取得该ID的的主表信息
     * @param $bigWheelID
     * @return bool|mixed
     */
    public function getMainBigWheelInfoByID( $bigWheelID ){

        $where['bigWheel_isDeleted' ] = 0;
        $where['WEIXIN_ID' ] = $this->weixinID;
        $where['bigWheel_id' ] = $bigWheelID;

        $data = M()->table('bigWheel_main')->where($where)->find();
        if(false === $data){
            return false;
        }
        return $data;
    }

    /**
     * 根据传入的数据添加新信息
     * @param $data
     * @return bool
     */
    public function insertMainBigWheelInfo( $data ){

        $data['WEIXIN_ID'] = $this->weixinID;

        $ret = M()->table('bigWheel_main')->add($data);

        if($ret > 0){
            return true;
        }else{
            return false;
        }
    }

    public function UpdateMainBigWheelInfo( $data, $bigWheelID ){
        $where['WEIXIN_ID'] = $this->weixinID;
        $where['bigWheel_id'] = $bigWheelID;

        $ret = M()->table('bigWheel_main')->where($where)->save($data);

        if($ret == 1){
            return true;
        }else{
            return false;
        }

    }

}