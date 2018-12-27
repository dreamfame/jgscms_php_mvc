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

    laydate.render({
        elem: '#start', //指定元素
        done: function(value, date){

        }
    });

    var json_data = JSON.parse( window.sessionStorage.getItem("edit_scenic"));
    $(".sname").val(json_data['name']);
    $(".recommend").val(json_data['recommend']);
    $(".created_at").val(json_data['created_at']);
    $("textarea[name^='brief']").val(json_data['brief']);
    $("#scenic_intro").val(json_data['intro']);

    form.verify({
        scenicname:function(value,item){
            if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
                return '景点名不能有特殊字符';
            }
            if(/(^\_)|(\__)|(\_+$)/.test(value)){
                return '景点名首尾不能出现下划线\'_\'';
            }
            if(/^\d+\d+\d$/.test(value)){
                return '景点名不能全为数字';
            }
            var msg = "";
            $.ajax({
                data: {"id":json_data['id'],"area_id":json_data['area_id'],"name":value},
                type: "POST",
                dataType: "text",
                async: false,
                url: "/index.php/scenic/JudgeOperate/verify_id_name",
                success: function (result) {
                    if(result=="1"){
                        msg = '此景区已存在该景点';
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
                return '景点概要不能有特殊字符';
            }
            if(/(^\_)|(\__)|(\_+$)/.test(value)){
                return '景点概要首尾不能出现下划线\'_\'';
            }
            if(value.length>10){
                return '景点概要不得超过150个字';
            }
        }
    });

	//创建一个编辑器
 	var editIndex = layedit.build('scenic_intro');
 	var editScenic;
 	form.on("submit(editScenic)",function(data){
	 	//显示、审核状态
        var content = layedit.getContent(editIndex);
        editScenic = '{"id":"'+json_data['id']+'",';
        editScenic += '"name":"'+data.field.sname+'",';  //文章名称
        editScenic += '"created_at":"'+data.field.created_at+'",'; //发布时间
        editScenic += '"brief":"'+ data.field.brief +'",'; //简介
        editScenic += '"recommend":"'+ data.field.recommend +'",';
        content = content.replace(/\"/g,"'");
        editScenic += '"intro":"'+ content +'"}'; //内容
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/scenic/JudgeOperate/edit";
        $.ajax({
            data: JSON.parse(editScenic),
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
