var $form;
var form;
var $;
layui.config({
	base : "../../js/"
}).use(['form','layer','upload','element'],function(){
	form = layui.form();
	var layer = parent.layer === undefined ? layui.layer : parent.layer;
		$ = layui.jquery;
		$form = $('form');
    var element = layui.element();
		var nickname = window.localStorage.getItem("nickname");

        $("#nickname").val(nickname);

		var info;


    var pic_src = "";

    layui.upload({
        url: '/index.php/picture/JudgeOperate/uploadImg'
        ,success: function(res){
            if(res.code==0){
                pic_src = res.data.src;
                $("#adminFace").attr("src",res.data.src);
            }
            console.log(res); //上传成功返回值，必须为json格式
        }
    });



    //添加验证规则
    form.verify({
        oldPwd : function(value, item){
            var msg = "";
            $.ajax({
                data: {"password": value,"username":window.sessionStorage.getItem("username")},
                type: "POST",
                dataType: "text",
                async: false,
                url: "/index.php/admin/JudgeOperate/verify_pwd",
                success: function (result) {
                    if (result == "0") {
                        msg = '原密码输入错误';
                    }
                },
                error: function (data) {
                    msg = data.responseText;
                }
            })
            return msg;
        },
        newPwd : function(value, item){
            if(value.length < 6){
                return "密码长度不能小于6位";
            }
        },
        confirmPwd : function(value, item){
            if($("#oldPwd").val()!=value){
                return "两次输入密码不一致，请重新输入！";
            }
        }
    })

    //修改密码
    form.on("submit(changePwd)",function(data){
        var username = window.sessionStorage.getItem("username");
        var oldpwd = data.field.oldpwd;
        var newpwd = data.field.newpwd;
    	var index = layer.msg('提交中，请稍候',{icon: 16,time:false,shade:0.8});
        $.ajax({
            data: {'username':username,"oldpwd":oldpwd,"newpwd":newpwd},
            type: "POST",
            dataType: "JSON",
            url: "/index.php/admin/JudgeOperate/pwd",
            beforeSend: function () {

            },
            complete: function () {

            },
            success: function (result) {
                if(result.state=="1"){
                    setTimeout(function(){
                        layer.close(index);
                        layer.msg("提交成功！");
                        $("#a").val("");
                        $("#b").val("");
                        $("#oldPwd").val("");
                    },2000);
                }
                else{
                    setTimeout(function(){
                        layer.close(index);
                        layer.msg(result.content);
                    },2000);
                }
            },
            error:function(data){
                console.log(data);
            }
        })
        return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        setTimeout(function(){
            layer.close(index);
            layer.msg("密码修改成功！");
            $(".pwd").val('');
        },2000);
    	return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
    })

})
