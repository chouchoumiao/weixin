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
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div id = "baseInfo">
                    <br />
                    <div class="alert alert-info" id = "baseTitle">
                        <p class="text-primary">请选择您的地区信息<br/>　如有错误请联系公众号管理员</p>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">昵　称</span>
                            <input type="text" class="form-control input-lg" readonly value="<?php echo ($theVip['Vip_name']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">总积分</span>
                            <input type="text" class="form-control input-lg" readonly value="<?php echo ($theVip['Vip_integral']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">时　间</span>
                            <input type="text" class="form-control input-lg" readonly value="<?php echo ($theVip['Vip_createtime']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">推荐人</span>
                            <input type="text" class="form-control input-lg" readonly value="<?php echo ($theVip['Vip_comment_text']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <select class="form-control select select-warning" id = "adressSelect" data-toggle="select">
                            <option value="0">请选择地区</option>
                            <option value="1">路南街道</option>
                            <option value="2">路北街道</option>
                            <option value="3">螺洋街道</option>
                            <option value="4">桐屿街道</option>
                            <option value="5">峰江街道</option>
                            <option value="6">新桥镇</option>
                            <option value="7">横街镇</option>
                            <option value="8">蓬街镇</option>
                            <option value="9">金清镇</option>
                            <option value="99">其他地区</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-info btn-block" id = "referrerSubmit">提交信息</button>
                </div>
                <button type="button" class="btn btn-block disabled" id = "referrerSubmitDoing" style="display:none">正在提交。。。</button>
                <br/>
                <div id="myMsg" class="alert alert-danger" style="display:none">
                    <a href="#" class="alert-link"></a>
                </div>
                <div id="myOKMsg" class="alert alert-success"  style="display:none" ></div>
                <button type="button" class="btn btn-primary btn-block" id = "OKBtn" style="display:none" >点击进入会员中心</button>
            </div>
        </div>
    </div>


    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/flat-ui/2.2.2/js/flat-ui.min.js"></script>
<script src="/weixin/Public/js/wx.js"></script>
<script src="/weixin/Public/js/common.js"></script>
<script src="/weixin/Public/js/function.js"></script>

    
    <script type="text/javascript" src="/weixin/Public/js/application.js"></script>
    <script type="text/javascript" src="/weixin/Public/js/VipCenter/VipAddress.js?v=20170107"></script>


</body>
</html>