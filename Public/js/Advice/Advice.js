
window.onload = function () {
    new uploadPreview({ UpBtn: "up_img", DivShow: "imgdiv", ImgShow: "imgShow" });
};

$(function(){
    $('#imgShow').click(function(){
        $('#up_img').click();
    });
});

function adviceCheckForm(){

    if(isNull($('#textinputAdvice').val())){
        alert("建言内容不能为空哟！");
        return false;
    }

    if(isNull($('#up_img').val())){
        alert("请上传图片！");
        return false;
    }

    $('#submitDiv').hide();
    $('#resetDiv').hide();
    $('#doingDiv').show();
}