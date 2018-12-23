layui.config({
	base : "js/"
}).use(['form','layer'],function(){
	var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		$ = layui.jquery;
	//video背景
	$(window).resize(function(){
		if($(".video-player").width() > $(window).width()){
			$(".video-player").css({"height":$(window).height(),"width":"auto","left":-($(".video-player").width()-$(window).width())/2});
		}else{
			$(".video-player").css({"width":$(window).width(),"height":"auto","left":-($(".video-player").width()-$(window).width())/2});
		}
	}).resize();
	
	//登录按钮事件
	form.on("submit(login)",function(data){
		var url = "/index.php/admin/JudgeOperate/login";
		var formData = data.field;
		var code = $("#verifycode").val().toLowerCase();
		if(data.field.code.toLowerCase()!=code){
            layer.alert("验证码错误", { title: '提示信息', icon: 5 });
        }
        else{
            $.ajax({
                data: formData,
                type: "POST",
                dataType: "JSON",
                url: url,
                beforeSend: function () {

                },
                complete: function () {

                },
                success: function (result) {
                    if(result.state=="1"){
                        window.localStorage.setItem("nickname",result.nickname);
                        window.sessionStorage.setItem("username",result.username);
                        window.location.href = "../../index.html";
                    }
                },
                error:function(data){
                   console.log(data);
                }
            })
        }
		//window.location.href = "../../index.html";
		return false;
	})
})
