$(function(){
    //显示遮罩层
    $("#showMask").click(function(){
        $("#mask").css("height",$(document).outerHeight(true));
        $("#mask").css("width",$(document).width());
        $("#mask").show();
    });

    //隐藏遮罩层
    $("#mask").click(function(){
        $("#mask").hide();
    });

    //点击更多活动
    $("#moreInfo").click(function(){
        alert("更多玩法，推出中...")
    });
});