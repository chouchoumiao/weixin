<?php
/**
 * Created by PhpStorm.
 * User: wujiayu
 * Date: 2017/2/19
 * Time: 12:49
 */

class CommonToolModel {
    /**
     * 上传拖图片
     * @param $folderName
     * @param bool $isThumb 是否需要生成缩略图,默认不生成
     * @return mixed
     */
    static function doUploadImg($folderName,$isThumb = false){
        $config = self::imgUploadConfig($folderName);
        //上传文件
        $retArrs = self::uploadImg($config);

        if(!$retArrs){
            self::goBack('上传图片错误,请重新上传');
            exit;
        }

        foreach ($retArrs as $key => $retArr){
            if($retArr['success'] == 0){
                self::goBack('上传第'.$key.'个图片错误,错误原因:'.$retArr['msg'].'请重新上传!');
                exit;
            }
            $imgPath = PUBLIC_PATH.'/'.$retArr['msg'];

            $ret[$key]['imgPath'] = $retArr['msg'];
            //判断是否需要生成缩略图
            if($isThumb){
                //设置缩略图的保存地址与原图path一致
                $thumbPath = PUBLIC_PATH.'/'.$retArr['savePath'].$retArr['imgName'].'_thumb.jpg';

                self::setThumb($imgPath,$thumbPath);

                $ret[$key]['thumbPath'] = $retArr['savePath'].$retArr['imgName'].'_thumb.jpg';
            }

        }
        return $ret;
    }

    /**
     * 图片上传的一般设置
     * 需传入文件夹的名称 (默认为public/Uploads/+文件夹名称)
     * @param $folderName
     * @return array
     */
     private function imgUploadConfig($folderName){

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
    private function uploadImg($config){

        if (!empty($_FILES)) {

            $upload = new \Think\Upload($config);// 实例化上传类
            $info = $upload->upload();
            //判断是否有图
            if($info){
                //单张图片
                if(count($info) == 1){
                    foreach($info as $file){
                        $pathName .= $file['savepath'].$file['savename'];
                    }
                    $retArr[0]['success'] = 1;
                    $retArr[0]['msg'] = $pathName;
                    $retArr[0]['size'] = $file['size'];
                    $retArr[0]['fileName'] = $file['name'];
                    $retArr[0]['saveName'] = $file['savename'];

                    //获得上传的路径地址
                    $retArr[0]['savePath'] = $config['savePath'];

                    //取得不带后缀名的文件名称
                    $imgNameArr = explode('.',$file['savename']);
                    $retArr[0]['imgName'] = $imgNameArr[0];

                }else{
                    foreach($info as $key => $file){

                        $retArr[$key]['success'] = 1;
                        $retArr[$key]['msg'] = $file['savepath'].$file['savename'];
                        $retArr[$key]['size'] = $file['size'];
                        $retArr[$key]['fileName'] = $file['name'];
                        $retArr[$key]['saveName'] = $file['savename'];

                        //获得上传的路径地址
                        $retArr[$key]['savePath'] = $config['savePath'];

                        //取得不带后缀名的文件名称
                        $imgNameArr = explode('.',$file['savename']);
                        $retArr[$key]['imgName'] = $imgNameArr[0];
                    }
                }

                return $retArr;
            }else{
                $retArr['success'] = 0;
                $retArr['msg'] = $upload->getError();
                return $retArr;
            }
        }
        return false;
    }

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
     * 生成缩略图
     * @param $imgPath
     * @param $thumbPath
     * @param int $width    默认150
     * @param int $height   默认150
     */
    private  function setThumb($imgPath,$thumbPath,$width=150,$height=150){
        //设置缩略图
        $image = new \Think\Image();
        $image->open($imgPath);
        // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
        $image->thumb($width, $height)->save($thumbPath);
    }
}
