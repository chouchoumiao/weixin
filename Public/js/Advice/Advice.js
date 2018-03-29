
window.onload = function () {
    new uploadPreview({ UpBtn: "up_img", DivShow: "imgdiv", ImgShow: "imgShow" });
};

$(function(){
    $('#imgShow').click(function(){
        $('#up_img').click();
    });
});

function adviceCheckForm(){

    var name = $('#textinputName').val();
    if(isNull(name)){
        alert("姓名/昵称不能为空！");
        return false;
    }
    if(name.length > 5){
        alert("姓名/昵称不能超过5个字！");
        return false;
    }

    var tel = $('#textinputTel').val();
    if(isNull(tel)){
        alert("手机号不能为空！");
        return false;
    }

    if(!checkMobile(tel)){
        alert("手机格式错误！");
        return false;
    }

    var imgName = $('#textinputAdvice').val();
    if(isNull(imgName)){
        alert("图片描述不能为空哟！");
        return false;
    }
    if(imgName.length > 20){
        alert("图片描述不能超过20个字！");
        return false;
    }

    if(isNull($('#up_img').val())){
        alert("请上传图片！");
        return false;
    }

    $("a").each(function () {
        var textValue = $(this).html();
        if (textValue == "活动" || textValue == "精选照片") {
            $(this).css("cursor", "default");
            $(this).attr('href', '#');     //修改<a>的 href属性值为 #  这样状态栏不会显示链接地址
            $(this).click(function (event) {
                event.preventDefault();   // 如果<a>定义了 target="_blank“ 需要这句来阻止打开新页面
            });
        }
    });

    $('#submitDiv').hide();
    $('#resetDiv').hide();
    $('#doingDiv').show();
}