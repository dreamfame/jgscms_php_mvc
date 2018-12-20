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

    var json_data = JSON.parse( window.sessionStorage.getItem("edit_area"));
    $(".aname").val(json_data['name']);
    $(".recommend").val(json_data['recommend']);
    $(".created_at").val(json_data['created_at']);
    $("textarea[name^='brief']").val(json_data['brief']);
    $("#area_intro").val(json_data['intro']);


	//创建一个编辑器
 	var editIndex = layedit.build('area_intro');
 	var editArea;
 	form.on("submit(editArea)",function(data){
	 	//显示、审核状态
        var content = layedit.getContent(editIndex);
        editArea = '{"id":"'+json_data['id']+'",';
        editArea += '"name":"'+data.field.aname+'",';  //文章名称
        editArea += '"created_at":"'+data.field.created_at+'",'; //发布时间
        editArea += '"brief":"'+ data.field.brief +'",'; //简介
        editArea += '"recommend":"'+ data.field.recommend +'",';
        content = content.replace(/\"/g,"'");
        editArea += '"intro":"'+ content +'"}'; //内容
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/area/JudgeOperate/edit";
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
