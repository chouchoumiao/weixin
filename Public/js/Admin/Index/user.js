$(function(){

    /**
     * 修改密码
     */
    $('#newPassBtn').click(function(){
        var newPass = $.trim($("#newPass").val());
        var newPass2 = $.trim($("#newPass2").val());
        if((newPass == '')||(newPass != newPass2)){
            $('#myAlert').show();
            setTimeout("$('#myAlert').hide()",2000);
        }else{
            $.ajax({
                url:ROOT + "/Admin/Index/doAction/action/editPwdData"
                ,type:"POST"
                ,data:{"action":"newPassEdit","newPass":newPass}
                ,dataType: "json"
                ,success:function(json){

                    if(json.success == 1){
                        $("#pass1").hide();
                        $("#pass2").hide();
                        $("#passBtn").hide();
                        $("#successMeg").show();
                        $('#myOKMsg').html(json.msg);
                        $('#myOKMsg').show();
                    }else{
                        $("#successMeg").show();
                        setTimeout("$('#successMeg').hide('fast')",3000);
                        setTimeout("$('#passBtn').show('fast')",3000);
                        $('#myMsg').html(json.msg);
                        $('#myMsg').show();
                        setTimeout("$('#myMsg').hide()",3000);
                    }
                }
                ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
            });
        }
    })
});