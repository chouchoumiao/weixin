$(function(){

    $('#menu').tendina({
        openCallback: function(clickedEl) {
            clickedEl.addClass('opened');
        },
        closeCallback: function(clickedEl) {
            clickedEl.addClass('closed');
        }
    });

    var nowWeixinID = $('#nowWeixinID').val();
    $("#weiIDSelect ").val(nowWeixinID);

    $(".ad_setting_new").click(function(){
        $("#ad_setting_ul").show();
    });
    $("#ad_setting_ul").mouseleave(function(){
        $(this).hide();
    });
    $("#ad_setting_ul li").mouseenter(function(){
        $(this).find("a").attr("class","ad_setting_ul_li_a");
    });
    $("#ad_setting_ul li").mouseleave(function(){
        $(this).find("a").attr("class","");
    });
});

function getWeiID(){
    var weixinID =  $("#weiIDSelect").val();

    $.ajax({
        url:ROOT + "/Admin/Index/doAction/action/getWeiID"
        ,type:"POST"
        ,data:{"weixinID":weixinID}//调用json.js类库将json对象转换为对应的JSON结构字符串
        ,dataType: "json"
        ,success:function(json){
            if(json.success != 1){
                alert(json.msg);
                return false;
            }
            self.location=ROOT + "/Admin/Index/doAction/action/showIndex";
        }
        ,error:function(xhr){alert('PHP页面有错误！'+xhr.responseText);}
    });
}