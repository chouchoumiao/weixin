window.onload = function () {
    new uploadPreview({ UpBtn: "up_img", DivShow: "imgdiv", ImgShow: "imgShow" });
};

$(function(){
    $('#imgShow').click(function(){
        $('#up_img').click();
    });

    /**
     * 点击查询结果按钮后
     */
    $("#resultSearch").click(function(){
        $.ajax({
            url:ROOT+"/APP/ForwardingGift/index/action/forwardingGift"
            ,type:"POST"
            ,data:{}//调用json.js类库将json对象转换为对应的JSON结构字符串
            ,dataType: "json"
            ,success:function(json){

                if(json.success == 1){

                    $("#baseInfo").hide();
                    $("#searchInfo").show();
                    //$('#myOKMsg').html(json.msg);
                    //$('#myOKMsg').show();
                }else if (json.success == -1){
                    $('#resultSearch').hide();
                    $('#myMsg').html(json.msg);
                    $('#myMsg').show();
                    setTimeout("$('#myMsg').hide();$('#resultSearch').show();",2000);

                }

            }
            ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
        });
    })
});
function CheckForm(){
    var thisImg = document.customersUpdateForm.up_img;

    if(isNull(thisImg.value)){
        alert("尚未选择图片！");
        thisImg.focus();
        return false;
    }
    $('#referrerSubmit').hide();
    $('#resultSearch').hide();
    $('#doingDiv').show();
}