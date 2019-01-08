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
        areaname:function(value,item){
            /*if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
                return '景区名不能有特殊字符';
            }*/
            if(/(^\_)|(\__)|(\_+$)/.test(value)){
                return '景区名首尾不能出现下划线\'_\'';
            }
            if(/^\d+\d+\d$/.test(value)){
                return '景区名不能全为数字';
            }
            var msg = "";
            $.ajax({
                data: {"name":value},
                type: "POST",
                dataType: "text",
                async: false,
                url: "/index.php/area/JudgeOperate/verify_name",
                success: function (result) {
                    if(result=="1"){
                        msg = '景区名已存在';
                    }
                },
                error:function(data){
                    msg = data.responseText;
                }
            })
            return msg;
        } ,
        abstract:function(value,item){
            if(value.length>150){
                return '文章概要不得超过150个字';
            }
        },
    });

    var sys = window.sessionStorage.getItem("system");
    $("#pic").attr("src",JSON.parse(sys).defaultPic);

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
 	var editIndex = layedit.build('area_intro');
 	var addArea;
 	form.on("submit(addArea)",function(data){
	 	//显示、审核状态
 		var show = data.field.show=="on" ? 1 : 0,
 			topstr = data.field.top=="on" ? 1 : 0;
        var content = layedit.getContent(editIndex);
        if(pic_src==""){
            pic_src = JSON.parse(sys).defaultPic;
        }
 		addArea = '{"name":"'+data.field.aname+'",';  //文章名称
 		addArea += '"created_at":"'+data.field.created_at+'",'; //发布时间
 		addArea += '"show":"'+ show +'",';  //是否展示
 		addArea += '"top":"'+ topstr +'",'; //是否置顶
        addArea += '"brief":"'+ data.field.brief +'",'; //简介
        addArea += '"see":"'+ 0 +'",';
        addArea += '"recommend":"'+ data.field.recommend +'",';
        addArea += '"pic":"'+ pic_src +'",';
		content = content.replace(/\"/g,"'");
        addArea += '"intro":"'+ content +'"}'; //内容
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/area/JudgeOperate/add";
        $.ajax({
            data: JSON.parse(addArea),
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
