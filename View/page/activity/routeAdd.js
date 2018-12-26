layui.config({
	base : "js/"
}).use(['form','layer','jquery','layedit','laydate','upload'],function(){
		var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		layedit = layui.layedit,
		laydate = layui.laydate,
		$ = layui.jquery;

		var loading;

    $.ajax({
        type: "POST",
        dataType: "JSON",
        url: "/index.php/area/JudgeOperate/id_name",
        beforeSend:function(){
            loading = top.layer.msg('数据加载中，请稍候',{icon: 16,time:false,shade:0.8});
        },
        success: function (result) {
            if(result.state=="1")
            {
                for(var i= 0;i<result.content.length;i++){
                    $(".aname").append("<option value='"+result.content[i].id+"'>"+result.content[i].name+"</option>");
                }
                form.render('select');
            }
            top.layer.close(loading);
        },
    })

	//创建一个编辑器
 	var addRoute;
 	form.on("submit(addRoute)",function(data){
        var area_id = $(".aname option:selected").val();
        addRoute = '{"area_id":"'+area_id+'",';
 		addRoute += '"name":"'+data.field.rname+'",';
 		addRoute += '"created_at":"'+data.field.created_at+'",';
        addRoute += '"route":"'+ data.field.route +'",';
        addRoute += '"type":"'+ data.field.type +'",';
        addRoute += '"time":"'+ data.field.time +'"}';
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/route/JudgeOperate/add";
        $.ajax({
            data: JSON.parse(addRoute),
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
