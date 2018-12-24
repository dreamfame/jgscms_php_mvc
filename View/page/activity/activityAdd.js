layui.config({
	base : "js/"
}).use(['form','layer','jquery','layedit','laydate','upload'],function(){
		var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		layedit = layui.layedit,
		laydate = layui.laydate,
		$ = layui.jquery;

    layedit.set({
        uploadImage: {
            url: '/index.php/picture/JudgeOperate/uploadImg', //接口url
            type: 'post' //默认post
        }
    });

    var sys = window.sessionStorage.getItem("system");
    $("#pic").attr("src",sys.defaultPic);
    var pic_src = "";

    layui.upload({
        url: '/index.php/picture/JudgeOperate/uploadImg'
        ,success: function(res){
            if(res.code==0){
                pic_src = res.data.src;
                $("#pic").attr("src",res.data.src);
            }
            console.log(res); //上传成功返回值，必须为json格式
        }
    });

	//创建一个编辑器
 	var addActivity;
 	form.on("submit(addActivity)",function(data){
 		var enable = data.field.enable=="on" ? 1 : 0;
        pic_src == ""?sys.defaultPic:pic_src;
 		addActivity = '{"name":"'+data.field.aname+'",';
 		addActivity += '"date":"'+data.field.date+'",';
 		addActivity += '"enable":"'+ enable +'",';
        addActivity += '"phone":"'+ data.field.phone +'",';
        addActivity += '"prize":"'+ data.field.prize +'",';
        addActivity += '"join":"'+ data.field.join +'",';
        addActivity += '"prize_way":"'+ data.field.prize_way +'",';
        addActivity += '"pic":"'+ pic_src +'",';
        addActivity += '"intro":"'+ data.field.intro +'"}';
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/activity/JudgeOperate/add";
        $.ajax({
            data: JSON.parse(addActivity),
            type: "POST",
            dataType: "JSON",
            url: url,
            beforeSend: function () {

            },
            complete: function () {

            },
            success: function (result) {
                if(result.state=="1"){
                    top.layer.close(index);
                    top.layer.msg("添加成功！");
                    layer.closeAll("iframe");
                    //刷新父页面
                    parent.location.reload();
                }
                else{
                    top.layer.close(index);
                    top.layer.msg(result.content);
                    layer.closeAll("iframe");
                    //刷新父页面
                    parent.location.reload();
                }
            },
            error:function(data){
                console.log(data.responseText);
                top.layer.close(index);
                top.layer.msg("添加失败!"+data.responseText);
                layer.closeAll("iframe");
                //刷新父页面
                parent.location.reload();
            }
        })
        return false;
 	})

})
