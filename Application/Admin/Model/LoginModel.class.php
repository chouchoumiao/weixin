<?php
/**
 * Created by PhpStorm.
 * User: wujiayu
 * Date: 2016/11/21
 * Time: 23:48
 */

namespace Admin\Model;
header("Content-Type:text/html; charset=utf-8");

class LoginModel {

    private $userName,$userPwd;

    public function __construct(){
    }

    /**
     * 登录验证
     * @param $userName 用户名
     * @param $userPwd 密码
     * @return bool
     */
    public function checkLogin($userName,$userPwd){
        $where['username'] = $userName;
        $where['password'] = md5($userPwd);
        $where['isdeleted'] = 0;

        $data = M()->table('AdminUser')->where($where)->find();
        
        if($data){
            $this->userName = $userName;
            $this->userPwd = $userPwd;
            return $data;

        }
        return false;

    }
}