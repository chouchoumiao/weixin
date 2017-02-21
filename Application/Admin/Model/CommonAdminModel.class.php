<?php

/**
 * 共用方法类
 */
namespace Admin\Model;

    use APP\Model\ToolModel;

    class CommonAdminModel {

        public function __construct(){
        }

        /**
         * 获得该微信公众号的基本设置(后台用)
         * @param $weixinID
         * @return array|mixed
         */
        public function getAdminCon($weixinID){
            if( (!isset($_SESSION['adminConfig'])) || ('' == $_SESSION['adminConfig']) ){
                $config = ToolModel::getConfig($weixinID);
                $_SESSION['adminConfig'] = $config;
            }else{
                $config = $_SESSION['adminConfig'];
            }

            return $config;
        }

        /**
         * 后台上传拖图片
         * @param $folderName
         * @param bool $isThumb 是否需要生成缩略图,默认不生成
         * @return mixed
         */
        public function doAdminUploadImg($folderName,$isThumb = false){
            $config = ToolModel::imgUploadConfig($folderName);
            //上传文件
            $retArrs = ToolModel::uploadImg($config);

            if(!$retArrs){
                ToolModel::goBack('上传图片错误,请重新上传');
                exit;
            }

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

                    ToolModel::setThumb($imgPath,$thumbPath);

                    $ret[$key]['thumbPath'] = $retArr['savePath'].$retArr['imgName'].'_thumb.jpg';
                }

            }
            return $ret;
        }

    }