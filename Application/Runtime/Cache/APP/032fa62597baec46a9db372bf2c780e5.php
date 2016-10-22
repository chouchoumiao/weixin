<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">

<meta name="format-detection" content="telephone=no">

<title>会员中心</title>

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<!--图标-->
<!--<link rel="icon" type="image/x-icon" href="/weixin/Public/img/favicon.ico">-->

    
    <link href="//cdn.bootcss.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/flat-ui/2.2.2/css/flat-ui.min.css" rel="stylesheet">

</head>
<body>
    

    <div class="container">
        <input type="text" style="display:none" id="vipId" value=<?php echo ($VipInfo['Vip_id']); ?>>
        <div class="row">
            <div class="col-md-12">
                <br/>
                <div class="demo-type-example">
                    <p>如信息输入错误，修改请联系微信号xiangyu426。关注“路桥发布”微信获取更多<?php echo $weixinName;?>
                        <a href = "http://mp.weixin.qq.com/s?__biz=MzA5MjAwNTg5MA==&mid=202313729&idx=1&sn=771fb385b364df578067e9b3307eba45#rd" id="blue">点我关注</a>
                    </p>
                </div>
                <br/>
                <div id = "baseInfo">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">昵　　称</span>
                            <input type="text" class="form-control input-lg" placeholder=<?php echo ($vipInfo['Vip_name']); ?> disabled="disabled">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">手　　机</span>
                            <input type="text" class="form-control input-lg" placeholder=<?php echo ($vipInfo['Vip_tel']); ?> disabled="disabled">
                        </div>
                    </div>
                    <?php if(!empty($area)): ?><div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">地　　区</span>
                                <input type="text" class="form-control input-lg" placeholder="<?php echo ($vipInfo['Vip_address_text']); ?>" disabled="disabled">
                            </div>
                        </div><?php endif; ?>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo ($config_vipName); ?></span>
                            <input type="text" id="vipIntegral" class="form-control input-lg" placeholder=<?php echo ($vipInfo['Vip_integral']); ?> value="<?php echo ($vipInfo['Vip_integral']); ?>" disabled="disabled">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">印章总数</span>
                            <input type="text" class="form-control input-lg" placeholder=<?php echo ($flowerCount); ?> disabled="disabled">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">剩余印章</span>
                            <input type="text" class="form-control input-lg" placeholder=<?php echo ($nowflowerCount); ?> disabled="disabled">
                        </div>
                    </div>

                    <?php if(!empty($count)): ?><div class="form-group">
                            <button type="button" class="btn btn-primary btn-block" onclick = "location.href='../95_scratchcard/scratchcard.php?openid=<?php echo $openid;?>&weixinID=<?php echo $weixinID?>'">建言献策抽奖<?php echo ($count); ?>次</button>
                        </div><?php endif; ?>

                    <?php if(!empty($noComment)): ?><div class="form-group">
                            <button type="button" class="btn btn-warning btn-block" id = "referrerBtn">点我补登推荐人</button>
                        </div><?php endif; ?>
                    <div class="form-group">
                        <button type="button" class="btn btn-success btn-block" id = "winInfoBtn">查看中奖信息</button>
                    </div>
                    <?php if(empty($area)): ?><div class="form-group">
                            <button type="button" class="btn btn-info btn-block" id = "areaInputBtn">补填地区信息</button>
                        </div><?php endif; ?>
                </div>
                <div id = "referrerInfo" style = "display:none">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">推荐人卡号</span>
                            <input type="number" class="form-control input-lg" id = "referrerID">
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success btn-block" id = "referrerSubmit">提交</button>
                        <button type="button" class="btn btn-block disabled" id = "referrerSubmitDoing" style="display:none">正在提交。。。</button>
                    </div>
                </div>
            </div>

            </br>
        </div>
    </div>


    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/flat-ui/2.2.2/js/flat-ui.min.js"></script>
<script src="/weixin/Public/js/wx.js"></script>
<script src="/weixin/Public/js/common.js"></script>
<script src="/weixin/Public/js/function.js"></script>

    
    <script src="/weixin/Public/js/VipCenter/VipInfoShow.js?v=20170102"></script>


</body>
</html>