var text = "",lastChar = "";
function setMenuAction(thisText) {
    text = thisText;

    //超过9以后截取后两位，没超过则截取最后一位
    if( (text.length == 4) || (text.length == 7)){
        lastChar = text.substr(-1,1);
    }else{
        lastChar = text.substr(-2,2);
    }
    //启动模态
    $('#myModal').modal();

    //取得原先设置的数据
    if(text.length == 4){
        $("#ipt-click").val($("#clickName"+lastChar).val());
        $("#ipt-url").val($("#linkName"+lastChar).val());
    }else{
        $("#ipt-click").val($("#subClickName"+lastChar).val());
        $("#ipt-url").val($("#subLinkName"+lastChar).val());
    }
}
$(function(){
    $('#setUrl').click(function(){
        $("#ipt-url").val("");
        $("#ipt-url").removeAttr("disabled");
    });

    $('#myModal').on('hidden.bs.modal', function () {
        $("#ipt-url").attr("disabled","disabled");
        //只能选择一种作为该菜单一级的目标（链接或者图文形式）
        if(($.trim($("#ipt-click").val()) != "") && ($.trim($("#ipt-url").val()) != "")){
            alert("只能选择其中一种类型");
            return;
        }
        if(($.trim($("#ipt-click").val()) == "") && ($.trim($("#ipt-url").val()) != "")) {
            if(text.length == 4){
                $("#linkName"+lastChar).val($("#ipt-url").val());
                $("#menutype"+lastChar).val("view");
            }else{
                $("#subLinkName"+lastChar).val($("#ipt-url").val());
                $("#subMenutype"+lastChar).val("view");
            }
        }
        if(($.trim($("#ipt-click").val()) != "") && ($.trim($("#ipt-url").val()) == "")){
            if(text.length == 4){
                $("#clickName"+lastChar).val($("#ipt-click").val());
                $("#menutype"+lastChar).val("click");
            }else{
                $("#subClickName"+lastChar).val($("#ipt-click").val());
                $("#subMenutype"+lastChar).val("click");
            }
        }
        if(($.trim($("#ipt-click").val()) == "") && ($.trim($("#ipt-url").val()) == "")){
            if(text.length == 4){
                $("#linkName"+lastChar).attr("value",'');
                $("#clickName"+lastChar).attr("value",'');
                $("#menutype"+lastChar).attr("value",'');
            }else{
                $("#subLinkName"+lastChar).attr("value",'');
                $("#subClickName"+lastChar).attr("value",'');
                $("#subMenutype"+lastChar).attr("value",'');
            }
        }
        //设置完毕后将模态中的数据清空
        $("#ipt-click").attr("value",'');
        $("#ipt-url").attr("value",'');
    });
});
function FormCheck() {
    //需要添加逻辑  以后需要追加
    $(".form-horizontal").submit();
}