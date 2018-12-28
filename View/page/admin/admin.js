var $form;
var form;
var $;
layui.config({
	base : "../../js/"
}).use(['form','layer','upload'],function(){
	form = layui.form();
	var layer = parent.layer === undefined ? layui.layer : parent.layer;
		$ = layui.jquery;
		$form = $('form');

		var id = window.sessionStorage.getItem("username");

		var info;

		form.verify({
            nickname:function(value,item) {
                if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
                    return '昵称不能有特殊字符';
                }
                if(value.length>8){
                    return '昵称不得超过8个字';
                }
                var msg = "";
                $.ajax({
                    data: {"nickname":value,"username":id},
                    type: "POST",
                    dataType: "text",
                    async: false,
                    url: "/index.php/admin/JudgeOperate/verify_edit",
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
            },
            checkEmail:function(value, item) {
                var msg = "";
                $.ajax({
                    data: {"email": value,"username":id},
                    type: "POST",
                    dataType: "text",
                    async: false,
                    url: "/index.php/admin/JudgeOperate/verify_edit",
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
                    data: {"phone": value,"username":id},
                    type: "POST",
                    dataType: "text",
                    async: false,
                    url: "/index.php/admin/JudgeOperate/verify_edit",
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
        })

    $.ajax({
        data:{"username":id},
        type: "POST",
        dataType: "text",
        url: "/index.php/admin/JudgeOperate/query",
        beforeSend: function () {

        },
        complete: function () {

        },
        success: function (result) {
            var data = eval('(' + result + ')');
            if(data.state == "1"){
                info = data.content[0];
                $("#username").val(info.username);
                $("#role").val(info.role);
                $(".nickname").val(info.nickname);
                $(".phone").val(info.phone);
                $(".age").val(info.age);
                $(".email").val(info.email);
                $("#adminFace").attr("src",info.head_pic);
            }
        },
        error:function(data){
            console.log(data.responseText);
        }
    })

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
            if(value != "123456"){
                return "密码错误，请重新输入！";
            }
        },
        newPwd : function(value, item){
            if(value.length < 6){
                return "密码长度不能小于6位";
            }
        },
        confirmPwd : function(value, item){
            if(!new RegExp($("#oldPwd").val()).test(value)){
                return "两次输入密码不一致，请重新输入！";
            }
        }
    })

    //判断是否修改过用户信息，如果修改过则填充修改后的信息
    if(window.sessionStorage.getItem('userInfo')){
        var userInfo = JSON.parse(window.sessionStorage.getItem('userInfo'));
        var citys;
        $(".realName").val(userInfo.realName); //用户名
        $(".userSex input[value="+userInfo.sex+"]").attr("checked","checked"); //性别
        $(".userPhone").val(userInfo.userPhone); //手机号
        $(".userBirthday").val(userInfo.userBirthday); //出生年月
        $(".userAddress select[name='province']").val(userInfo.province); //省
        //填充省份信息，同时调取市级信息列表
        var value = userInfo.province;
        var d = value.split('_');
        var code = d[0];
        var count = d[1];
        var index = d[2];
        if (count > 0) {
            loadCity(areaData[index].mallCityList);
            citys = areaData[index].mallCityList
        } else {
            $form.find('select[name=city]').attr("disabled","disabled");
        }
        $(".userAddress select[name='city']").val(userInfo.city); //市
        //填充市级信息，同时调取区县信息列表
        var value = userInfo.city;
        var d = value.split('_');
        var code = d[0];
        var count = d[1];
        var index = d[2];
        if (count > 0) {
            loadArea(citys[index].mallAreaList);
        } else {
            $form.find('select[name=area]').attr("disabled","disabled");
        }
        $(".userAddress select[name='area']").val(userInfo.area); //区
        for(key in userInfo){
            if(key.indexOf("like") != -1){
                $(".userHobby input[name='"+key+"']").attr("checked","checked");
            }
        }
        $(".userEmail").val(userInfo.userEmail); //用户邮箱
        $(".myself").val(userInfo.myself); //自我评价
        form.render();
    }

    //判断是否修改过头像，如果修改过则显示修改后的头像，否则显示默认头像
    if(window.sessionStorage.getItem('userFace')){
    	$("#userFace").attr("src",window.sessionStorage.getItem('userFace'));
    }else{
    	$("#userFace").attr("src","../../images/face.jpg");
    }

    //提交个人资料
    form.on("submit(changeAdmin)",function(data){
    	var index = layer.msg('提交中，请稍候',{icon: 16,time:false,shade:0.8});
        //将填写的用户信息存到session以便下次调取
        var adminInfo = '';
        adminInfo = {
            'username':$("#username").val(),
            'nickname' : $(".nickname").val(),
            'phone' : $(".phone").val(),
            'age' : $(".age").val(),
            'email' : $(".email").val(),
            'head_pic':pic_src
        };
        window.sessionStorage.setItem("adminInfo",JSON.stringify(adminInfo));
        $.ajax({
            data: adminInfo,
            type: "POST",
            dataType: "JSON",
            url: "/index.php/admin/JudgeOperate/edit",
            beforeSend: function () {

            },
            complete: function () {

            },
            success: function (result) {
                if(result.state=="1"){
                    setTimeout(function(){
                        layer.close(index);
                        layer.msg("提交成功！");
                    },2000);
                }
            },
            error:function(data){
                console.log(data);
            }
        })
    	return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
    })

    //修改密码
    form.on("submit(changePwd)",function(data){
    	var index = layer.msg('提交中，请稍候',{icon: 16,time:false,shade:0.8});
        setTimeout(function(){
            layer.close(index);
            layer.msg("密码修改成功！");
            $(".pwd").val('');
        },2000);
    	return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
    })

})
