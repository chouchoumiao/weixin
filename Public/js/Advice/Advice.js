function adviceCheckForm(){

    if(isNull($('#textinputAdvice').val())){
        alert("建言内容不能为空哟！");
        return false;
    }
    $('#submitDiv').hide();
    $('#resetDiv').hide();
    $('#doingDiv').show();
}

$('#link-scratchcard').click(function () {
   location.href = ROOT+"/APP/Scratchcard/index/action/showView";
});