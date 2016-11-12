<?php
/**
 * Created by PhpStorm.
 * User: wb-wjy227944
 * Date: 2016/10/14
 * Time: 11:19
 */

namespace APP\Model;
header("Content-Type:text/html; charset=utf-8");

/**
 * 印章相关操作模块
 * Class IphonEventModel
 * @package APP\Model
 */
class IntegralCityModel {

	private $openid,$weixinID;

	public function __construct(){
		$this->openid = $_SESSION['openid'];
		$this->weixinID = $_SESSION['weixinID'];
	}

    /**
     * 取得有效积分商城活动
     * @return bool
     */
    public function getIntegralCityEvent(){

        $where['integralCity_fromDate'] = array('ELT',date("Y-m-d",time()));
        $where['integralCity_endDate'] = array('EGT',date("Y-m-d",time()));
        $where['WEIXIN_ID'] = $this->weixinID;
        $where['integralCity_isDeleted'] = 0;

        $order = 'integralCity_integralNum ASC';

        $data = M()->table('integralCity_config')->where($where)->order($order)->select();

        if(false === $data){
            return false;
        }
        return $data;
    }

    /**
     * 根据传入的商品ID获得该活动的信息
     * @param $id
     * @return bool
     */
    public function getSealInfoByID($id){

        $where['flowerCity_isDeleted'] = 0;
        $where['id'] = $id;
        $where['WEIXIN_ID'] = $this->weixinID;

        $data = M()->table('flowerCity_config')->where($where)->find();

        if(false === $data){
            return false;
        }
        return $data;
    }

    /**
     * 更新库存
     * @param $id
     * @param $count
     * @return bool
     */
    public function updateStockCount($id,$count){

        $data['flowerCity_stockCount'] = $count;
        $data['flowerCity_editTime'] = date("Y-m-d H:i:s",time());

        $where['id'] = $id;
        $where['WEIXIN_ID'] = $this->weixinID;

        if(false === M()->table('flowerCity_config')->where($where)->save($data)){
            return false;
        }

        return true;
    }


}