//显示遮罩层
function showMask(){
    $("#mask").css("height",$(document).outerHeight(true));
    $("#mask").css("width",$(document).width());
    $("#mask").show();
}
//隐藏遮罩层
$("#mask").click(function(){
    $("#mask").hide();
});