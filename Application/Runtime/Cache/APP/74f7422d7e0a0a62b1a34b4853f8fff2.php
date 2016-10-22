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

    
    <link href="/weixin/Public/css/style.css?v=20170101" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="http://apps.bdimg.com/libs/jquerymobile/1.3.0/jquery.mobile-1.3.0.min.css"/>

</head>
<body>
    

    <div data-role="page">
        <div data-role="header" data-position="fixed">
            <tr class = "win-bgMain"><td>
                亲爱的会员:<?php echo ($_SESSION['vipInfo']['Vip_name']); ?></br>
                手机号码:<?php echo ($_SESSION['vipInfo']['Vip_tel']); ?>
            </td></tr>
        </div><!-- /header -->
        <?php if(!empty($billInfoIsNull)): ?><div data-role="content" alin>
                <p>尚未有中奖信息！</p>
            </div>
        <?php else: ?>
            <div data-role="content">
                <table border=1 width = "100%" align="center">
                <form name="customersUpdateForm" method="POST" action="?" data-ajax="false" data-transition="flip">
                    <?php if(is_array($data)): foreach($data as $k=>$vo): ?><tr  class="win-bg"><td><p>
                        第<?php echo ($k + 1); ?>条中奖信息:
                        </tr></td></p>
                        <tr  class="win-bg"><td><p>
                        中奖类型 : <?php echo ($vo["Bill_type"]); ?>
                        </tr></td></p>
                        <tr class="win-bg"><td><p>
                        中奖内容 : <?php echo ($vo["Bill_GoodsName"]); ?>
                        </tr></td></p>
                        <tr class="win-bg"><td><p>
                        中奖SN码 : <?php echo ($vo["Bill_SN"]); ?>
                        </tr></td></p>
                        <tr class="win-bg"><td><p>
                        中奖时间 : <?php echo ($vo["Bill_insertDate"]); ?>
                        </tr></td></p>
                        <tr class="win-bg"><td><p>
                        活动开始日期 : <?php echo ($vo["Bill_goods_beginDate"]); ?>
                        </tr></td></p>
                        <tr class="win-bg"><td><p>
                        活动结束日期 : <?php echo ($vo["Bill_goods_endDate"]); ?>
                        </tr></td></p>
                        <tr class="win-bg"><td><p>
                        奖品兑换截止日期 : <?php echo ($vo["Bill_goods_expirationDate"]); ?>
                        </tr></td></p>
                        <tr><td>
                            </br>
                        </td></tr><?php endforeach; endif; ?>
                </form>
                </table>

            </div><?php endif; ?>
    </div>


    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/flat-ui/2.2.2/js/flat-ui.min.js"></script>
<script src="/weixin/Public/js/wx.js"></script>
<script src="/weixin/Public/js/common.js"></script>
<script src="/weixin/Public/js/function.js"></script>

    
    <script type="text/javascript" src="http://apps.bdimg.com/libs/jquerymobile/1.3.0/jquery.mobile-1.3.0.min.js"></script>


</body>
</html>