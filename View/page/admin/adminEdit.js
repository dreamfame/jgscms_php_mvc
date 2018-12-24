layui.config({
	base : "js/"
}).use(['form','layer','jquery','layedit','laydate'],function(){
	var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		layedit = layui.layedit,
		$ = layui.jquery;

	var url = "/index.php/admin/JudgeOperate/query";
	var id = getUrlParam("id");
	$.ajax({
        data: {userid:id},
        type: "POST",
        dataType: "JSON",
        url: url,
        beforeSend: function () {

        },
        complete: function () {

        },
        success: function (result) {
            if(result.state=="1"){
                $(".adminName").val(result.content[0].username);
                if(result.content[0].role=="照片审核管理员"){
                    $("input[name=role][value='照片审核管理员']").attr("checked","true");
                }
                else{
                    $("input[name=role][value='内容管理员']").attr("checked","true");
                }
                form.render();
            }
            else{

            }
        },
        error:function(data){
            console.log(data.responseText);
        }
    });

    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null) return unescape(r[2]); return null; //返回参数值
    }

	//创建一个编辑器
 	var editIndex = layedit.build('admin_content');
 	var editAdminArray = [],editAdmin;
 	form.on("submit(editAdmin)",function(data){
 		//是否编辑过信息
	 	if(window.sessionStorage.getItem("editAdmin")){
	 		editAdminArray = JSON.parse(window.sessionStorage.getItem("editAdmin"));
	 	}
 		editAdmin = '{"username":"'+data.field.username+'",';  //用户名
        editAdmin += '"role":"'+data.field.role+'"}'; //角色
        editAdminArray.unshift(JSON.parse(editAdmin));
 		window.sessionStorage.setItem("editAdmin",JSON.stringify(editAdminArray));
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
 		var url = "/index.php/admin/JudgeOperate/role";
        $.ajax({
            data: JSON.parse(editAdmin),
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
                    top.layer.msg("修改成功！");
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
                top.layer.msg("修改失败!"+data.responseText);
                layer.closeAll("iframe");
                //刷新父页面
                parent.location.reload();
            }
        })
 		return false;
 	})
	
})
