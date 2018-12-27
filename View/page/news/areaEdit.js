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

    var json_data = JSON.parse( window.sessionStorage.getItem("edit_area"));
    $(".aname").val(json_data['name']);
    $(".recommend").val(json_data['recommend']);
    $(".created_at").val(json_data['created_at']);
    $("textarea[name^='brief']").val(json_data['brief']);
    $("#area_intro").val(json_data['intro']);


    form.verify({
        areaname:function(value,item){
            if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
                return '景区名不能有特殊字符';
            }
            if(/(^\_)|(\__)|(\_+$)/.test(value)){
                return '景区名首尾不能出现下划线\'_\'';
            }
            if(/^\d+\d+\d$/.test(value)){
                return '景区名不能全为数字';
            }
            var msg = "";
            $.ajax({
                data: {"id":json_data['id'],"name":value},
                type: "POST",
                dataType: "text",
                async: false,
                url: "/index.php/area/JudgeOperate/verify_id_name",
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
            if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
                return '文章概要不能有特殊字符';
            }
            if(/(^\_)|(\__)|(\_+$)/.test(value)){
                return '文章概要首尾不能出现下划线\'_\'';
            }
            if(value.length>10){
                return '文章概要不得超过150个字';
            }
        },
    });


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
