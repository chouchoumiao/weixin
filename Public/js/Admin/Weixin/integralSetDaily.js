var ROOT = "/weixin";
$(function(){
    $('#OKBtn').click(function(){

        var thisIntegral = $('#integralSet').val();
        var dailyCodeIntegral = $('#integralDailyCode').val();

        var vipName = $('#vipName').val();
        var msg = "";

        if(isNull(thisIntegral)){
            msg = "【每日签到"+vipName+"】不能为空";
        }else if(!isNumber(thisIntegral)){
            msg = "【每日签到"+vipName+"】只能为数字";
        }else if(thisIntegral<0 || thisIntegral>999){
            msg = "【每日签到"+vipName+"】只能为1到999之间的整数";
        }

        if(msg != ""){
            $('#myMsg').html(msg);
            $('#myMsg').show();
            setTimeout("$('#myMsg').hide()",2000);
            return false;
        }

        if(isNull(dailyCodeIntegral)){
            msg = "【签到码签到"+vipName+"】不能为空";
        }else if(!isNumber(dailyCodeIntegral)){
            msg = "【签到码签到"+vipName+"】只能为数字";
        }else if(dailyCodeIntegral<0 || dailyCodeIntegral>999){
            msg = "【签到码签到"+vipName+"】只能为1到999之间的整数";
        }

        if(msg != ""){
            $('#myMsg').html(msg);
            $('#myMsg').show();
            setTimeout("$('#myMsg').hide()",2000);
            return false;
        }

        $.ajax({
            url:ROOT+"/Admin/Weixin/doAction/action/integralSetDailyData"//改为你的动态页
            ,type:"POST"
            ,data:{"thisIntegral":thisIntegral,
                "dailyCodeIntegral":dailyCodeIntegral
            }
            ,dataType: "json"
            ,success:function(json){
                if(json.success == 1){

                    $('#mainInfo').hide();
                    $('#myOKMsg').html(json.msg);
                    $('#myOKMsg').show();
                }else{
                    $('#myMsg').html(json.msg);
                    $('#myMsg').show();
                }
            }
            ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
        });


        // $.ajax({
        //     url:'integralSetDailyData.php'//改为你的动态页
        //     ,type:"POST"
        //     ,data:{"thisIntegral":thisIntegral,
        //         "dailyCodeIntegral":dailyCodeIntegral
        //     }
        //     ,dataType: "json"
        //     ,success:function(json){
        //         if(json.success == "OK"){
        //
        //             $('#mainInfo').hide();
        //             $('#myOKMsg').html(json.msg);
        //             $('#myOKMsg').show();
        //         }else{
        //             $('#myMsg').html(json.msg);
        //             $('#myMsg').show();
        //         }
        //     }
        //     ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
        // });

    })
});