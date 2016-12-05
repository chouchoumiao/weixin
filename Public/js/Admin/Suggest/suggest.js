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

    //图片点击放大JS
    $("#imgUrl1").imgbox({
        'speedIn'		: 0,
        'speedOut'		: 0,
        'alignment'		: 'center',
        'overlayShow'	: true,
        'allowMultiple'	: false
    });


});

function doUpdate(id) {
    var reply1 = '';
    var reply2 = '';
    var reply3 = '';
    if($('#reply1'+id)){
        reply1 = $('#reply1'+id).val();
    }

    if($('#reply2'+id)){
        reply2 = $('#reply2'+id).val();
    }

    if($('#reply3'+id)){
        reply3 = $('#reply3'+id).val();
    }

    update(id,reply1,reply2,reply3);
}

function doDelete(id) {

    if(!confirm('确定删除吗？')){
        return false;
    }
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

    var reply1 = '';
    var reply2 = '';
    var reply3 = '';
    if($('#small_reply1'+id)){
        reply1 = $('#small_reply1'+id).val();
    }

    if($('#small_reply2'+id)){
        reply2 = $('#small_reply2'+id).val();
    }

    if($('#small_reply3'+id)){
        reply3 = $('#small_reply3'+id).val();
    }

    update(id,reply1,reply2,reply3);

}

function update(id,reply1,reply2,reply3) {
    if( ('' == id) || (0 == id)){
        alert('参数错误');
        return false;
    }

    if('' == reply1){
        alert('回复内容不能为空');
        return false;
    }

    $.ajax({
        url:ROOT+'/Admin/Suggest/doAction/action/reply'//改为你的动态页
        ,type:"POST"
        ,data:{
            "id":id,
            "reply1":reply1,
            "reply2":reply2,
            "reply3":reply3
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

function showImg(imgPath) {
    alert(imgPath);
}

