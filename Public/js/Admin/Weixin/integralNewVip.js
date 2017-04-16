var ROOT = "/weixin";

$(function(){
    $('#OKBtn').click(function(){

        var integralNewInsert = $('#integralNewInsert').val();
        var integralReferrerForNewVip = $('#integralReferrerForNewVip').val();
        var integralReferrer = $('#integralReferrer').val();
        var weixinName = $('#weixinDisplayName').val();
        var msg = "";

        if((isNull(integralNewInsert))&&(isNull(integralReferrerForNewVip))&&(isNull(integralReferrer))){
            msg = "三项不能都为空，不然设置就木有意义啦！";
        }else if(!isNull(integralNewInsert)){
            if(!isNumber(integralNewInsert)){
                msg = "【新绑定会员可获得"+weixinName+"数】只能为数字";
            }else  if(integralNewInsert<0 || integralNewInsert>999){
                msg = "【新绑定会员可获得"+weixinName+"数】只能为1到999之间的整数";
            }
        }else if(!isNull(integralReferrerForNewVip)){
            if(!isNumber(integralReferrerForNewVip)){
                msg = "【新会员可获得额外"+weixinName+"数】只能为数字";
            }else  if(integralReferrerForNewVip<0 || integralReferrerForNewVip>999){
                msg = "【新会员可获得额外"+weixinName+"数】只能为1到999之间的整数";
            }
        }else if(!isNull(integralReferrer)){
            if(!isNumber(integralReferrer)){
                msg = "【推荐人可获得额外"+weixinName+"数】只能为数字";
            }else  if(integralReferrer<0 || integralReferrer>999){
                msg = "【推荐人可获得额外"+weixinName+"数】只能为1到999之间的整数";
            }
        }
        if(msg != ""){
            $('#myMsg').html(msg);
            $('#myMsg').show();
            setTimeout("$('#myMsg').hide()",2000);
            return false;
        }
        // var url = ROOT+"/Admin/Weixin/doAction/action/integralNewVipData";
        // var typeFlag = 'POST';
        // var ajaxData = {
        //         "integralNewInsert":integralNewInsert,
        //         "integralReferrerForNewVip":integralReferrerForNewVip,
        //         "integralReferrer":integralReferrer
        //     };
        //
        // var jsonRet = doAjax(url,typeFlag,ajaxData);
        //
        // alert(jsonRet);
        // if(jsonRet){
        //
        //     $('#mainInfo').hide();
        //     $('#myOKMsg').html(jsonRet.msg);
        //     $('#myOKMsg').show();
        // }else{
        //     $('#myMsg').html(jsonRet.msg);
        //     $('#myMsg').show();
        // }


        $.ajax({
            url:ROOT+"/Admin/Weixin/doAction/action/integralNewVipData"//改为你的动态页
            ,type:"POST"
            ,data:{"integralNewInsert":integralNewInsert,"integralReferrerForNewVip":integralReferrerForNewVip,"integralReferrer":integralReferrer}
            ,dataType: "json"
            ,success:function(json){
                if(json.success == 1){

                    $('#mainInfo').hide();
                    $('#myOKMsg').html(json.msg);
                    $('#myOKMsg').show();
                }else{
                    $('#myMsg').html(json.msg);
                    $('#myMsg').show();
                }
            }
            ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
        });

    })
});