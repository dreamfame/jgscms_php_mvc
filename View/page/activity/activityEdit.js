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

    var json_data = JSON.parse( window.sessionStorage.getItem("edit_activity"));
    $(".aname").val(json_data['name']);
    $(".date").val(json_data['date']);
    $(".phone").val(json_data['phone']);
    $(".prize").val(json_data['prize']);
    $("textarea[name^='prize_way']").val(json_data['prize_way']);
    $("textarea[name^='intro']").val(json_data['intro']);
    $("textarea[name^='join']").val(json_data['join']);


	//创建一个编辑器
 	var editArea;
 	form.on("submit(editActivity)",function(data){
	 	//显示、审核状态
        editArea = '{"id":"'+json_data['id']+'",';
        editArea += '"name":"'+data.field.aname+'",';
        editArea += '"date":"'+data.field.date+'",';
        editArea += '"phone":"'+ data.field.phone +'",';
        editArea += '"prize":"'+ data.field.prize +'",';
        editArea += '"intro":"'+ data.field.intro +'",';
        editArea += '"join":"'+ data.field.join +'",';
        editArea += '"prize_way":"'+ data.field.prize_way +'"}';
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/activity/JudgeOperate/edit";
        $.ajax({
            data: JSON.parse(editArea),
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
                    top.layer.msg("编辑成功！");
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
                top.layer.msg("编辑失败!"+data.responseText);
                layer.closeAll("iframe");
                //刷新父页面
                parent.location.reload();
            }
        })
        return false;
 	})

})
