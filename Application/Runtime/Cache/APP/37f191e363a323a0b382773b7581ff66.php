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

    
    <link href="/weixin/Public/css/style.css" rel="stylesheet" type="text/css">
    <link href="/weixin/Public/css/footer.css" rel="stylesheet" type="text/css">

</head>
<body>
    
    <div id="mappContainer">
        <section id="card_ctn">
            <div class="bk1"></div>
            <div class="cont">
                <div class="card">
                    <div class="front">
                        <figure class="fg" style="background-image:url(/weixin/Public/img/VipCenter/vipCard.png);">
                            <figcaption class="fc">
                                <span class="cname" style="color:#957426;"></span>
                                <span class="t" style="color:#aaa;text-shadow:#000 0 -1px;"></span>
                                <span class="n" style="color: rgb(210, 210, 210); text-shadow: rgb(0, 0, 0) 0px -1px; ">NO.<?php echo ($vipInfo['Vip_id']); ?></span>
                            </figcaption>
                        </figure>
                    </div>
                    <div class="back">
                        <figure class="fg" style="background-image:url(/weixin/Public/img/VipCenter/4b.jpg);">
                            <div class="info">
                                <p class="addr">地址：路桥区委宣传部</p>
                                <p class="tel"><a class="autotel" onclick="return false;" href="javascript:void(0);">0576-89207054</a></p>
                            </div>
                            <p class="keywords">路 桥 发 布 中 心</p>
                        </figure>
                    </div>
                </div>
            </div>
        </section>
        <br>
        <div id="vip">
            <small><b><em>亲爱的会员,您的目前<?php echo ($_SESSION['config']['CONFIG_VIP_NAME']); ?>数为:<?php echo ($vipInfo['Vip_integral']); ?></em></b></small>
            <small><em><?php echo ($vipInfo['Vip_name']); ?>排名为: 第 <?php echo ($getIntegralRank); ?> 名</em></small>
            <div class="linkBox">
                <div class = "link">
                    <div class="leftIcon6">
                        <a href="/weixin/APP/VipCenter/index/action/VipInfoShow/">
                            <img class = "leftImg" src="/weixin/Public/img/VipCenter/icon_vipInfo.png"></a>
                        <p>我的信息</p>
                    </div>
                    <?php
 if(0 == $isSigned){ ?>
                        <div class="leftIcon6">
                            <a href="vipdaliy.php?openid=<?php echo $openid;?>&integral=<?php echo $thisVipIntegral;?>&weixinID=<?php echo $weixinID?>">
                                <img src="/weixin/Public/img/VipCenter/icon_qiandao.png"></a>
                            <p>每日签到</p>
                        </div>
                        <?php
 }else{ ?>
                        <div class="leftIcon6" id = "modelDiv">
                            <a onclick="return false;" href="javascript:void(0);"><img src="/weixin/Public/img/icon_qiandaoDisable.png"></a>
                            <p>您已签到</p>
                        </div>
                        <?php
 } ?>
                    <div class="rightIcon6">
                        <a href="../07_forwardingGift/forwardingGift.php?openid=<?php echo $openid;?>&weixinID=<?php echo $weixinID?>">
                            <img class = "leftImg" src="/weixin/Public/img/VipCenter/icon_fenxiang.png"></a>
                        <p>分享有礼</p>
                    </div>
                </div>
                </br>
                <div class = "link">
                    <div class="leftIcon6">
                        <a href="javascript:;" onclick="showMask()" >
                            <img src="/weixin/Public/img/VipCenter/icon_guize.png"></a>
                        <p><?php echo ($_SESSION['config']['CONFIG_VIP_NAME']); ?>规则</p>
                    </div>
                    <div class="leftIcon6">
                        <a href="./VipCennterToGame.php?openid=<?php echo $openid;?>&weixinID=<?php echo $weixinID?>">
                            <img class = "leftImg" src="/weixin/Public/img/VipCenter/icon_shuoming.png"></a>
                        <p>有奖活动</p>
                    </div>
                    <div class="rightIcon6">
                        <a href="vipInfoListSearch.php?openid=<?php echo $openid;?>&weixinID=<?php echo $weixinID?>">
                            <img class = "leftImg" src="/weixin/Public/img/VipCenter/icon_paiming.png"></a>
                        <p>排行榜</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Footer start -->
    <footer class="am-footer am-footer-default">
        <div class="am-footer-miscs">
            <p>由
                <a onclick="return false;" href="javascript:void(0)" title="路桥区网络新闻中心" target="_blank" class="">路桥区网络新闻中心</a>提供技术支持</p>
            <p>CopyRight@2015 路桥发布</p>
        </div>
    </footer>
    <!-- Footer end -->
    <div id="mask" class="mask" style = "display:none">
        <span style="color: #3d74ef"><h3>一、<?php echo ($_SESSION['config']['CONFIG_VIP_NAME']); ?>获得</h3></span>
        <span>1．初次注册会员。完成个人信息填写即可获得5个<?php echo ($_SESSION['config']['CONFIG_VIP_NAME']); ?>（注：个人信息涉及到奖品领取等线下操作）</span>
        <span>2．每日签到。在每日签到页面中，可以通过签到获取1个<?php echo ($_SESSION['config']['CONFIG_VIP_NAME']); ?>，输入签到码，可获得5个<?php echo ($_SESSION['config']['CONFIG_VIP_NAME']); ?>。每日可签到一次。签到码的获得：可在“路桥发布”最近发布的某篇文章底部找到。注：签到码有时间限制，过期失效。</span>
        <span>3．邀请好友。邀请好友加入“路桥发布”会员系统，并填写你的会员卡号作为邀请码即可。邀请人获得3个<?php echo ($_SESSION['config']['CONFIG_VIP_NAME']); ?>，被邀请人可额外获得2个<?php echo ($_SESSION['config']['CONFIG_VIP_NAME']); ?>。</span>

        <span style="color: #3d74ef"><h3>二、奖品领取</h3></span>
        <span>实物奖品，请到路桥区网络新闻中心领取，领取前先预约。地址：台州市路桥区西路桥大道201号（新华书店六楼），上班时间：周一至周五，上午8：30-12：00，下午14：30-17：30。预约电话：0576-89207054</span>
    </div>


    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/flat-ui/2.2.2/js/flat-ui.min.js"></script>
<script src="/weixin/Public/js/wx.js"></script>
<script src="/weixin/Public/js/common.js"></script>
<script src="/weixin/Public/js/function.js"></script>

    
    <script src="/weixin/Public/js/VipCenter/VipCenter.js"></script>
    <script src="/weixin/Public/js/VipCenter/_meishi_wei_html5_v3.2.9.js"></script>


</body>
</html>