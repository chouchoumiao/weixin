    $(function(){

        //如果是有ID的情况下，显示该ID的奖项个数
        if($("#bigWheelCount").val() > 0){

            $("#bigWheel_count ").get(0).selectedIndex = $("#bigWheelCount").val() - 1;
        }

        var i = 0;
        var titleArr = [],descriptionArr = [],probabilityArr = [],countArr = [];
        var detailInfoTitle,detailInfoDescription,detailInfoProbability,detailInfoCount;
        var bigWheel_id = $('#bigWheel_id').val();

        $('#detailSet').click(function(){

            //判断主表填入数据的完整性
            if (!MainInfoCheck()){
                return;
            }
            $('#detail_form').show();
            $('#main_set').hide();
            i++;

            $('#detail_setNum').val("一");
            $('#detail_title').val("一等奖");
            var count= $("#bigWheel_count").val();
            if( i == count){
                $("#detailSetMore").hide();
                $('#detailAllSubmit').show();
            }
            $.ajax({
                url:"__ROOT__/Admin/Event/doAction/action/bigWheelMainInfo"
                ,type:"POST"
                ,data:{"bigWheel_id":bigWheel_id}//调用json.js类库将json对象转换为对应的JSON结构字符串
                ,dataType: "json"
                ,success:function(json){
                    if(json.success == 1){
                        if(bigWheel_id){
                            detailInfoDescription = json.detailInfoDescription;
                            detailInfoProbability = json.detailInfoProbability;
                            detailInfoCount = json.detailInfoCount;

                            //修正bug 返回值不是数据的情况，不出错 20150925
                            if(detailInfoDescription === ""){
                                $('#detail_description').val("");
                            }else{
                                $('#detail_description').val(detailInfoDescription[0]);
                            }
                            if(detailInfoProbability === ""){
                                $('#detail_probability').val("");
                            }else{
                                $('#detail_probability').val(detailInfoProbability[0]);
                            }
                            if(detailInfoCount === ""){
                                $('#detail_count').val("");
                            }else{
                                $('#detail_count').val(detailInfoCount[0]);
                            }
                        }
                    }else {
                        alert('参数传递错误，请重新关闭后打开');
                    }
                }
                ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
            });
        });
        $('#detailSetMore').click(function(){

            if(!DetailInfoCheck()){
                return;
            }
            $('#detail_form').show();
            $('#main_set').hide();
            i++;
            titleArr.push($('#detail_title').val());
            descriptionArr.push($('#detail_description').val());
            probabilityArr.push($('#detail_probability').val());
            countArr.push($('#detail_count').val());

            var count= $("#bigWheel_count").val();
            if(i > count){
                return;
            }else if(i ==count){
                $("#detailSetMore").hide();
                $('#detailAllSubmit').show();
            }
            if(bigWheel_id){
                //修正bug 返回值不是数据的情况，不出错 20150925
                if(detailInfoDescription === ""){
                    $('#detail_description').val("");
                }else{
                    $('#detail_description').val(detailInfoDescription[i-1]);
                }
                if(detailInfoProbability === ""){
                    $('#detail_probability').val("");
                }else{
                    $('#detail_probability').val(detailInfoProbability[i-1]);
                }
                if(detailInfoCount === ""){
                    $('#detail_count').val("");
                }else{
                    $('#detail_count').val(detailInfoCount[i-1]);
                }
            }else{
                $('#detail_description').val("");
                $('#detail_probability').val("");
                $('#detail_count').val("");
            }
            switch(i){
                case 2:
                    $('#detail_setNum').val("二");
                    $('#detail_title').val("二等奖");
                    break;
                case 3:
                    $('#detail_setNum').val("三");
                    $('#detail_title').val("三等奖");
                    break;
                case 4:
                    $('#detail_setNum').val("四");
                    $('#detail_title').val("四等奖");
                    break;
                case 5:
                    $('#detail_setNum').val("五");
                    $('#detail_title').val("五等奖");
                    break;
                case 6:
                    $('#detail_setNum').val("六");
                    $('#detail_title').val("六等奖");
                    break;
                default:
            }
        });
        $('#detailAllSubmit').click(function(){

            i++;
            if(!DetailInfoCheck()){
                return;
            }

            $('#detailAllSubmit').hide();
            $('#submitDoing').show();
            titleArr.push($('#detail_title').val());
            descriptionArr.push($('#detail_description').val());
            probabilityArr.push($('#detail_probability').val());
            countArr.push($('#detail_count').val());

            var mainInfoStr = $("#bigWheel_title").val()+"."+$("#bigWheel_description").val()+"."+$("#bigWheel_times").val()+"."+$("#bigWheel_Integral").val()+"."+$("#bigWheel_beginDate").val()+"."+$("#bigWheel_endDate").val()+"."+$("#bigWheel_expirationDate").val()+"."+$("#bigWheel_address").val()+"."+$("#bigWheel_count").val();

            $.ajax({
                url:"__ROOT__/Admin/Event/doAction/action/bigWheelDetailInsert"
                ,type:"POST"
                ,data:{
                    "bigWheel_id":bigWheel_id,
                    "titleArr":titleArr,
                    "descriptionArr":descriptionArr,
                    "probabilityArr":probabilityArr,
                    "countArr":countArr,
                    "mainInfoStr":mainInfoStr}//调用json.js类库将json对象转换为对应的JSON结构字符串
                ,dataType: "json"
                ,success:function(json){
                    //不论设置成功失败，都显示语句然后跳转到主页面
                    $('#detail_form').hide();
                    $('#main_set').hide();

                    $('#myOKMsg').html(json.msg);
                    $('#myOKMsg').show();
                    setTimeout("$('#myOKMsg').hide()",2000);
                    setTimeout(function(){window.location="bigWheelManger.php";},2000);
                }
                ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
            });
        })
    });
    //判断Main表中的数值正确性
    function MainInfoCheck(){
        var msg = "";
        if(isNull($('#bigWheel_title').val())){
            msg = "【活动标题】不能为空";
            showMsg(msg);
            return false;
        }
        if(isNull($('#bigWheel_description').val())){
            msg = "【活动描述】不能为空";
            showMsg(msg);
            return false;
        }
        if(isNull($('#bigWheel_times').val())){
            msg = "【每天免费可抽奖次数】不能为空";
            showMsg(msg);
            return false;
        }else if(!isNumber($('#bigWheel_times').val())){
            msg = "【每天免费可抽奖次数】只能为数字";
            showMsg(msg);
            return false;
        }else if($('#bigWheel_times').val()<=0 || $('#bigWheel_times').val()>=100){
            msg = "【每天免费可抽奖次数】只能为1到99之间的整数";
            showMsg(msg);
            return false;
        }
        if(isNull($('#bigWheel_Integral').val())){
            msg = "【每次抽奖需要<?php echo $weixinName;?>数】不能为空";
            showMsg(msg);
            return false;
        }else if(!isNumber($('#bigWheel_Integral').val())){
            msg = "【每次抽奖需要<?php echo $weixinName;?>数】只能为数字";
            showMsg(msg);
            return false;
        }else if($('#bigWheel_Integral').val()<=0 || $('#bigWheel_Integral').val()>=100){
            msg = "【每次抽奖需要<?php echo $weixinName;?>数】只能为1到99之间的整数";
            showMsg(msg);
            return false;
        }
        if(isNull($('#bigWheel_beginDate').val())){
            msg = "【活动开始日期】不能为空";
            showMsg(msg);
            return false;
        }
        if(!isDate($('#bigWheel_beginDate').val(),"yyyy-MM-dd")){
            msg = "【活动开始日期】格式不正确";
            showMsg(msg);
            return false;
        }
        if(isNull($('#bigWheel_endDate').val())){
            msg = "【活动结束日期】不能为空";
            showMsg(msg);
            return false;
        }
        if(!isDate($('#bigWheel_endDate').val(),"yyyy-MM-dd")){
            msg = "【活动结束日期】格式不正确";
            showMsg(msg);
            return false;
        }
        //判断日期大小
        var dateMsg = checkTwoDate2($('#bigWheel_beginDate').val(),$('#bigWheel_endDate').val(),"活动开始日期","活动结束日期");

        if(dateMsg != ""){
            showMsg(dateMsg);
            return false;
        }
        if(isNull($('#bigWheel_expirationDate').val())){
            msg = "【领奖过期日期】不能为空";
            showMsg(msg);
            return false;
        }
        if(!isDate($('#bigWheel_expirationDate').val(),"yyyy-MM-dd")){
            msg = "【领奖过期日期】格式不正确";
            showMsg(msg);
            return false;
        }
        dateMsg = checkTwoDate2($('#bigWheel_endDate').val(),$('#bigWheel_expirationDate').val(),"活动结束日期","领奖过期日期");
        if(dateMsg != ""){
            showMsg(dateMsg);
            return false;
        }
        if(isNull($('#bigWheel_address').val())){
            msg = "【领奖地址】不能为空";
            showMsg(msg);
            return false;
        }
        return true;
    }
    //明细表格检查
    function DetailInfoCheck()
    {
        if(isNull($('#detail_title').val())){
            msg = "【奖项标题】不能为空";
            showMsg(msg);
            return false;
        }
        if(isNull($('#detail_description').val())){
            msg = "【奖项描述】不能为空";
            showMsg(msg);
            return false;
        }
        if(isNull($('#detail_probability').val())){
            msg = "【中奖概率】不能为空";
            showMsg(msg);
            return false;
        }else if(!isNumber($('#detail_probability').val())){
            msg = "【中奖概率】只能为数字";
            showMsg(msg);
            return false;
        }else if($('#detail_probability').val()<0 || $('#detail_probability').val()>100){
            msg = "【中奖概率】只能为1到100之间的整数";
            showMsg(msg);
            return false;
        }
        if(isNull($('#detail_count').val())){
            msg = "【奖品数量】不能为空";
            showMsg(msg);
            return false;
        }else if(!isNumber($('#detail_count').val())){
            msg = "【奖品数量】只能是数字";
            showMsg(msg);
            return false;
        }else if($('#detail_count').val()<0 || $('#detail_count').val()>999999){
            msg = "中奖概率】只能为0到999999之间的整数";
            showMsg(msg);
            return false;
        }
        return true;
    }

    function showMsg(msg){
        if(msg != ""){
            $('#myMsg').html(msg);
            $('#myMsg').show();
            setTimeout("$('#myMsg').hide()",2000);
        }
    }

    // $('.form_date').datetimepicker({
    //     language: 'zh-CN',
    //     weekStart: 1,
    //     todayBtn: 1,
    //     autoclose: 1,
    //     todayHighlight: 1,
    //     startView: 2,
    //     minView: 2,
    //     forceParse: 0
    // });