/**
 * 
 */
$(document).ready(function(){
	$("#8").attr("class","active");
	$("#0").attr("class","");
	$("#2").attr("class","");
	$("#3").attr("class","");
	$("#1").attr("class","");
	$("#5").attr("class","");
	$("#6").attr("class","");
	$("#7").attr("class","");
	$("#4").attr("class","");
	
	var oldname = "";
	var oldbirth = "";
	var oldsex = "";
	var oldphone = "";
	
	$.post("../Control/Dumbbell.php?action=user&operate=private",function(data){
		var obj = eval('('+data+')');
		if(obj.state=="1"){
			var list = obj['content'];
			oldname = list[0]['name'];
			oldbirth = list[0]['birth'];
			oldsex = list[0]['sex'];
			oldphone = list[0]['phone'];
			$("#nametxt").val(list[0]['name']);
			$("#phonetxt").val(list[0]['phone']);
			$("#birthtxt").val(list[0]['birth']);
			if(list[0]['sex']=="男"){
				$("#male").closest('label').addClass("active");
			}else{
				$("#female").closest('label').addClass("active");
			}
			$("#headimg").attr("src","../Resources/headimg/"+list[0]['headimg']);
			$("#actionImg").attr("src","../Resources/headimg/"+list[0]['headimg']);
		}
	});
	
	$("#btn-reset").click(function(){
		if(oldname=="")return;
		$("#nametxt").val(oldname);
		$("#phonetxt").val(oldphone);
		$("#birthtxt").val(oldbirth);
		if(oldsex=="男"){
			$("#male").closest('label').addClass("active");
		}else{
			$("#female").closest('label').addClass("active");
		}
	});
	
	$("#btn-save").click(function(){
		var newname = $("#nametxt").val();
		var newphone = $("#phonetxt").val();
		var newbirth = $("#birthtxt").val();
		var newsex = "";
		if($("#male").closest('label').hasClass("active")){
			newsex = "男";
		}else{
			newsex = "女";
		}
		if(newname==""){
			alert("姓名不能为空");return;
		}
		if(newbirth==""){
			alert("生日不能为空");return;
		}
		if(newsex=="")
		{
			alert("性别不能为未知");return;
		}
		$.post("../Control/Dumbbell.php?action=user&operate=edit",{name:newname,phone:newphone,birth:newbirth,sex:newsex},function(data){
			var obj = eval('('+data+')');
			alert(obj.content);
			oldname = "";
			oldbirth = "";
			oldsex = "";
			oldphone = "";
		});
	});
	
	$("#btn-confirm").click(function(){
		var oldpwd = $("#oldpwd").val();
		var newpwd = $("#newpwd").val();
		var repwd = $("#repwd").val();
		if(oldpwd==""){
			alert("请输入旧密码");return;
		}
		if(newpwd.length<6){
			alert("密码不少于6位");
			return;
		}
		if(newpwd==""){
			alert("请输入需要修改的密码");return;
		}
		if(repwd==""){
			alert("请输入确认密码");return;
		}
		if(newpwd==oldpwd){
			alert("不能设置重复密码");return;
		}
		if(newpwd!=repwd){
			alert("两次输入的密码不一致");return;
		}
		$.post("../Control/Dumbbell.php?action=user&operate=changeCode",{oldpwd:oldpwd,newpwd:newpwd},function(data){
			alert(data);
			$("#oldpwd").val("");
			$("#repwd").val("");
			$("#newpwd").val("")
		});
	});

	$("#btn-cancel").click(function(){
		$("#oldpwd").val("");
		$("#repwd").val("");
		$("#newpwd").val("");
	});
	
	$("#btn-submit").click(function(){
		var pic = $(".a").find("input:hidden").val().replace("../Resources/headimg/","");
		$.post("../Control/Dumbbell.php?action=user&operate=updateImage",{pic:pic},function(data){
			alert(data);
			$(".a").find("input:hidden").val("../Resources/default.jpg");
		});
	});
	
	$(document).on("click", ".a .change-file", function(e) {
		e.preventDefault();
		var uploader = $(this).closest(".a");
		$(this).blur();
		if (uploader.find(":file").length) return;
		uploader.data("name", uploader.find("input:hidden").attr("name"));
		var modal  = $("<div class='modal fade' class='upload-modal' role='dialog'>" +
			"<div class='modal-dialog'><div class='modal-content'>" +
				"<div class='modal-header'>" + 
					"<button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span></button>" +
					"<h4 class='modal-title'>文件上传</h4>" +
				"</div>" +
				"<div class='modal-body'>" +
					"<form style='display: inline-block;' method='post' target='upload' action='" + uploader.data("url") + "' enctype='multipart/form-data'>" +
						"<div class='form-group'><label>文件上传</label><input type='file' name='" + uploader.data("name") + "' /></div>" +
						"<div class='form-group'><img id='previewImg' style='display: none;' class='ajax-loader' src='../Resources/loader.gif' width='30' height='30' />" +
						"<span class='btn btn-warning'>取消上传图片</span>" +
					"</form>" +
				"</div>" +
			"</div></div></div>");
		var iframe = $("<iframe name='upload' id='uploader' style='width: auto; height: auto; overflow: scroll; border: none;'><span class='loader'></span></iframe>");
		$(document).find("body").append(modal);
		$(document).find("body").append(iframe);
		modal.modal("show");
		//file.click(); // 激活上传按钮点击事件，IE不兼容
		//文件提交完毕后
		var file = modal.find(":file");
		var form = modal.find("form");
		var btn  = modal.find("span.btn");
		file.change(function() {
			modal.find(".a").show();
			$(this).closest("form").submit();
		});
		form.on("submit", function(e) {
			e.stopPropagation();
		});
		modal.on('hidden.bs.modal', function(e) {
			$(this).remove();
		});
		iframe.load(function() {
			var path = $("#previewImg").attr("src");
			iframe.remove();
			modal.modal('hide');
			uploader.find("img.preview").attr("src", path);
			uploader.find("input:hidden").val(path);
			$("#headimg").attr("src",path);
		});
		btn.click(function() {
			uploader.find(".a").hide();
			iframe.remove();
			modal.modal('hide');
		});
	});
	$(document).on("setImgSrc", ".a img.preview", function(e, src) {
		var path = $(this).attr("src");
		path = path.substring(0, path.lastIndexOf("/") + 1);
		$(this).attr("src", path + src);
		$(this).siblings("input").val(src);
	});
});