<?php
/**
 * Created by PhpStorm.
 * User: wb-wjy227944
 * Date: 2016/10/14
 * Time: 11:19
 */

namespace APP\Model;
header("Content-Type:text/html; charset=utf-8");

class AdviceModel {

	private $openid,$weixinID;

	public function __construct(){
		$this->openid = $_SESSION['openid'];
		$this->weixinID = $_SESSION['weixinID'];
        $this->obj = M()->table('adviceInfo');

	}

	/**
	 * 判断当前用户的建言献策的剩余次数
	 * @return bool
	 */
	public function getAdviceCount(){

		$where['WEIXIN_ID'] = $this->weixinID;
		$where['ADVICE_OPENID'] = $this->openid;
		$where['ADVICE_EVENT'] = 1;

		$count = M()->table('adviceInfo')->where($where)->count();

		if(false === $count){
			return false;
		}
		return $count;
	}

    /**
     * 判断当前用户是否已经存在相同的建议了
     * @param $advice
     * @return bool
     */
    public function isSameAdvice($advice){

        $where['ADVICE_ADVICE'] = $advice;
        $where['WEIXIN_ID'] = $this->weixinID;
        $where['ADVICE_OPENID'] = $this->openid;

        if(M()->table('adviceInfo')->where($where)->find()){
            return true;
        }
        return false;
    }

    /**
     * 获得当前用户今天建言的次数
     * @return mixed
     */
    public function getTodayAdvicedCounts(){

        $nowDate = date("Y-m-d",time());

        $where = "WEIXIN_ID = $this->weixinID
                AND ADVICE_OPENID = '$this->openid'
                AND DATE_FORMAT( ADVICE_CREATETIME , '%Y-%m-%d' ) = '$nowDate'";

        return intval(M()->table('adviceInfo')->where($where)->count());
    }

    /**
     * 追加记录
     * @param $post
     * @return bool
     */
    public function addAdvice($post)
    {
        $nowTime = date("Y-m-d H:i:s", time());
        $data['WEIXIN_ID'] = $this->weixinID;
        $data['ADVICE_OPENID'] = $this->openid;
        $data['ADVICE_NAME'] = strval($post['textinputName']);
        $data['ADVICE_TEL'] = strval($post['textinputTel']);
        $data['ADVICE_ADVICE'] = strval($post['textinputAdvice']);
        $data['ADVICE_REPLY'] = '';
        $data['ADVICE_CREATETIME'] = $nowTime;
        $data['ADVICE_EDITETIME'] = $nowTime;
        $data['ADVICE_REPLYTIME'] = $nowTime;
        $data['ADVICE_ISOK'] = 0;
        $data['ADVICE_EVENT'] = 0;

        if (false === M()->table('adviceInfo')->add($data)) {
            return false;
        }

        return true;
    }

    /**
     * 取得通过记录的数据
     * @return bool|int
     */
    public function getAccessInfo(){
        $where['ADVICE_ISOK'] = array('in','1,3');
        $where['WEIXIN_ID'] = $this->weixinID;

        $order = 'ADVICE_EDITETIME DESC';

        $data = M()->table('adviceInfo')->where($where)->order($order)->select();

        return ToolModel::doFilterSelect($data);
    }

}