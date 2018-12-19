layui.config({
	base : "js/"
}).use(['form','layer','jquery','layedit','laydate'],function(){
	var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		layedit = layui.layedit,
		$ = layui.jquery;

	//创建一个编辑器
 	var editIndex = layedit.build('admin_content');
 	var addAdminArray = [],addAdmin;
 	form.on("submit(addAdmin)",function(data){
 		//是否添加过信息
	 	if(window.sessionStorage.getItem("addAdmin")){
	 		addAdminArray = JSON.parse(window.sessionStorage.getItem("addAdmin"));
	 	}
	 	//显示、审核状态
 		var status = data.field.status=="on" ? 1 : 0,

 		addAdmin = '{"username":"'+data.field.username+'",';  //用户名
        addAdmin += '"nickname":"'+data.field.nickname+'",';	 //昵称
        addAdmin += '"phone":"'+data.field.phone+'",'; //手机
        addAdmin += '"email":"'+data.field.email+'",'; //邮箱
        addAdmin += '"role":"'+data.field.role+'",'; //角色
        addAdmin += '"status":"'+ status +'"}'; //状态
 		addAdminArray.unshift(JSON.parse(addAdmin));
 		window.sessionStorage.setItem("addAdmin",JSON.stringify(addAdminArray));
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
