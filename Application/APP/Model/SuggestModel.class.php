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
     * @param $flag
     * @return int
     */
    public function getTodaySuggestdCounts($flag){

        $where['WEIXIN_ID'] = $this->weixinID;
        $where['openid'] = $this->openid;
        $where['flag'] = $flag;
        $where['create_date'] = date("Y-m-d",time());

        return intval(M()->table('suggest_info')->where($where)->count());
    }

    /**
     * 追加记录
     * @param $flag          区长还是书记flag
     * @param $imgPathJson   原始图片路径的Json字符串
     * @param $thumbPathJson 缩略图路径的Json字符串
     * @return bool
     */
    public function addSuggest($flag,$imgPathJson,$thumbPathJson)
    {
        $nowTime = date("H:i:s", time());
        $nowDate = date("Y-m-d", time());
        $data['WEIXIN_ID'] = $this->weixinID;
        $data['openid'] = $this->openid;
        $data['name'] = I('post.name','');
        $data['tel'] = I('post.tel','');
        $data['content'] = I('post.content','');
        $data['imgPath'] = $imgPathJson;
        $data['thumbPath'] = $thumbPathJson;

        $data['create_time'] = $nowTime;
        $data['create_date'] = $nowDate;
        $data['reply1'] = '';
        $data['reply2'] = '';
        $data['reply3'] = '';
        $data['reply_time1'] = null;
        $data['reply_date1'] = null;
        $data['reply_time2'] = null;
        $data['reply_date2'] = null;
        $data['reply_time3'] = null;
        $data['reply_date3'] = null;
        $data['flag'] = $flag;
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
        $where['reply1'] = array('neq','');

        $data = M()->table('suggest_info')->where($where)->select();
        if($data && count($data)>0){
            return $data;
        }
        return false;
    }

}