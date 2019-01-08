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

    var json_data = JSON.parse( window.sessionStorage.getItem("edit_route"));
    $(".aname").val(json_data['area_name']);
    $(".rname").val(json_data['name']);
    $("textarea[name^='route']").val(json_data['route']);

    form.verify({
        routename:function(value,item){
            /*if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
                return '路线名称不能有特殊字符';
            }*/
            if(/(^\_)|(\__)|(\_+$)/.test(value)){
                return '路线名称首尾不能出现下划线\'_\'';
            }
            if(/^\d+\d+\d$/.test(value)){
                return '路线名称不能全为数字';
            }
            if(value.length>50){
                return '路线名称不得超过50个字';
            }
            var msg = "";
            var id = json_data['id']
            var area_id = json_data['area_id'];
            var time = json_data['time'];
            $.ajax({
                data: {"id":id,"area_id":area_id,"name":value,"time":time},
                type: "POST",
                dataType: "text",
                async: false,
                url: "/index.php/route/JudgeOperate/verify_id_name",
                success: function (result) {
                    if(result=="1"){
                        msg = '此景区已存在此路线名';
                    }
                },
                error:function(data){
                    msg = data.responseText;
                }
            })
            return msg;
        } ,
        route:function(value,item){
            if(/(^\_)|(\__)|(\_+$)/.test(value)){
                return '路线首尾不能出现下划线\'_\'';
            }
            var n = value.split("-");
            if(n.length>=1){
                if(value.indexOf("-")>-1&&n.length<2){
                    return '路线至少由两个景点组成';
                }
                else if(value.indexOf("-")<=-1){
                    return '路线格式有误，请按照例子中的格式填写';
                }
            }
        }
    });

 	var editRoute;
 	form.on("submit(editRoute)",function(data){
        editRoute = '{"id":"'+json_data['id']+'",';
        editRoute += '"area_id":"'+json_data['area_id']+'",';
        editRoute += '"name":"'+data.field.rname+'",';
        editRoute += '"route":"'+ data.field.route +'"}';
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/route/JudgeOperate/edit";
        $.ajax({
            data: JSON.parse(editRoute),
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
