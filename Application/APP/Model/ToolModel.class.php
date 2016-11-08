<?php

/**
 * 验证方法类
 */
namespace APP\Model;

    class ToolModel {

        /**
         * 解决中文多字符问题，改方式将中文认为一个字符
         * @param $str
         * @return int
         */
        static function getStrLen($str){
            preg_match_all('/./us', $str, $match);
            return count($match[0]);
        }

        /**
         * 返回从0开始到指定位数的字符串
         * @param $str
         * @param $len
         * @return string
         * 中文截取
         */
        static function getSubString($str,$len){

            return mb_substr($str,0,$len,'utf-8').'....';
        }

        /**
         * 根据传入的字符串，截取图片地址后返回数组
         * @param $str
         * @return mixed
         */
        static function getImgPath($str){

            $newStr =  str_replace("\"","'",$str);

            $preg = '/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/i';
            preg_match_all($preg, $newStr, $imgArr);
            return $imgArr[1];
        }

        /**
         * 弹出对话框
         * @param $msg
         */
        static function doAlert($msg){
            echo "<script>alert('$msg');</script>";
        }

        /**
         * 错误返回
         * @param $msg
         */
		static function goBack($msg){

            echo "<script>alert('$msg');history.back();</script>";

            exit;
        }

        /**
         * 错误返回
         * @param $msg
         */
        static function goReload($msg){
            echo "<script>alert('$msg');location.reload()</script>";
            exit;
        }

        /**
         * 错误关闭
         * @param $msg
         */
        static function goClose($msg){
            echo "<script>alert('$msg');close()</script>";
            exit;
        }

        /**
         * 错误跳转
         * @param $msg
         * @param $url
         */
        static function goToUrl($msg,$url){
            echo "<script>alert('$msg');location='$url'</script>";
            exit;
        }

        /**
         * 删除指定文件
         * @param $img
         * @return bool
         */
        static function delImg($img){
            if(file_exists($img)){

                if(unlink($img)){
                    return 1;
                }else{
                    return '删除失败';
                }
            }
            return '文件不存在';

        }

        /**
         * 将时间戳转化为正常时间格式
         * @param $data
         * @return bool|string
         */
        static function formartTime($data){
            return date('Y-m-d H:i:s', $data);
        }

        /**
         * 更新session
         */
        static function setNowUserBaseSession(){
            $where['id'] = $_SESSION['uid'];
            $obj = M('m_user')->where($where)->find();

            $_SESSION['username'] = $obj['username'];
            $_SESSION['img']      = $obj['img'];
        }


        /**
         * 清除session
         * 根据传入的name清除指定的额session，不传入则默认清除所有session(退出登录用)
         * @param string $name
         */
        static function clearSession( $name = '' ){

            if( '' == $name){
                if(isset($_SESSION['username'])){
                    unset($_SESSION['username']);
                }

                if(isset($_SESSION['uid'])){
                    unset($_SESSION['uid']);
                }

                if(isset($_SESSION['img'])){
                    unset($_SESSION['img']);
                }

                if(isset($_SESSION['newImg'])){
                    unset($_SESSION['newImg']);
                }

                if(isset($_SESSION['editImg'])){
                    unset($_SESSION['editImg']);
                }

                if(isset($_SESSION['currentUrl'])){
                    unset($_SESSION['currentUrl']);
                }

                if(isset($_SESSION['activeNotice'])){
                    unset($_SESSION['activeNotice']);
                }

                if(isset($_SESSION['activeNoticeCount'])){
                    unset($_SESSION['activeNoticeCount']);
                }
            }else{
                if(isset($_SESSION[$name])){
                    unset($_SESSION[$name]);
                }
            }

        }

        /**
         * 图片上传的一般设置
         * 需传入文件夹的名称 (默认为public/Uploads/+文件夹名称)
         * @param $folderName
         * @return array
         */
        static function imgUploadConfig($folderName){
            $day =  date('Ymd',time());
            //图片上传设置
            $config = array(
                'maxSize'    =>    C('FILE_SIZE'),
                'rootPath'	 =>    './Public',
                'savePath'   =>    '/Uploads/'.$folderName.'/'.$day.'/',
                'saveName'   =>    array('uniqid',rand(1000000,9999999).'_'),
                'exts'       =>    C('MEDIA_TYPE_ARRAY'),
                'autoSub'    =>    false,
                'subName'    =>    array('date','Ymd'),
            );
            return $config;
        }

        /**
         * 上传图片
         * @param $config
         * @return mixed   正确则返回路径名称 错误则返回错误信息
         */
        static function uploadImg($config){

            if (!empty($_FILES)) {

                $upload = new \Think\Upload($config);// 实例化上传类
                $info = $upload->upload();

                //判断是否有图
                $pathName = '';
                if($info){
                    foreach($info as $file){
                        $pathName .= $file['savepath'].$file['savename'];
                    }
                    $retArr['success'] = 1;
                    $retArr['msg'] = $pathName;
                    $retArr['size'] = $file['size'];
                    $retArr['fileName'] = $file['name'];
                    $retArr['saveName'] = $file['savename'];

                    //获得上传的路径地址
                    $retArr['savePath'] = $config['savePath'];

                    //取得不带后缀名的文件名称
                    $imgNameArr = explode('.',$file['savename']);
                    $retArr['imgName'] = $imgNameArr[0];

                    return $retArr;
                }
                else{
                    $retArr['success'] = 0;
                    $retArr['msg'] = $upload->getError();
                    return $retArr;
                }
            }
            return false;
        }

        /**
         * 生成缩略图
         * @param $imgPath
         * @param $thumbPath
         * @param int $width    默认150
         * @param int $height   默认150
         */
        static  function setThumb($imgPath,$thumbPath,$width=150,$height=150){
            //设置缩略图
            $image = new \Think\Image();
            $image->open($imgPath);
            // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
            $image->thumb($width, $height)->save($thumbPath);
        }

        /**
         * 取得当前公众号的基本设置，如果未设置则初始化
         * @param $weixinID
         * @return array|mixed
         */
        static function getConfig($weixinID){

            $where['WEIXIN_ID'] = $weixinID;
            $data = M()->table('ConfigSet')->where($where)->find();
            if(false === $data){
                return array(
                    "CONFIG_INTEGRALINSER" =>0,
                    "CONFIG_INTEGRAL_REFERRER_FOR_NEW_VIP"=>0,
                    "CONFIG_INTEGRALREFERRER"=>0,
                    "CONFIG_INTEGRALSETDAILY"=>0,
                    "CONFIG_DAILYPLUS"=>0,
                    "CONFIG_VIP_NAME"=>'积分'
                );
            }

            return $data;

        }

        /**
         * 根据传入的字符串，判断字符串的长度是否大于传入要求的长度，大于则截取传入长度的字符并加上'...'，否则返回原字符串
         * @param $str
         * @param $len
         * @return string
         */
        static function doLengthUTF8($str,$len){
            $nowLen = self::getStrLen($str);
            if($nowLen > $len){
                return self::getSubString($str,$len);
            }
            return $str;
        }


        /**
         *
         * @param $str
         */
        /**
         * 为了label美观，固定4个中文字符占位，不满四个字符则用日文的空位代替
         * @param $str
         * @return string
         */
        static function do3lenUtf8($str){

            $len = self::getStrLen($str);

            if($len <= 3) {

                switch ($len) {
                    case 1:
                        return '　　　' . $str;
                        break;
                    case 2:
                        return '　　' . $str;
                        break;
                    case 3:
                        return '　' . $str;
                        break;
                }
            }
            return self::getSubString($str,3);
        }

        /**
         * 返回json结果
         * @param $flag
         * @param $msg
         */
        static function jsonReturn($flag,$msg){
            $arr['success'] = $flag;
            $arr['msg'] = $msg;
            echo json_encode($arr);
            exit;
        }

        /**
         * 传入完整的手机号,返回隐藏中间五位号码的手机号
         * @param $tel
         * @return string
         */
        static function hideTel($tel){
            return substr($tel,0,3)."*****".substr($tel,8,3);
        }
        
        /**
         * 生成兑换码
         * @param $pre
         * @return string
         */
        static function snMaker($pre) {
            $date = date('Ymd');
            $rand = rand(1000,9999);
            $time = mb_substr(time(), 5, 5, 'utf-8');
            $serialNumber = $time.$pre.$date.$rand;
            return $serialNumber;
        }

    }