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

    laydate.render({
        elem: '#start', //指定元素
        done: function(value, date){

        }
    });

    form.verify({
        title:function(value,item){
            /*if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
                return '文章标题不能有特殊字符';
            }*/
            if(/(^\_)|(\__)|(\_+$)/.test(value)){
                return '文章标题首尾不能出现下划线\'_\'';
            }
            if(/^\d+\d+\d$/.test(value)){
                return '文章标题不能全为数字';
            }
            if(value.length>50){
                return '文章标题不得超过50个字';
            }
            if(value.length<4){
                return '文章标题不得少于4个字';
            }
        } ,
        author:function(value,item){
            if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
                return '文章作者不能有特殊字符';
            }
            if(/(^\_)|(\__)|(\_+$)/.test(value)){
                return '文章作者首尾不能出现下划线\'_\'';
            }
            if(value.length>10){
                return '文章作者不得超过10个字';
            }
        },
        keyword:function(value,item){
            if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
                return '关键字不能有特殊字符';
            }
            if(/(^\_)|(\__)|(\_+$)/.test(value)){
                return '关键字首尾不能出现下划线\'_\'';
            }
            if(value.length>20){
                return '关键字不得超过20个字';
            }
        },
        abstract:function(value,item){
            if(value.length>150){
                return '文章概要不得超过150个字';
            }
        },
        newsType:function(value,item){
            if(value=="-请选择-"){
                return "请选择文章分类";
            }
        }
    });

    var pic_src = JSON.parse( window.sessionStorage.getItem("edit_news"))['pic'];

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

    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null) return unescape(r[2]); return null; //返回参数值
    }

    var json_data = JSON.parse( window.sessionStorage.getItem("edit_news"));
    var type_data = JSON.parse( window.sessionStorage.getItem("type"));
    $(".title").val(json_data['title']);
    $(".operator").val(json_data['operator']);
    $(".created_at").val(json_data['created_at']);
    $("input[name^='keyword']").val(json_data['keyword']);
    $("textarea[name^='abstract']").val(json_data['abstract']);
    $("#news_content").val(json_data['content']);
    $("#pic").attr("src",json_data['pic']);
    for(var i= 0;i<type_data.length;i++){
        $(".newsLook").append("<option value='"+type_data[i].id+"'>"+type_data[i].name+"</option>");
    }
    $(".newsLook option").each(function(i,n){
        if($(n).text()==json_data['type'])
        {
            $(n).attr("selected",true);
        }
    })
    form.render('select');
    $(".layui-unselect").attr("lay-verify","newsType");

	//创建一个编辑器
 	var editIndex = layedit.build('news_content');
 	var editNews;
 	form.on("submit(editNews)",function(data){
	 	//显示、审核状态
        var content = layedit.getContent(editIndex);
        editNews = '{"id":"'+json_data['id']+'",';
        editNews += '"title":"'+data.field.title+'",';  //文章名称
        editNews += '"type":"'+$(".newsLook option:selected").val()+'",'; //文章分类
        editNews += '"created_at":"'+data.field.created_at+'",'; //发布时间
        editNews += '"operator":"'+data.field.operator+'",'; //文章作者
        editNews += '"keyword":"'+ data.field.keyword +'",'; //关键字
        editNews += '"abstract":"'+ data.field.abstract +'",'; //摘要
        editNews += '"pic":"'+ pic_src +'",'; //图片
		content = content.replace(/\"/g,"'");
        editNews += '"content":"'+ content +'"}'; //内容
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/news/JudgeOperate/edit";
        $.ajax({
            data: JSON.parse(editNews),
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
