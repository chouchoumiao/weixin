<?php
/**
 * Created by PhpStorm.
 * User: wujiayu
 * Date: 2016/11/21
 * Time: 23:48
 */

namespace Admin\Model;
header("Content-Type:text/html; charset=utf-8");

class UserModel {

    private $userName;
    private $weixinID;


    public function __construct(){
        $this->userName = $_SESSION['username'];
        $this->weixinID = $_SESSION['weixinID'];
    }

    /**
     * 后台修改密码
     * @return bool
     */
    public function updatePwd(){

        //取得set页面传递过来的数据

        $data['password'] = md5(strval(I('post.newPass')));
        $data['loginTime'] = date("Y-m-d H:i:s",time());
        $data['login_ip'] = GetIP();

        //login_count没有做

        $where['username'] = $this->userName;
        if( 1 === M()->table('adminUser')->where($where)->save($data)){
            return true;
        }

        return false;

    }
}