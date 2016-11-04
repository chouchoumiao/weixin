$(function(){

    var thisName = $('#textinputName');
    var thisTel = $('#textinputTel');
    var thisReferrer = $('#thisVip_referrer');

    var thisState = $('#custom-switch-08').bootstrapSwitch('state');

    $('#custom-switch-08').on('switchChange.bootstrapSwitch', function(event, state) {
        thisState = state;
    });

    function CheckForm(){

        if(isNull(thisName.val())){
            alert("姓名不能为空");
            thisName.focus();
            return false;
        }else{
            if(!isChinaOrNumbOrLett(thisName.val())){
                alert("姓名只能是汉族，字母，数字组成");
                thisName.focus();
                return false;
            }
        }
        if(isNull(thisTel.val())){
            alert("手机号码不能为空");
            thisTel.focus();
            return false;
        }else{
            if (checkMobile(thisTel.val()) == false){
                alert("手机号码格式不正确");
                thisTel.focus();
                return false;
            }
        }

        if(!isNull(thisReferrer.val())){
            if (thisReferrer.val().length != 8){
                alert("会员卡号格式错误，请确认！");
                thisReferrer.focus();
                return false;
            }
        }else{
            if(!confirm("真的不需要填写推荐人么？")){
                thisReferrer.focus();
                return false;
            }
        }
        return true;
    }

    $("#referrerSubmit").click(function(){

        if(thisState){
            sexName = "1";
        }else{
            sexName = "0";
        }

        if(!CheckForm()){
            return false;
        }

        $.ajax({
            url:'VipBDData.php?openid=<?php echo $openid;?>&weixinID=<?php echo $weixinID ?>'//改为你的动态页
            ,type:"POST"
            ,data:{
                "name":thisName.val(),
                "tel":thisTel.val(),
                "referrer":thisReferrer.val(),
                "sex": sexName
            }
            ,dataType:"json"
            ,beforeSend: function(){
                $("#referrerSubmit").hide();
                $("#referrerSubmitDoing").show();
            }
            ,success:function(json){
                $("#referrerSubmitDoing").hide();
                if(json.success == 0){
                    $("#main").hide();
                    $('#myOKMsg').html(json.msg);
                    $('#myOKMsg').show();
                    $("#OKBtn").show();
                }else{
                    $('#myMsg').html(json.msg);
                    $('#myMsg').show();
                    setTimeout("$('#myMsg').hide();$('#referrerSubmit').show()",3000);
                }
            }
            ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
        });
    });
    $("#OKBtn").click(function(){
        window.location="<?php echo $url?>?openid=<?php echo $openid?>&weixinID=<?php echo $weixinID?>";
    })
});