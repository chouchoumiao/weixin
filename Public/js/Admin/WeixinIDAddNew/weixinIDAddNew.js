$(function(){

    new uploadPreview({ UpBtn: "up_img", DivShow: "imgdiv", ImgShow: "imgShow" });
    new uploadPreview({ UpBtn: "up_imgMin", DivShow: "imgdivMin", ImgShow: "imgShowMin" });

    if($('#weixin_id').val() == ""){
        $('#imgShow').hide();
        $("#imgShow").attr("src",PUBLIC+"/img/Admin/default_QR.png");
        $('#imgShow').show();

        $('#imgShowMin').hide();
        $("#imgShowMin").attr("src", PUBLIC+"/img/Admin/default_head.png");
        $('#imgShowMin').show();

        //初始化设置Url，token
        $('#weixinUrl').val("http://<?php echo $_SERVER['HTTP_HOST'];?>/?weixinID="); //将app名称用常量替换
        create_token();
    }

    if($('#weixin_id').val()){
        $("#weixinType ").val($('#theWeixinType').val());
    }

    $('#imgShow').click(function(){
        $('#up_img').click();
    });
    $('#imgShowMin').click(function(){
        $('#up_imgMin').click();
    });
});
function formCheck(){
    if(isNull($('#weixinUrl').val())){
        alert("【公众号名称】不能为空");
        return false;
    }
    /*
     if($('#weixinType').val() == "1"){
     if(isNull($('#weixinUrl').val())){
     alert("【公众号名称】不能为空");
     return false;
     }
     }*/
    return true;
}
function create_token(){
    $.ajax({
        url:ROOT+"/Admin/Weixin/doAction/action/getToken"//改为你的动态页
        ,type:"POST"
        ,data:{}//调用json.js类库将json对象转换为对应的JSON结构字符串
        ,dataType: "json"
        ,success:function(json){
            if(json.success == 1){
                $('#weixinToken').val(json.msg);
            }else{
                $('#tokenMsg').html(json.msg);
                $('#tokenMsg').show();
                setTimeout("$('#tokenMsg').hide()",2000);
            }
        }
        //,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
    });
}