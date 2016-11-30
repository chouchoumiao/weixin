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

        $where['WEIXIN_ID'] = $this->weixinID;
        $where['openid'] = $this->openid;
        $where['create_date'] = date("Y-m-d",time());

        return intval(M()->table('suggest_info')->where($where)->count());
    }

    /**
     * 追加记录
     * @return bool
     */
    public function addSuggest()
    {
        $nowTime = date("H:i:s", time());
        $nowDate = date("Y-m-d", time());
        $data['WEIXIN_ID'] = $this->weixinID;
        $data['openid'] = $this->openid;
        $data['name'] = I('post.name','');
        $data['tel'] = I('post.tel','');
        $data['content'] = I('post.content','');
        $data['reply'] = '';
        $data['create_time'] = $nowTime;
        $data['create_date'] = $nowDate;
        $data['reply_time'] = $nowTime;
        $data['reply_date'] = $nowDate;
        $data['flag'] = 0;
        $data['is_event'] = 0;

        if (false === M()->table('suggest_info')->add($data)) {
            return false;
        }

        return true;
    }

    /**
     * 取得当前用户的建议回复信息
     * @return bool
     */
    public function getReplyInfo(){
        $where['openid'] = $this->openid;
        $where['WEIXIN_ID'] = $this->weixinID;
        $where['reply'] = array('neq','');

        $data = M()->table('suggest_info')->where($where)->select();
        if($data && count($data)>0){
            return $data;
        }
        return false;
    }

}