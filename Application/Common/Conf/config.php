<?php
define('PUBIC_URL_PATH','http://'.$_SERVER['HTTP_HOST'].'/weixin/Public');
//json返回的成功失败参数
define('JSON_ERROR',-1);
define('JSON_SUCCESS',1);

//区长书记的flag
define('QUZHANG',1);
define('SHUJI',2);

define('PAGE_SHOW_COUNT_10',10);

return array(


	//'配置项'=>'配置值'

    /********************数据库配置**************************/
    'DB_TYPE' => 'mysql',
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'app_zglqxww',
//    'DB_USER' => 'wujiayu',
//    'DB_PWD' => '84112326Wu',
    'DB_USER' => 'root',
    'DB_PWD' => '84112326Wu',
    'DB_PORT' => 3306,
    'DB_PREFIX' => '',
    'DB_CHARSET' => 'utf8',
    /********************数据库配置**************************/

    //数据库中的字段分大小写
    'DB_PARAMS' => array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),


//    'SHOW_PAGE_TRACE' =>true,
);