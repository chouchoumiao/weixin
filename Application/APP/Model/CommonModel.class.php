<?php

/**
 * 共用方法类
 */
namespace APP\Model;

    class CommonModel {

        private $openid,$weixinID;

        public function __construct(){
            $this->openid = $_SESSION['openid'];
            $this->weixinID = $_SESSION['weixinID'];
        }


        /**
         * 根据传入的微信ID得到该公众号信息，最外主页面用
         * @param $id
         * @return bool|mixed
         */
        public function getAdminInfoByWeixinID($id){
            $where['weixinStatus'] = 1;
            $where['id'] = $id;

            $data =  M()->table('adminToWeiID')->where($where)->find();

            if(false === $data){
                return false;
            }

            return $data;
        }

        /**
     * 获得该微信公众号的基本设置
     * @return array|mixed
     */
        public function getCon(){
            if( (!isset($_SESSION['config'])) || ('' == $_SESSION['config']) ){
                $config = ToolModel::getConfig($this->weixinID);
                $_SESSION['config'] = $config;
            }else{
                $config = $_SESSION['config'];
            }

            return $config;
        }

        /**
         * 获得所有的印章数
         * @param $vipID
         * @return mixed
         */
        public function getAllSealCount($vipID){

            $where['ipE_referee_vipID'] = $vipID;

            //印章总数
            return M()->table('iphoneEvent')->where($where)->count();
        }

        /**
         * 追加印章记录
         * @param $ipE_name
         * @param $ipE_sex
         * @param $ipE_tel
         * @param $referrerID
         * @return bool
         */
        public function addIphoneEvent($ipE_name,$ipE_sex,$ipE_tel,$referrerID){

            $data['ipE_openid'] = $this->openid;
            $data['WEIXIN_ID'] = $this->weixinID;

            $data['ipE_name'] = $ipE_name;
            $data['ipE_sex'] = $ipE_sex;
            $data['ipE_tel'] = $ipE_tel;
            $data['ipE_referee_vipID'] = $referrerID;
            $data['ipE_insertTime'] = date("Y-m-d H:i:s",time());

            if(false === M()->table('iphoneEvent')->add($data)){
                return false;
            }

            return true;
        }

        /**
         * 根据传入的信息追加信息(分值变化时候)
         * @param $openid
         * @param $event
         * @param $oldIntegral
         * @param $plusIntegral
         * @return bool
         */

        public function addIntegralRecord($openid,$event,$oldIntegral,$plusIntegral){

            $data['openid'] = $openid;
            $data['event'] = $event;
            $data['totalIntegral'] = $oldIntegral;
            $data['integral'] = $plusIntegral;
            $data['insertTime'] = date("Y-m-d H:i:s",time());

            if( false === M()->table('integralRecord')->add($data)){
                return false;
            }

            return true;

            //追加积分变动时写入记录表中 功能

        }


        public function getCountBySignidText($signidText){

            $where['WEIXIN_ID'] = $this->weixinID;
            $where['dailyCode'] = $signidText;
            $where['flag'] = 1;

            $count = M()->table('vipDailySet')->where($where)->count();

            if(false === $count){
                return false;
            }
            return intval($count);
        }

        /**
         * 根据传入的weixinID获得当前公众号的图文设置信息
         * @return bool|int
         */
        public function getReplyInfo(){
            $where['WEIXIN_ID'] = $_SESSION['weixinID'];

            return ToolModel::doFilterSelect(M()->table('replyInfo')->where($where)->select());

        }

        /**
         * 上传拖图片
         * @param $folderName
         * @param bool $isThumb 是否需要生成缩略图,默认不生成
         * @return mixed
         */
        public function doUploadImg($folderName,$isThumb = false){
            $config = ToolModel::imgUploadConfig($folderName);
            //上传文件
            $retArrs = ToolModel::uploadImg($config);

            //获取不到图片信息
            if(false === $retArrs){
                ToolModel::goBack('上传功能不可用，请重试');
                exit;
            }else{
                //上传多个图片时，如果出错则显示错误
                foreach ($retArrs as $key => $retArr){
                    if($retArr['success'] == 0){
                        ToolModel::goBack('上传第'.$key.'个图片错误,错误原因:'.$retArr['msg'].'请重新上传!');
                        exit;
                    }
                    $imgPath = PUBLIC_PATH.'/'.$retArr['msg'];

                    $ret[$key]['imgPath'] = $retArr['msg'];
                    //判断是否需要生成缩略图
                    if($isThumb){
                        //设置缩略图的保存地址与原图path一致
                        $thumbPath = PUBLIC_PATH.'/'.$retArr['savePath'].$retArr['imgName'].'_thumb.jpg';

                        ToolModel::setThumb($imgPath,$thumbPath,750,750);

                        $ret[$key]['thumbPath'] = $retArr['savePath'].$retArr['imgName'].'_thumb.jpg';
                    }

                }
                return $ret;
            }


        }
    }