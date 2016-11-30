$(function () {

    $('#small_replyBtn').click(function () {
        var reply = $('#small_reply').val();
        var openid = $('#small_openid').val();

        //对回复内容进行检查
        doUpdate(reply,openid);
    });

    $('#replyBtn').click(function () {

        alert('DDD');
        var reply = $('#reply').val();
        var openid = $('#openid').val();

        //对回复内容进行检查
        doUpdate(reply,openid);
    });


});

function doUpdate(id) {

    var reply = $('#reply'+id).val();

    update(id,reply);
}

function doDelete(id) {
    if( ('' == id) || (0 == id)){
        alert('参数错误');
        return false;
    }
    $.ajax({
        url:ROOT+'/Admin/Suggest/doAction/action/delete'//改为你的动态页
        ,type:"POST"
        ,data:{
            "id":id
        }
        ,dataType:"json"
        ,beforeSend: function(){
            $(".btn-danger").attr('disabled','disabled');
        }
        ,success:function(json){

            $('.btn-danger').removeAttr('disabled');
            if(json.success == -1){
                alert(json.msg);
                return false;
            }else if(json.success == 1) {
                alert(json.msg);
                location.reload();
            }
        }
        ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
    });


}

function small_doUpdate(id) {

    var reply = $('#small_reply'+id).val();
    update(id,reply);

}

function update(id,reply) {
    if( ('' == id) || (0 == id)){
        alert('参数错误');
        return false;
    }

    if('' == reply){
        alert('回复内容不能为空');
        return false;
    }

    $.ajax({
        url:ROOT+'/Admin/Suggest/doAction/action/reply'//改为你的动态页
        ,type:"POST"
        ,data:{
            "id":id,
            "reply":reply
        }
        ,dataType:"json"
        ,beforeSend: function(){
            $(".btn-primary").attr('disabled','disabled');
        }
        ,success:function(json){

            $('.btn-primary').removeAttr('disabled');
            if(json.success == -1){
                alert(json.msg);
                return false;
            }else if(json.success == 1) {
                alert(json.msg);
                location.reload();
            }
        }
        ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
    });
}