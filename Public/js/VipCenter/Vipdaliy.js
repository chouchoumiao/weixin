$(function(){

    /**
     * 点击不输入签到码签到
     */
    $("#signInNoCodeBtn").click(function(){

        if(!confirm("签到码可在最新文章下方查询!")){
            return false;
        }

        $.ajax({
            url:ROOT+"/APP/VipCenter/index/action/VipdaliyData/"
            ,type:"POST"
            ,data:{}
            ,dataType:"json"
            ,beforeSend: function(){

                $("#signInNoCodeBtn").html("正在提交...");
                $("#signInNoCodeBtn").attr("disabled","disabled");
                $("#signInWithCodeBtn").attr("disabled","disabled");
            }
            ,success:function(json){

                alert(json.msg);
                if(json.success == 1){
                    location.href = ROOT+"/APP/VipCenter/index/action/center/";
                }else {
                    $("#signInNoCodeBtn").html("不输入签到码直接签到");
                    $("#signInNoCodeBtn").removeAttr("disabled");
                    $("#signInWithCodeBtn").removeAttr("disabled");
                    return false;
                }

            }
            ,error:function(){alert('页面有错误！,请联系管理员');}
        });
    });

    /**
     * 点击输入签到码签到
     */
    $("#signInWithCodeBtn").click(function(){
        var signidText = $("#signIn").val();
        if(isNull(signidText)){
            alert("可在“路桥发布”最近发布的某篇文章底部找到签到码");
            return false;
        }else{
            $.ajax({
                url:ROOT+"/APP/VipCenter/index/action/VipdaliyData/"
                ,type:"POST"
                ,data:{
                    "signidText":signidText
                }
                ,dataType:"json"
                ,beforeSend: function(){
                    $("#signInWithCodeBtn").html("正在提交...");
                    $("#signInWithCodeBtn").attr("disabled","disabled");
                    $("#signInNoCodeBtn").attr("disabled","disabled");
                }
                ,success:function(json){
                    alert(json.msg);
                    if(json.success == 1){
                        location.href = ROOT+"/APP/VipCenter/index/action/center/";
                    }else {
                        $("#signInWithCodeBtn").html("输入签到码签到");
                        $("#signInWithCodeBtn").removeAttr("disabled");
                        $("#signInNoCodeBtn").removeAttr("disabled");
                        return false;
                    }

                }
                ,error:function(){alert('页面有错误！,请联系管理员');}
            });

        }
    });
});