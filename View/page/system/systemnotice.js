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

    function fillData(result){
        function nullData(data){
            if(data == '' || data == "undefined"){
                return "未定义";
            }else{
                return data;
            }
        }
        $("#notice_content").val(nullData(result));
    }

    var notice = window.sessionStorage.getItem("notice");
    fillData(notice);
    var editIndex = layedit.build('notice_content',{
        tool: [
            'strong' //加粗
            ,'italic' //斜体
            ,'underline' //下划线
            ,'del' //删除线

            ,'|' //分割线

            ,'left' //左对齐
            ,'center' //居中对齐
            ,'right' //右对齐
            ,'link' //超链接
            ,'unlink' //清除链接
            ,'face' //表情
        ]
    });

	//创建一个编辑器
 	var editNotice;
 	form.on("submit(editNotice)",function(data){
 	    var content = layedit.getContent(editIndex).replace(/\"/g,"'");
        editNotice = '{"content":"'+ content +'"}'; //内容
 		//弹出loading
 		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/system/JudgeOperate/notice";
        $.ajax({
            data: JSON.parse(editNotice),
            type: "POST",
            dataType: "text",
            url: url,
            beforeSend: function () {

            },
            complete: function () {

            },
            success: function (data) {
                layer.close(index);
                layer.msg("系统公告修改成功！");
                window.sessionStorage.setItem("notice",content);
            },
            error:function(data){
                console.log(data.responseText);
                layer.close(index);
                layer.msg(data.responseText);
            }
        })
        return false;
 	})

})
