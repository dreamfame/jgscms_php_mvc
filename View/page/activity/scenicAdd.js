layui.config({
	base : "js/"
}).use(['form','layer','jquery','layedit','upload'],function(){
		var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		layedit = layui.layedit,
		$ = layui.jquery;

    layedit.set({
        uploadImage: {
            url: '/index.php/picture/JudgeOperate/uploadImg', //接口url
            type: 'post' //默认post
        }
    });

    var area = JSON.parse(window.sessionStorage.getItem("area_id_name"));
    for(var i= 0;i<area.length;i++){
        $(".newsLook").append("<option value='"+area[i].id+"'>"+area[i].name+"</option>");
    }
    form.render('select');

	//创建一个编辑器
 	var editIndex = layedit.build('scenic_intro');
 	var addScenic;
 	form.on("submit(addScenic)",function(data){
	 	//显示、审核状态
 		var show = data.field.show=="on" ? 1 : 0,
 			topstr = data.field.top=="on" ? 1 : 0;
        var content = layedit.getContent(editIndex);
 		addScenic = '{"name":"'+data.field.sname+'",';  //文章名称
        addScenic += '"area_id":"'+$(".newsLook option:selected").val()+'",';
 		addScenic += '"created_at":"'+data.field.created_at+'",'; //发布时间
 		addScenic += '"show":"'+ show +'",';  //是否展示
 		addScenic += '"top":"'+ topstr +'",'; //是否置顶
        addScenic += '"brief":"'+ data.field.brief +'",'; //简介
        addScenic += '"see":"'+ 0 +'",';
        addScenic += '"recommend":"'+ data.field.recommend +'",';
		content = content.replace(/\"/g,"'");
        addScenic += '"intro":"'+ content +'"}'; //内容
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/scenic/JudgeOperate/add";
        $.ajax({
            data: JSON.parse(addScenic),
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
