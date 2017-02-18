$(function(){

    $('#formSubmitNG').click(function(){

        var adviceID =  $('#advice_id').val();
        var page =  $('#nowPage').val();
        $.ajax({
            url:ROOT_ADVICE + "/adviceSetData/flag/NG"//改为你的动态页
            ,type:"POST"
            ,data:{"adviceID":adviceID}//调用json.js类库将json对象转换为对应的JSON结构字符串
            ,dataType: "json"
            ,success:function(json){

                $('#titel').hide();
                $('#Forminfo').hide();
                if(json.success == 1){
                    $('#myOKMsg').html(json.msg);
                    $('#myOKMsg').show();
                    setTimeout("$('#myOKMsg').hide()",2000);
                }else if (json.success == -1){
                    $('#myMsg').html(json.msg);
                    $('#myMsg').show();
                    setTimeout("$('#myMsg').hide()",2000);
                }
                setTimeout(function(){window.location=ROOT_ADVICE + "/showAdvice/p/"+page;},2000);

            }
            ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
        });
    });

    $('#formSubmitOK').click(function(){
        if($('#advice_advice').val() == ""){
            $('#myMsg').html("建言内容不能为空！");
            $('#myMsg').show();
            setTimeout("$('#myMsg').hide()",2000);
            return false;
        }
        var page =  $('#nowPage').val();

        var newBbsContent = $('#advice_advice').val();
        var adviceID =  $('#advice_id').val();
        $.ajax({
            url:ROOT_ADVICE + "/adviceSetData/flag/ok"//改为你的动态页
            ,type:"POST"
            ,data:{"adviceID":adviceID,"newBbsContent":newBbsContent}
            ,dataType: "json"
            ,success:function(json){

                $('#titel').hide();
                $('#Forminfo').hide();
                if(json.success == 1){
                    $('#myOKMsg').html(json.msg);
                    $('#myOKMsg').show();
                    setTimeout("$('#myOKMsg').hide()",2000);
                }else if (json.success == -1){
                    $('#myMsg').html(json.msg);
                    $('#myMsg').show();
                    setTimeout("$('#myMsg').hide()",2000);
                }
                setTimeout(function(){window.location=ROOT_ADVICE + "/showAdvice/p/"+page;},2000);

            }
            ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
        });
    });
    $('#formSubmitOKANDEvent').click(function(){
        if($('#advice_advice').val() == ""){
            $('#myMsg').html("建言内容不能为空！");
            $('#myMsg').show();
            setTimeout("$('#myMsg').hide()",2000);
            return false;
        }

        var page =  $('#nowPage').val();
        var newBbsContent = $('#advice_advice').val();
        var adviceID =  $('#advice_id').val();
        $.ajax({
            url:ROOT_ADVICE + "/adviceSetData/flag/okANDEvent"//改为你的动态页
            ,type:"POST"
            ,data:{"adviceID":adviceID,"newBbsContent":newBbsContent}
            ,dataType: "json"
            ,success:function(json){

                $('#titel').hide();
                $('#Forminfo').hide();
                if(json.success == 1){
                    $('#myOKMsg').html(json.msg);
                    $('#myOKMsg').show();
                    setTimeout("$('#myOKMsg').hide()",2000);
                }else if (json.success == -1){
                    $('#myMsg').html(json.msg);
                    $('#myMsg').show();
                    setTimeout("$('#myMsg').hide()",2000);
                }
                setTimeout(function(){window.location=ROOT_ADVICE + "/showAdvice/p/"+page;},2000);
            }
            ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
        });
    });
});