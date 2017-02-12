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

    }