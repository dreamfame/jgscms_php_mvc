$(function(){
    $.ajax({
        url : "/json/systeminfo.json",
        type : "get",
        dataType : "json",
        success : function(data){
            window.sessionStorage.setItem("system",JSON.stringify(data));
            var sys = JSON.parse(window.sessionStorage.getItem("system"));
            $("#title").text(sys.webName+"-管理登录");
            $(document).attr("title",sys.webTitle);
            $(".logo").text(sys.webName);
            $(".layui-footer p").text(sys.powerby);
        }
    })
})