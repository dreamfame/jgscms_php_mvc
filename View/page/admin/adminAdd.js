layui.config({
	base : "js/"
}).use(['form','layer','jquery','layedit'],function(){
	var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		layedit = layui.layedit,
		$ = layui.jquery;

	form.verify({
        username:function(value,item){
            if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
                return '用户名不能有特殊字符';
            }
            if(/(^\_)|(\__)|(\_+$)/.test(value)){
                return '用户名首尾不能出现下划线\'_\'';
            }
            if(/^\d+\d+\d$/.test(value)){
                return '用户名不能全为数字';
            }
            if(value.length>10){
                return '用户名不得超过10个字';
            }
            /*if(value.length<4){
                return '用户名不得少于4个字';
            }*/
            var msg = "";
            $.ajax({
                data: {"name":value},
                type: "POST",
                dataType: "text",
                async: false,
                url: "/index.php/admin/JudgeOperate/verify",
                success: function (result) {
                    if(result=="1"){
                        msg = '用户名已存在';
                    }
                },
                error:function(data){
                    msg = data.responseText;
                }
            })
            return msg;
        },
        nickname:function(value,item) {
            var nick = $(".nickname option:selected").text();
            if(nick=="-请选择-"){
                return "请选择昵称";
            }
            else{
                var msg = "";
                $.ajax({
                    data: {"nickname":nick},
                    type: "POST",
                    dataType: "text",
                    async: false,
                    url: "/index.php/admin/JudgeOperate/verify",
                    success: function (result) {
                        if(result=="1"){
                            msg = '昵称已存在';
                        }
                    },
                    error:function(data){
                        msg = data.responseText;
                    }
                })
                return msg;
            }
        },
        checkEmail:function(value, item) {
            if(value==""){
                return;
            }
            var msg = "";
            $.ajax({
                data: {"email": value},
                type: "POST",
                dataType: "text",
                async: false,
                url: "/index.php/admin/JudgeOperate/verify",
                success: function (result) {
                    if (result == "1") {
                        msg = '邮箱已被使用';
                    }
                },
                error: function (data) {
                    msg = data.responseText;
                }
            })
            return msg;
        },
        checkPhone:function(value, item) {
            var msg = "";
            $.ajax({
                data: {"phone": value},
                type: "POST",
                dataType: "text",
                async: false,
                url: "/index.php/admin/JudgeOperate/verify",
                success: function (result) {
                    if (result == "1") {
                        msg = '手机号已被使用';
                    }
                },
                error: function (data) {
                    msg = data.responseText;
                }
            })
            return msg;
        }
    });

    $.ajax({
        type: "POST",
        dataType: "JSON",
        url: "/index.php/user/JudgeOperate/nickname",
        beforeSend:function(){
            loading = top.layer.msg('数据加载中，请稍候',{icon: 16,time:false,shade:0.8});
        },
        success: function (result) {
            if(result.state=="1")
            {
                for(var i= 0;i<result.content.length;i++){
                    $(".nickname").append("<option value='"+result.content[i].openid+"'>"+result.content[i].nickname+"</option>");
                }
                form.render('select');
                $(".layui-unselect").attr("lay-verify","nickname");
            }
            top.layer.close(loading);
        },
    })

	//创建一个编辑器
 	var editIndex = layedit.build('admin_content');
 	var addAdminArray = [],addAdmin;
 	form.on("submit(addAdmin)",function(data){
 		//是否添加过信息
	 	if(window.sessionStorage.getItem("addAdmin")){
	 		addAdminArray = JSON.parse(window.sessionStorage.getItem("addAdmin"));
	 	}
	 	var sys = JSON.parse(window.sessionStorage.getItem("system"));
	 	//显示、审核状态
 		var status = data.field.status=="on" ? 1 : 0;
        var nickname = $(".nickname option:selected").text();
        var openid = $(".nickname option:selected").val();
 		addAdmin = '{"username":"'+data.field.username+'",';  //用户名
        addAdmin += '"nickname":"'+nickname+'",';	 //昵称
        addAdmin += '"head_pic":"'+sys.defaultHeadPic+'",';	 //默认头像
        addAdmin += '"phone":"'+data.field.phone+'",'; //手机
        addAdmin += '"email":"'+data.field.email+'",'; //邮箱
        addAdmin += '"role":"'+data.field.role+'",'; //角色
        addAdmin += '"openid":"'+openid+'",'; //绑定微信
        addAdmin += '"status":"'+ status +'"}'; //状态
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
 		var url = "/index.php/admin/JudgeOperate/add";
        $.ajax({
            data: JSON.parse(addAdmin),
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
