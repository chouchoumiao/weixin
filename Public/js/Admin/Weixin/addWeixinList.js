$(function(){

    var ROOT = "/weixin";

    $('#OKBtn').click(function(){

        var eventNameList = $.trim($("#eventNameList").val());
        var eventBackUrlList = $.trim($("#eventBackUrlList").val());
        var eventForwardUrlList = $.trim($("#eventForwardUrlList").val());

        var isArr1 = eventNameList.match(/[,，]/g);
        var isArr2 = eventBackUrlList.match(/[,，]/g);
        var isArr3 = eventForwardUrlList.match(/[,，]/g);
        if(isArr1 && isArr2 && isArr3){
            var arr1 = isArr1.length;
            var arr2 = isArr2.length;
            var arr3 = isArr3.length;
        }
        if((arr1 != arr2) || (arr1 != arr3) ){
            $('#myAlert').show();
            setTimeout("$('#myAlert').hide()",2000);
        }else{
            $.ajax({
                url:ROOT+"/Admin/Weixin/doAction/action/adminDBOpr"//改为你的动态页
                ,type:"POST"
                ,data:{
                    "eventNameList":eventNameList,
                    "eventBackUrlList":eventBackUrlList,
                    "eventForwardUrlList":eventForwardUrlList
                }
                ,dataType: "json"
                ,success:function(json){
                    if(json.success == 1){
                        $("#infoDiv").hide();
                        $('#myOKMsg').html(json.msg);
                        $('#myOKMsg').show();
                    }else{
                        $('#infoDiv').hide()
                        $('#myMsg').html(json.msg+'三秒后返回...');
                        $('#myMsg').show();
                        setTimeout("$('#myMsg').hide()",3000);
                        setTimeout("$('#infoDiv').show()",3000);
                    }
                }
                ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
            });
        }

    })
});