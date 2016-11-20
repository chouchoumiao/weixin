$(function(){
    new uploadPreview({ UpBtn: "up_img", DivShow: "imgdiv", ImgShow: "imgShow" });

    $('#imgShow').click(function(){
        $('#up_img').click();
    });


});

function photoWallCheckForm(){

    var thisName = document.customersUpdateForm.textinputName;
    var thisTel = document.customersUpdateForm.textinputTel;
    var thisImg = document.customersUpdateForm.up_img;

    if(isNull(thisName.value)){
        alert("姓名不能为空");
        thisName.focus();
        return false;
    }else{
        if(!isChinaOrNumbOrLett(thisName.value)){
            alert("姓名只能是汉族，字母，数字组成");
            thisName.focus();
            return false;
        }
    }
    if(!isNull(thisTel.value)){
        if (checkMobile(thisTel.value) == false){
            alert("手机号码格式不正确");
            thisTel.focus();
            return false;
        }
    }
    if(isNull(thisImg.value)){
        alert("尚未选择图片！");
        thisImg.focus();
        return false;
    }
    $('#submitDiv').hide();
    $('#resetDiv').hide();
    $('#doingDiv').show();
}