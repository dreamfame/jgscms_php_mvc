layui.config({
	base : "js/"
}).use(['form','layer','jquery','layedit','upload'],function(){
		var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		layedit = layui.layedit,
		$ = layui.jquery;

		var loading;

    laydate.render({
        elem: '#start', //指定元素
        done: function(value, date){

        }
    });

    form.verify({
        routename:function(value,item){
            if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
                return '路线名称不能有特殊字符';
            }
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
            var area_id = $(".aname option:selected").val();
            $.ajax({
                data: {"area_id":area_id,"name":value},
                type: "POST",
                dataType: "text",
                async: false,
                url: "/index.php/route/JudgeOperate/verify_name",
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
        },
        areaType:function(value,item){
            if(value=="-请选择-"){
                return "请选择景区";
            }
        }
    });

    $.ajax({
        type: "POST",
        dataType: "JSON",
        url: "/index.php/area/JudgeOperate/id_name",
        beforeSend:function(){
            loading = top.layer.msg('数据加载中，请稍候',{icon: 16,time:false,shade:0.8});
        },
        success: function (result) {
            if(result.state=="1")
            {
                for(var i= 0;i<result.content.length;i++){
                    $(".aname").append("<option value='"+result.content[i].id+"'>"+result.content[i].name+"</option>");
                }
                form.render('select');
                $(".layui-unselect").attr("lay-verify","areaType");
            }
            top.layer.close(loading);
        },
    })

	//创建一个编辑器
 	var addRoute;
 	form.on("submit(addRoute)",function(data){
        var area_id = $(".aname option:selected").val();
        addRoute = '{"area_id":"'+area_id+'",';
 		addRoute += '"name":"'+data.field.rname+'",';
 		addRoute += '"created_at":"'+data.field.created_at+'",';
        addRoute += '"route":"'+ data.field.route +'",';
        addRoute += '"type":"'+ data.field.type +'",';
        addRoute += '"time":"'+ data.field.time +'"}';
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/route/JudgeOperate/add";
        $.ajax({
            data: JSON.parse(addRoute),
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
