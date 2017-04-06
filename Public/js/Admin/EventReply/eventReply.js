$(function(){

    new uploadPreview({ UpBtn: "up_img", DivShow: "imgdiv", ImgShow: "imgShow" });

    if($('#replyID').val() == ""){
        $('#imgShow').hide();
        $("#imgShow").attr("src", PUBLIC+"/img/upload.jpg");
        $('#imgShow').show();
    }

    $('#imgShow').click(function(){
        $('#up_img').click();
    });

    $('#eventTypeText').change(function () {
        var eventText =  $('#eventTypeText').val();

        $.ajax({
            url:ROOT+"/Admin/Event/doAction/action/getNowData"//改为你的动态页
            ,type:"POST"
            ,data:{"eventText":eventText}//调用json.js类库将json对象转换为对应的JSON结构字符串
            ,dataType: "json"
            ,success:function(json){

                $('#formSubmit').removeAttr("disabled");

                $('#reply_intext').val(json.msg.reply_intext);
                $('#reply_title').val(json.msg.reply_title);
                $("#imgShow").attr("src", json.msg.reply_ImgUrl);
                $("#imgUrl").val(json.msg.reply_ImgUrl);
                $('#reply_description').val(json.msg.reply_description);
                $('#reply_content').val(json.msg.reply_content);
                $('#replyID').val(json.msg.replyID);
            }
            ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
        });
    });

    //判断Main表中的数值正确性

    $("form").submit( function(event){
        if(isNull($('#reply_intext').val())){
            alert("【入口关键字】不能为空");
            return false;
        }
        if(isNull($('#reply_title').val())){
            alert("【标题】不能为空");
            return false;
        }
        if(isNull($('#hongbao_id').val())){
            if(isNull($('#up_img').val())){
                alert("请选择图片");
                return false;
            }
        }
        if(isNull($('#reply_description').val())){
            alert("【描述】不能为空");
            return false;
        }
        if(isNull($('#reply_content').val())){
            alert("【正文】不能为空");
            return false;
        }
    } );

});