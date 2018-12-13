/**
 * 
 */
$(document).ready(function(){
	var rename="";
	var message = "";
	
	$("#username").change(function(){
		var phone = $("#username").val();
		var telReg = !!phone.match(/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/);
		if(phone==""){
			$("#username").popover("destroy");
			return;
		}
		if(phone!=""&&telReg == false){
		    message = "手机格式错误";
		    $("#username").popover({
				trigger:'manual',
				placement : 'right',
				content:"<div style='white-space:nowrap;color:red'>"+message+"</div>",
				html:true,
			});
			$("#username").popover("show");
		}else{
			$("#username").popover("destroy");
			var url = "../Control/Dumbbell.php?action=user&operate=validatePhone";
			$.post(url,{phone:phone},function(data){
				if(data=="手机号已被注册"){
					rename = "1";
					color = "red";
				}
				else{
					rename = "0";
					color = "green";
				}
				$("#username").popover({
					trigger:'manual',
					placement : 'right',
					content:"<div style='white-space:nowrap;color:"+color+"'>"+data+"</div>",
					html:true,
				});
				$("#username").popover("show");
			});
		}
	});
	
	$("#username").click(function(){
		$("#username").popover("destroy");
	});
	
	$("#register-btn").click(function(){
		var phone = $("#username").val();
		var pwd = $("#password").val();
		var repwd = $("#passwordConfirm").val();
		var name = $("#name").val();
		var birth = $("#birthtxt").val();
		var sex = "";
		if($("#male").closest('label').hasClass("active")){
			sex = "男";
		}else{
			sex = "女";
		}
		if(phone==""){
			alert("请输入手机号");
			return;
		}
		if(rename=="1"){
			alert("对不起，此手机号已被注册");
			return;
		}
		if(pwd.length<6){
			alert("密码不少于6位");
			return;
		}
		if(pwd!=repwd){
			alert("两次输入密码不一致");
			return;
		}
		if(name==""){
			alert("请输入姓名");
			return;
		}
		if(birth==""){
			alert("请输入生日");
			return;
		}
		$.post("../Control/Dumbbell.php?action=user&operate=reg",{phone:phone,password:pwd,name:name,birth:birth,sex:sex},function(data){
			if(data=="1"){
				alert("注册成功");
				window.location.href="../View/login.html";
			}
			else{
				alert("注册失败");
			}
		});
	});
});