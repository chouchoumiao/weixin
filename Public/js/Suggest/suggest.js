$(function () {

    /**
     * 提交建议
     */
    $('#suggestBtn').click(function () {
        if(!suggestCheck()){
            return false;
        }

        $.ajax({
            url:ROOT+'/APP/Suggest/index/action/suggested'//改为你的动态页
            ,type:"POST"
            ,data:{
                "name":$('#name').val(),
                "tel":$('#tel').val(),
                "content":$('#content').val()
            }
            ,dataType:"json"
            ,beforeSend: function(){
                $("#submitBtn").attr("disabled","disabled");
                $("#submitBtn").html("正在提交...");
            }
            ,success:function(json){

                //成功
                if(json.success == 1){
                    alert(json.msg);
                    location.reload();
                }else if(json.success == -1) {
                    alert(json.msg);
                    $("#submitBtn").removeAttr("disabled");
                    $("#submitBtn").html("提交");
                }else {
                    alert('提交出现未知错误,请联系管理员');
                }


            }
            ,error:function(){
                alert('提交出现未知错误,请联系管理员！');
            }
        });
    });

    /**
     * 检查提交的内容
     * @returns {boolean}
     */
    function suggestCheck(){

        var name = $('#name').val();

        if(isNull(name)){
            alert("姓名不能为空");
            return false;
        }else{
            if(!isChinaOrNumbOrLett(name)){
                alert("姓名只能是汉族，字母，数字组成");
                return false;
            }
        }
        var tel = $('#tel').val();

        if(isNull(tel)){
            alert("手机号码不能为空");
            return false;
        }else{
            if (checkMobile(tel) == false){
                alert("手机号码格式不正确");
                return false;
            }
        }
        var content = $('#content').val();
        if(isNull(content)){
            alert('建议不能为空');
            return false;
        }

        return true;
    }


});