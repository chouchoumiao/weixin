function showMask(id){
    //显示商品说明图层
    var o=$("#mask"+id);
    var itop=(document.documentElement.clientHeight-o.height())/2+document.documentElement.scrollTop;
    var ileft=(document.documentElement.clientWidth-o.width())/2+document.documentElement.scrollLeft;
    o.css({
        position:"fixed",
        top:itop+"px",
        left:ileft+"px"
    }).fadeIn();

    //显示图层(全屏)，用于点击该图层使商品说明图层消失
    $("#overMask").css("height",$(document).outerHeight(true));
    $("#overMask").css("width",$(document).width());
    $("#overMask").show();
};

//点击最外层图层后，商品说明图层消失
$("#overMask").click(function(){
    $("#overMask").hide();
    $(".mask").fadeOut();
});

//提交数据
function integralGoodsBill(sealID){
    var a = window.confirm("您要进行兑换么？");
    if(a==true)
    {
        $.ajax({
            url:ROOT+"/APP/VipCennterToGame/doAction/action/sealCityJudge/"
            ,type:"POST"
            ,data:{"sealID":sealID}//调用json.js类库将json对象转换为对应的JSON结构字符串
            ,dataType: "json"
            ,success:function(json){
                if(json.success == 1){
                    alert(json.msg);
                    location.reload();
                }else{
                    alert(json.msg);
                }

            }
            ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
        });
    }
}

