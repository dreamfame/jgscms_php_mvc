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
                data: {"name":value},
                type: "POST",
                dataType: "text",
                async: false,
                url: "/index.php/activity/JudgeOperate/verify_name",
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
 	var addActivity;
 	form.on("submit(addActivity)",function(data){
 		var enable = data.field.enable=="on" ? 1 : 0;
 		if(pic_src==""){
 		    pic_src = JSON.parse(sys).defaultPic;
        }
 		addActivity = '{"name":"'+data.field.aname+'",';
 		addActivity += '"date":"'+data.field.date+'",';
 		addActivity += '"enable":"'+ enable +'",';
        addActivity += '"phone":"'+ data.field.phone +'",';
        addActivity += '"prize":"'+ data.field.prize +'",';
        addActivity += '"join":"'+ data.field.join +'",';
        addActivity += '"prize_way":"'+ data.field.prize_way +'",';
        addActivity += '"pic":"'+ pic_src +'",';
        addActivity += '"intro":"'+ data.field.intro +'"}';
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/activity/JudgeOperate/add";
        $.ajax({
            data: JSON.parse(addActivity),
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
