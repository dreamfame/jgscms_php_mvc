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
    $("#pic").attr("src",JSON.parse(sys).defaultPic);
    var pic_src = "";

    layui.upload({
        url: '/index.php/picture/JudgeOperate/uploadImg'
        ,success: function(res){
            if(res.code==0){
                $("#pic").attr("src",res.data.src);
                pic_src = res.data.src;
            }
            console.log(res); //上传成功返回值，必须为json格式
        }
    });

    $.ajax({
        type: "POST",
        dataType: "JSON",
        url: "/index.php/news/JudgeOperate/gettype",
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
        if(pic_src==""){
            pic_src = JSON.parse(sys).defaultPic;
        }
 		addNews = '{"title":"'+data.field.title+'",';  //文章名称
 		addNews += '"type":"'+$(".newsLook option:selected").val()+'",'; //文章分类
 		addNews += '"created_at":"'+data.field.created_at+'",'; //发布时间
 		addNews += '"operator":"'+data.field.operator+'",'; //文章作者
 		addNews += '"show":"'+ show +'",';  //是否展示
 		addNews += '"top":"'+ topstr +'",'; //是否置顶
        addNews += '"keyword":"'+ data.field.keyword +'",'; //关键字
        addNews += '"abstract":"'+ data.field.abstract +'",'; //摘要
        addNews += '"see":"'+ 0 +'",';
        addNews += '"pic":"'+ pic_src +'",';
		content = content.replace(/\"/g,"'");
        addNews += '"content":"'+ content +'"}'; //内容
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/news/JudgeOperate/add";
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
