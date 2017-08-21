
var PUBLIC= "/weixin/Public";
var ROOT = "/weixin";

NoShowRightBtn();

/****************************************************
onBridgeReady，NoShowRightBtn含税
不显示微信右上角的按钮，防止代码流失
****************************************************/
function NoShowRightBtn(){
    if (typeof WeixinJSBridge == "undefined"){
        if( document.addEventListener ){
            document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
        }else if (document.attachEvent){
            document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
            document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
        }
    }else{
        onBridgeReady();
    }

}

function onBridgeReady(){
 WeixinJSBridge.call('hideOptionMenu');
}

/**
 * 判断是否为会员
 * @param openid
 * @constructor
 */
function NotVipDo(openid){
	var a = window.confirm("您还不是会员，马上进入会员绑定画面，进行绑定");
	if(a==true)
	{	window.location.href='../01_vipCenter/VipBD.php?openid='+openid;
	}else{
		window.location.href='../01_vipCenter/NotVipInfo.php?openid='+openid;
	}
}

// /**
//  * 显示msg
//  * @param msg
//  */
// function showMsg(msg){
//     if(msg != ""){
//         $('#myMsg').html(msg);
//         $('#myMsg').show();
//         setTimeout("$('#myMsg').hide()",2000);
//     }
// }