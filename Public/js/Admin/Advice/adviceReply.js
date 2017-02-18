$(function(){
    $('#formSubmit').click(function(){
        if($('#advice_Reply').val() == ""){
            $('#myMsg').html("回复内容不能为空！");
            $('#myMsg').show();
            setTimeout("$('#myMsg').hide()",2000);
            return false;
        }
        var page =  $('#nowPage').val();
        var newBbsReply = $('#advice_Reply').val();
        var adviceID =  $('#advice_id').val();
        $.ajax({
            url:ROOT_ADVICE + "/adviceSetData/flag/Reply"//改为你的动态页
            ,type:"POST"
            ,data:{"adviceID":adviceID,"newBbsReply":newBbsReply}
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
    })
});