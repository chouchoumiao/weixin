$(function(){
    var display = false;
    var num = 0;
    var win = false;
    var prizeName = "";
    var prizedescription = "";
    var SN = "";
    var adress = "";
    var pirationDate = "";

    //取得当前的年月日，以便于数据库中的最近更新日比较
    var today = new Date();
    var year = today.getFullYear();
    var month = today.getMonth() + 1;
    //人为将月份改为两位数
    if(month<10){
        month = "0" + month;
    }
    var day = today.getDate();
    var thisDate = year + "-" + month + "-" + day;

    //【免费次数用完画面】初始化
//        var freeIsOverFlag = 0;
//     $.ajax({
//         url: "scratchcardData.php?action=isfreeOrNot&openid=<?php echo $openid;?>&weixinID=<?php echo $weixinID?>",
//         type: "POST",
//         dataType: "json",
//         data: {"thisDate":thisDate},
//         success: function(data) {
//             //进入画面时，先判断是否已经使用完免费次数
//             if(data.status == "sameDateAndFlag"){
//                 //隐藏本来的活动说明框
//                 $("#thisVipIntegral").text(data.integral);
//                 $("#NowIntegralCount").show();
//                 return false;
//             }
//         },
//         //未获取json返回时
//         error: function() {
//             alert(data.message+"error");
//         }
//     });
    //刮刮卡进行时触发
    $("#scratchpad").wScratchPad({
        width : 150,
        height : 40,
        color : "#a9a9a7",

        scratchMove : function(e, percent){
            num++;
            //80%时自动清除
            if(percent > 80){
                this.clear();
            }
            //开始时请求中奖结果
            if (num == 1) {

                $.ajax({
                    url: ROOT+"/APP/Scratchcard/index/action/scratchcard",
                    type: "POST",
                    dataType: "json",
                    data: {"scratchcard_id":$('#scratchcard_id').val()},
                    success: function(data) {

                        //alert(data.status);
                        if (data.status == "noData"){
                            $("#cover").slideUp(1000);
                            $("#defaultInfo1").slideUp(500);
                            $("#defaultInfo2").slideUp(500);

                            $("#noDataInfo").slideToggle(500);

                            return;

                        }
                        if (data.status == "ok") {
                            win = true;

                            prizeName = data.prizelevel;
                            prizedescription = data.prizedescription;
                            SN = data.SN;
                            adress = data.adress;
                            pirationDate = data.expirationDate;

                            $("#prize").text(prizeName);
                            $("#prizelevel").text(prizeName);
                            $("#prizename").text(prizedescription);
                            $("#prizeSN").text(SN);
                            $("#prizeAdress").text(adress);
                            $("#prizeexPirationDate").text(pirationDate);


                            $("#defaultInfo1").slideUp(10);
                            $("#defaultInfo2").slideUp(10);
                            $("#NowIntegralCount").hide();
                            $("#winprize").slideToggle(1000);

                            return

                        }
                        //<?php echo $weixinName;?>不够一次刮刮卡时
                        if(data.status == "NoEnoughIntegral"){

                            $("#cover").hide();

                            $("#defaultInfo1").hide();
                            $("#defaultInfo2").hide();
                            $("#maxTimesInfo").slideToggle(1000);

                            //$("#thisVipIntegral").text(data.nowIntegralData);
                            //$("#NowIntegralCount").show();

                            return false;
                        }

                        //已经中过奖了
                        if (data.status == "isWinning") {

                            $("#cover").hide();
                            $("#NowIntegralCount").slideUp(10);
                            $("#defaultInfo1").slideUp(10);
                            $("#defaultInfo2").slideUp(10);

                            $("#winneddateTime").text(data.winnedDateTime);
                            $("#winnedprizelevel").text(data.prizelevel);
                            $("#winnedprizename").text(data.prizedescription);
                            $("#winnedprizeSN").text(data.SN);
                            $("#winnedprizeAdress").text(data.adress);
                            $("#winnedprizeexPirationDate").text(data.expirationDate);

                            $("#winnedInfo").slideToggle(1000);

                            return false;
                        }
                    },
                    //未获取json返回时
                    error: function() {
                        alert(data.message+"error");
                    }
                })
            }
            if (num > 10){
                if (!display){
                    //根据概率显示
                    if (!win){
                        $("#prize").text("谢谢参与");
                    }
                }
                display = true;
            }
        }
    });
});