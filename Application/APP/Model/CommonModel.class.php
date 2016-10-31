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
         * 取得建言献策的抽奖次数
         * @return mixed
         */
        public function getAdviceCount(){
            $where['WEIXIN_ID'] = $this->weixinID;
            $where['ADVICE_OPENID'] = $this->openid;
            $where['ADVICE_EVENT'] = 1;

            return M()->table('adviceInfo')->where($where)->count();
        }

        /**
         * 取得答题刮刮卡获得的使用次数
         * @return mixed
         */
        public function getScratchcardUserCount(){
            $where['scratchcard_userOpenid'] = $this->openid;
            $where['WEIXIN_ID'] = $this->weixinID;
            $where['scratchcard_userIsAllow'] = 1;
            $where['scratchcard_id'] = DATI_GUAGUAKA_EVENT_ID;

            $data = M()->table('scratchcard_user')->field('scratchcard_userCount')->where($where)->find();
            if(false === $data){
                return false;
            }

            return $data['scratchcard_userCount'];

        }

        /**
         * 
         * @param $data
         * @return bool
         */
        public function addIphoneEvent($data){
            if(false === M()->table('iphoneEvent')->add($data)){
                return false;
            }

            return true;
        }

        /**
         * 追加积分记录
         * @param $data
         * @return bool
         */
        public function addIntegralRecord($data){

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


    }