<?php

//允许的省份
define('ALLOW_PROVINCE','浙江');

//公众文件夹路径
define('PUBLIC_PATH',$_SERVER['DOCUMENT_ROOT'].'/weixin/Public');

//分享有礼的文件夹名称
define('FOLDER_NAME_FORWARDINGGIFT','ForwardingGift');

//答题刮刮卡的活动ID
define('DATI_GUAGUAKA_EVENT_ID',159);

return array(
	//'配置项'=>'配置值'

    //需要做会员判断的action
    'IS_VIP_ACTION_ARR'=>array(
        'VipCenter',
        'Guaguaka',
    ),

    //中奖的type数据
    'BILL_TYPE_ARR'=>array(
        'INTEGRAL_CITY' => '001',
        'BIG_WHEEL' =>'002',
        'GUAGUAKA' =>'003',
        'SEAL' =>'004',
        'ADVICE' => '005'
    ),

    //中奖的typ名称数据
    'BILL_NAME_ARR'=>array(
        '001' => '积分商城',
        '002' =>'大转盘',
        '003' =>'刮刮卡',
        '004' =>'印章',
        '005' => '建言献策'
    ),

    //地区名称数据
    'AREA_NAME_ARR'=>array(
        0 => '请选择地区',
        1 => '路南街道',
        2 => '路北街道',
        3 => '螺洋街道',
        4 => '桐屿街道',
        5 => '峰江街道',
        6 => '新桥镇',
        7 => '横街镇',
        8 => '蓬街镇',
        9 => '金清镇',
        99 => '其他地区'
    ),

    //文件上传默认大小:5M
    'FILE_SIZE' => 5242880,


    //图片资源可上传的后缀
    'MEDIA_TYPE_ARRAY'=> array(
        'jpg','png','jpeg','gif'
    ),

);