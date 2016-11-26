<?php
/**
 * Created by PhpStorm.
 * User: wb-wjy227944
 * Date: 2016/10/14
 * Time: 11:19
 */

namespace APP\Model;
header("Content-Type:text/html; charset=utf-8");

class SuggestModel {

	private $openid,$weixinID;

	public function __construct(){
		$this->openid = $_SESSION['openid'];
		$this->weixinID = $_SESSION['weixinID'];
	}

    /**
     * 判断当前用户是否已经存在相同的建议了
     * @param $content
     * @return bool
     */
    public function isSameSuggest($content){

        $where['content'] = $content;
        $where['WEIXIN_ID'] = $this->weixinID;
        $where['openid'] = $this->openid;

        if(M()->table('suggest_info')->where($where)->find()){
            return true;
        }
        return false;
    }

    /**
     * 获得当前用户今天建言的次数
     * @return mixed
     */
    public function getTodaySuggestdCounts(){

        $nowDate = date("Y-m-d",time());

        $where = "WEIXIN_ID = $this->weixinID
                AND openid = '$this->openid'
                AND DATE_FORMAT( create_time , '%Y-%m-%d' ) = '$nowDate'";

        return intval(M()->table('suggest_info')->where($where)->count());
    }

    /**
     * 追加记录
     * @return bool
     */
    public function addSuggest()
    {
        $nowTime = date("Y-m-d H:i:s", time());
        $data['WEIXIN_ID'] = $this->weixinID;
        $data['openid'] = $this->openid;
        $data['name'] = I('post.name','');
        $data['tel'] = I('post.tel','');
        $data['content'] = I('post.content','');
        $data['reply'] = '';
        $data['create_time'] = $nowTime;
        $data['reply_time'] = $nowTime;
        $data['flag'] = 0;
        $data['is_event'] = 0;

        if (false === M()->table('suggest_info')->add($data)) {
            return false;
        }

        return true;
    }

}