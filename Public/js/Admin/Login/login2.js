/**
 * Created by wujiayu on 2016/11/21.
 */
function wp_attempt_focus(){
    setTimeout( function(){ try{
        d = document.getElementById('user_login');
        d.focus();
        d.select();
    } catch(e){}
    }, 200);
}

wp_attempt_focus();
if(typeof wpOnload=='function')wpOnload();

$(function () {

    $('#wp-submit').click(function () {
        var name = $('#user_login').val();
        var pwd = $('#user_pass').val();

        if(isNull(name)){
            alert('用户名不能为空');
            return false;
        }

        if(name.length > 8){
            alert('用户名不能大于8位');
            return false;
        }

        if(isNull(pwd)){
            alert('密码不能为空');
            return false;
        }

        if(pwd.length < 6){
            alert('密码不能小于6位');
            return false;
        }

        $.ajax({
            url:ROOT+'/Admin/Login/doAction/action/login2Data'//改为你的动态页
            ,type:"POST"
            ,data:{
                "userName":name,
                "pwd":pwd
            }
            ,dataType:"json"
            ,beforeSend: function(){
                $("#wp-submit").attr('disabled','disabled');
            }
            ,success:function(json){

                $('#wp-submit').removeAttr('disabled');
                if(json.success == -1){
                    alert(json.msg);
                    return false;
                }else if(json.success == 1) {
                    location.href = ROOT + '/Admin/Login/doAction/action/showIndex';
                }
            }
            ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
        });

    });
});