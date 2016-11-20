<?php
/**
 * Created by PhpStorm.
 * User: wb-wjy227944
 * Date: 2016/10/14
 * Time: 11:19
 */

namespace APP\Model;
header("Content-Type:text/html; charset=utf-8");

class PhotoWallModel {

	private $openid,$weixinID;

	public function __construct(){
		$this->openid = $_SESSION['openid'];
		$this->weixinID = $_SESSION['weixinID'];
	}

    /**
     * 点赞后累计
     * @param $id
     * @param $num
     * @return bool
     */
    public function addLike($id,$num){
        $data['PHOTOWALL_LIKENUM'] = $num;
        $data['PHOTOWALL_EDITETIME'] = date("Y/m/d H:i:s",time());

        $where['id'] = $id;
        $where['WEIXIN_ID'] = $this->weixinID;

        if(M()->table('photoWall')->where($where)->save($data)){
            return true;
        }
        return false;
    }
    /**
     * 追加新照片墙记录
     * @param $post
     * @param $imgPath
     * @return bool
     */
    public function addPhotoWall($post,$imgPath){

        $nowTime = date("Y-m-d H:i:s",time());

        $data['WEIXIN_ID'] = $this->weixinID;
        $data['PHOTOWALL_OPENID'] = $this->openid;
        $data['PHOTOWALL_NAME'] = strval($post['textinputName']);
        $data['PHOTOWALL_TEL'] = strval($_POST['textinputTel']);
        $data['PHOTOWALL_IMGURL'] = $imgPath;
        $data['PHOTOWALL_LIKENUM'] = 0;
        $data['PHOTOWALL_CREATETIME'] = $nowTime;
        $data['PHOTOWALL_EDITETIME'] = $nowTime;
        $data['PHOTOWALL_ISOK'] = 0;

        if(false === M()->table('photoWall')->add($data)){
            return false;
        }

        return true;
    }

    /**
     * 判断今天是否已经提交过了
     * @return int
     */
    public function isTodayUploaded(){
        $nowDate = date("Y-m-d",time());

        $where = "WEIXIN_ID = $this->weixinID
                AND PHOTOWALL_OPENID = '$this->openid'
                AND DATE_FORMAT( PHOTOWALL_CREATETIME , '%Y-%m-%d' ) = '$nowDate'";

        return M()->table('photoWall')->where($where)->find();
    }

    /**
     * 取得审核通过的照片墙信息
     * @return bool|int
     */
    public function getAccessPhotoWallInfo(){
        $where['WEIXIN_ID'] = $this->weixinID;
        $where['PHOTOWALL_ISOK'] = 1;

        $order = 'id DESC';
        $data = M()->table('photoWall')->where($where)->order($order)->select();

        return ToolModel::doFilterSelect($data);
    }
}