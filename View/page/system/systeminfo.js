layui.config({
	base : "js/"
}).use(['form','layer','jquery'],function(){
	var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		$ = layui.jquery;

 	var system;
    form.on("submit(system)",function(data){
        system = '{"webName":"'+$(".webName").val()+'",';  //网站名称
        system += '"webTitle":"'+$(".webTitle").val()+'",';	 //网站标题
		system += '"version":"'+$(".version").val()+'",';	 //当前版本
        system += '"defaultHeadPic":"'+$(".defaultHeadPic").val()+'",'; //默认头像
        system += '"defaultPic":"'+$(".defaultPic").val()+'",'; //默认图片
        system += '"server":"'+$(".server").val()+'",'; //服务器环境
        system += '"dataBase":"'+$(".dataBase").val()+'",'; //数据库版本
        system += '"description":"'+$(".description").val()+'",'; //站点描述
        system += '"powerby":"'+$(".powerby").val()+'",'; //版权信息
        system += '"record":"'+$(".record").val()+'"}'; //网站备案号
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
		$.ajax({
			data:JSON.parse(system),
            url : "/index.php/system/JudgeOperate/update",
            type : "post",
            dataType : "text",
            success : function(data){
                layer.close(index);
                layer.msg("系统信息修改成功！");
                window.sessionStorage.setItem("system",system);
            }
        })
 		return false;
 	})


 	//加载默认数据
 	if(window.sessionStorage.getItem("system")){
 		var data = JSON.parse(window.sessionStorage.getItem("system"));
 		fillData(data);
 	}else{
 		$.ajax({
			url : "../../json/systeminfo.json",
			type : "get",
			dataType : "json",
			success : function(data){
				fillData(data);
			}
		})
 	}

 	//填充数据方法
 	function fillData(data){
        function nullData(data){
            if(data == '' || data == "undefined"){
                return "未定义";
            }else{
                return data;
            }
        }
        $(".webName").val(nullData(data.webName));      //网站名称
        $(".webTitle").val(nullData(data.webTitle));      //网站标题
        $(".version").val(nullData(data.version));      //当前版本
        $(".defaultHeadPic").val(nullData(data.defaultHeadPic));    //管理员默认头像
        $(".defaultPic").val(nullData(data.defaultPic));        //景区新闻/介绍/活动默认图片
        $(".server").val(nullData(data.server));   // 服务器环境
        $(".dataBase").val(nullData(data.dataBase));    //数据库版本
        $(".powerby").val(nullData(data.powerby));      //版权信息
        $(".description").val(nullData(data.description));//站点描述
        $(".record").val(nullData(data.record));      //网站备案号
 	}
 	
})
