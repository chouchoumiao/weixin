$(function(){


    $("#referrerSubmit").click(function(){

        var addressVal = $('#adressSelect').val();

        if(addressVal == "0" || addressVal == 0){
            alert("请选择地区再提交！");
            return false;
        }

        $.ajax({
            url:ROOT+"/APP/VipCenter/index/action/VipAddressData"
            ,type:"POST"
            ,data:{
                "address":addressVal
            }
            ,dataType:"json"
            ,beforeSend: function(){
                $("#referrerSubmit").hide();
                $("#referrerSubmitDoing").show();
            }
            ,success:function(json){
                $("#referrerSubmitDoing").hide();
                if(json.success == 1){

                    var myOKMsg = $('#myOKMsg');

                    myOKMsg.html(json.msg);

                    myOKMsg.show();
                    $('#baseInfo').hide();
                    setTimeout(function () {
                        window.parent.location = ROOT+"/APP/VipCenter/index/action/VipInfoShow";
                    },3000);
                }else if(json.msg == 2){
                    var myMsg = $('#myMsg');
                    myMsg.html(json.msg);
                    myMsg.show();
                    $('#baseInfo').hide();
                    setTimeout(function () {
                        window.parent.location = ROOT+"/APP/VipCenter/index/action/VipInfoShow";
                    },3000);
                }else {
                    var myMsg = $('#myMsg');
                    myMsg.html(json.msg);
                    myMsg.show();
                    setTimeout(function () {
                        $('#myMsg').hide();$('#referrerSubmit').show()
                    },3000);

                }
            }
            ,error:function(){alert('页面有错误！,请联系管理员');}
        });
    });
});