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

    var json_data = JSON.parse( window.sessionStorage.getItem("edit_activity"));
    $(".aname").val(json_data['name']);
    $(".date").val(json_data['date']);
    $(".phone").val(json_data['phone']);
    $(".prize").val(json_data['prize']);
    $("textarea[name^='prize_way']").val(json_data['prize_way']);
    $("textarea[name^='intro']").val(json_data['intro']);
    $("textarea[name^='join']").val(json_data['join']);

    form.verify({
        activityname:function(value,item){
            if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
                return '活动名不能有特殊字符';
            }
            if(/(^\_)|(\__)|(\_+$)/.test(value)){
                return '活动名首尾不能出现下划线\'_\'';
            }
            if(/^\d+\d+\d$/.test(value)){
                return '活动名不能全为数字';
            }
            if(value.length>50){
                return '活动名不得超过50个字';
            }
            var msg = "";
            $.ajax({
                data: {'id':json_data['id'],"name":value},
                type: "POST",
                dataType: "text",
                async: false,
                url: "/index.php/activity/JudgeOperate/verify_id_name",
                success: function (result) {
                    if(result=="1"){
                        msg = '已存在此活动';
                    }
                },
                error:function(data){
                    msg = data.responseText;
                }
            })
            return msg;
        } ,
        mobile:function(value,item){
            if(!new RegExp("(^(\\d{3,4}-)?\\d{7,8})$|(13[0-9]{9})").test(value)){
                return '请输入正确的电话号码或手机号码';
            }
        }
    });


	//创建一个编辑器
 	var editArea;
 	form.on("submit(editActivity)",function(data){
	 	//显示、审核状态
        editArea = '{"id":"'+json_data['id']+'",';
        editArea += '"name":"'+data.field.aname+'",';
        editArea += '"date":"'+data.field.date+'",';
        editArea += '"phone":"'+ data.field.phone +'",';
        editArea += '"prize":"'+ data.field.prize +'",';
        editArea += '"intro":"'+ data.field.intro +'",';
        editArea += '"join":"'+ data.field.join +'",';
        editArea += '"prize_way":"'+ data.field.prize_way +'"}';
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/activity/JudgeOperate/edit";
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
