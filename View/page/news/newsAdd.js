layui.config({
	base : "js/"
}).use(['form','layer','jquery','layedit','laydate'],function(){
		var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		layedit = layui.layedit,
		laydate = layui.laydate,
		$ = layui.jquery;

    layedit.set({
        uploadImage: {
            url: '/index.php/picture/JugdeOperate/uploadImg', //接口url
            type: 'post' //默认post
        }
    });

    $.ajax({
        type: "POST",
        dataType: "JSON",
        url: "/index.php/news/JugdeOperate/gettype",
        success: function (result) {
			if(result.state=="1")
			{
				for(var i= 0;i<result.content.length;i++){
                    $(".newsLook").append("<option value='"+result.content[i].id+"'>"+result.content[i].name+"</option>");
				}
                form.render('select');
			}

        },
    })

	//创建一个编辑器
 	var editIndex = layedit.build('news_content');
 	var addNewsArray = [],addNews;
 	form.on("submit(addNews)",function(data){
	 	//显示、审核状态
 		var show = data.field.show=="on" ? 1 : 0,
 			topstr = data.field.top=="on" ? 1 : 0;
        var content = layedit.getContent(editIndex);
 		addNews = '{"title":"'+data.field.title+'",';  //文章名称
 		addNews += '"type":"'+$(".newsLook option:selected").val()+'",'; //文章分类
 		addNews += '"created_at":"'+data.field.created_at+'",'; //发布时间
 		addNews += '"operator":"'+data.field.operator+'",'; //文章作者
 		addNews += '"show":"'+ show +'",';  //是否展示
 		addNews += '"top":"'+ topstr +'",'; //是否置顶
        addNews += '"keyword":"'+ data.field.keyword +'",'; //关键字
        addNews += '"abstract":"'+ data.field.abstract +'",'; //摘要
        addNews += '"see":"'+ 0 +'",';
		content = content.replace(/\"/g,"'");
        addNews += '"content":"'+ content +'"}'; //内容
 		addNewsArray.unshift(JSON.parse(addNews));
 		window.sessionStorage.setItem("addNews",JSON.stringify(addNewsArray));
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/news/JugdeOperate/add";
        $.ajax({
            data: JSON.parse(addNews),
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
