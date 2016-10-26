$(function(){

    /**
     * 点击查看中奖信息后跳转指定url
     */
    $("#winInfoBtn").click(function() {

        //跳转到中奖信息详细画面，需要修改
        location.href= ROOT+"/APP/VipCenter/index/action/vipWinningInfo";
    });
    
    $("#areaInputBtn").click(function() {

        //跳转到中奖信息详细画面，需要修改
        location.href= ROOT+"/APP/VipCenter/index/action/VipAddress";
    });

    /**
     * 点击按钮进入刮刮卡画面
     */
    $("#goToscratchcardBtn").click(function() {

        //跳转到中奖信息详细画面，需要修改
        location.href= ROOT+"/APP/Scratchcard/index/action/scratchcard";
    });


    $("#referrerBtn").click(function(){
        $("#baseInfo").hide();
        $("#referrerInfo").show();

    });
    $("#referrerSubmit").click(function(){

        var referrerID = $("#referrerID").val();
        var VipID = $('#vipId').val();
        var vipIntegral = $('#vipIntegral').val();

        if(!isNull(referrerID)){
            if (referrerID.length != 8){
                alert("会员卡号格式错误，请确认！");

                return false;
            }
        }else{
            alert("会员卡号不能为空");
            return false;
        }

        if(referrerID == VipID){
            alert("不能输入自己的卡号");
            return false;
        }
        $.ajax({
            url:ROOT+"/APP/VipCenter/index/action/referrerAdd" //改为你的动态页
            ,type:"POST"
            ,data:{
                "thisVipIntegral":vipIntegral,
                "referrerID":referrerID
            }
            ,dataType:"json"
            ,beforeSend: function(){
                $("#referrerSubmit").hide();
                $("#referrerSubmitDoing").show();
            }
            ,success:function(json){
                $("#referrerSubmitDoing").hide();
                alert(json.msg);
                if(json.success == 0){
                    window.location= ROOT+"/APP/VipCenter/index/action/VipCennter";
                }else{
                    $("#referrerSubmit").show();
                }
            }
            ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
        });
    });

});