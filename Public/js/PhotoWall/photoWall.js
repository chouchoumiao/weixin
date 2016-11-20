$(function(){
    new uploadPreview({ UpBtn: "up_img", DivShow: "imgdiv", ImgShow: "imgShow" });

    $('#imgShow').click(function(){
        $('#up_img').click();
    });

    $('.body').imgAutoSize(5);// 控制图片与容器的边距5

    $(".search-btn").click(function(){
        $(".top-navbar").toggleClass("toggle");
    });


});

function adlike(id,num,picid){

    $.ajax({
        type: "post",
        dataType: "json",
        url: ROOT+"/APP/PhotoWall/index/action/photoWallShowData",
        data:{"id":id,"num":num},
        success: function (json) {
            if(json.success == 1){
                $(".spanid_"+picid).text('已赞');
            }else if (json.success == -1){
                return false;
            }
        }
    });
}

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