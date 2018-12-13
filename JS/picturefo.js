function picPageProc(pager, page, pageSize,condition,conditionText1,conditionText2) {
	$.get("../Control/Dumbbell.php?action=picture&operate=conditionQuery", {condition:condition,conditionText1:conditionText1,conditionText2:conditionText2,page: page, pageSize: pageSize}, function(data) {
		var obj = eval('('+data+')');
		if(obj['state']=="1"){
			var html = "";
			var totalPages = 0;
			if(obj.totalPages<=20){
				totalPages = 1;
			}else{
				if(obj.totalPages%20==0){
					totalPages = parseInt(obj.totalPages/20);
				}
				else{
					totalPages = parseInt(obj.totalPages/20)+1;
				}
			}
			var list = obj['content'];
			for (var i = 0; i < obj['content'].length; i++) {
				html += "<tr data-id='" + list[i]['id'] + "'><td>" + ((page - 1) * pageSize + i + 1) + "</td><td>"
					+ list[i]['title'] + "</td><td><span style='color:#BC8F8F;cursor:pointer' onclick='editcontent(this)'>"
					+ list[i]['content'] + "</span></td><td>"
					+ list[i]['senddate'] + "</td><td>"
					+ list[i]['sender'] +"</td><td><img style='width:130px;height:130px;' src='.."
				    + list[i]['pic']+"'></td><td><span class='btn btn-danger btn-sm glyphicon glyphicon-remove-circle' onclick='delPic(this)'>&nbsp;删除</span></td></tr>";
			}
			$("#picture_content").html(html);
			pager.data("pageProc", picPageProc).data("pageSize", pageSize).data("page", page).data("total", totalPages).trigger("paging");
		}
		else{
			$("#picture_content").html("");
		}
	});
}

function editcontent(obj){
	var id = $(obj).closest('tr').data("id");
	var con = $(obj).text();
	$(obj).popover({
		title:'内容编辑',
		trigger:'manual',
		placement : 'right',
		content:"<textarea data-id='"+id+"' class='form-control' style='text-align:left;width:250px;height:120px;resize:none;' id='editcontent'>"+con+"</textarea><br/><button onclick='finishEdit(this)' type='button' class='btn btn-info'>提交</button>&nbsp;<button type='button' onclick='closeEdit()' class='btn btn-warning'>取消</button>",
		html:true,
	});
	$(obj).popover("show");
}

function closeEdit(){
	$(".popover").prev().popover("destroy");
}

function finishEdit(obj){
	var editcontent = $("#editcontent").val();
	var id = $("#editcontent").data("id");
	$.post("../Control/Dumbbell.php?action=picture&operate=edit",{id:id,content:editcontent},function(data){
		if(data==1){
			$(".popover").prev().text(editcontent);
			$(".popover").prev().popover("destroy");
		}else{
			alert("修改失败");
		}
	});
}

function setCondition(){
		$("#detail").attr("disabled","disabled");
		$("#detail").css("display","");
		$("#startTime").css("display","none");
		$("#endTime").css("display","none");
		$("#condition").html("<option value=0>搜索条件...</option>");
		var select = document.getElementById('condition');
		select.options.add(new Option("标题","title"));
		select.options.add(new Option("上传时间","senddate"));
		select.options.add(new Option("上传人","sender"));
	}

function fileSelect(obj)
{
	var fileId = obj.id.replace("image", "changeImage");
	document.getElementById(fileId).click();
}

function imgChange(obj)
{//换图片
	var form = document.getElementById("picform");
	var num = parseInt(obj.id.replace("changeImage",""),8);
	var id = $(obj).closest("tr").data("id");
	var url = "../Control/Dumbbell.php?action=picture&operate=updateImage&index="+num+"&id="+id;
	form.action = url;
	form.submit();
	var img = obj.id.replace("changeImage","image");
	var t1 = obj.value.lastIndexOf("\\");
	var t2 = obj.value.length;
	if(t1 >= 0 && t1 < t2){
    	document.getElementById(img).src = "../Resources/"+obj.value.substring(t1 + 1, t2);
	}
} 

function delPic(obj){//删除一行信息
	var id = $(obj).closest("tr").data("id");
	var img = obj.parentNode.parentNode.childNodes[5].childNodes[0].src;
	if(confirm("确定要删除该数据吗？"))
	{
		var url= "../Control/Dumbbell.php?action=picture&operate=deletepicture&id="+id;
			$.post(url,function(data){
				var obj = eval('('+data+')');
				alert(obj.content);
				picPageProc($("#pic-list-pager"),1,20,"","","");
				if(obj['state']=="1"){
					var url= "../Control/Dumbbell.php?action=picture&operate=DelServer&img="+img;
					$.post(url,function(){});
				}
			});
	}
}

$(document).ready(function(){
	$("#6").attr("class","active");
	$("#4").attr("class","");
	$("#0").attr("class","");
	$("#2").attr("class","");
	$("#3").attr("class","");
	$("#1").attr("class","");
	$("#5").attr("class","");
	$("#7").attr("class","");
	
	var rename = "0";
	
	setCondition();
	
	picPageProc($("#pic-list-pager"),1,20,"","","");
	
	$("#condition").change(function(){
		$("#detail").css("display","");
		$("#startTime").css("display","none");
		$("#endTime").css("display","none");
		var selectedContent = $("#condition").find("option:selected").val();
		var selectedText = $("#condition").find("option:selected").text();
		if(selectedContent==0){
			$("#detail").attr("disabled","disabled");
		}else{
			$("#detail").removeAttr("disabled");
			if(selectedText=="上传时间"){
				$("#detail").css("display","none");
				$("#startTime").css("display","");
				$("#startTime").addClass("datepicker");
				$("#endTime").addClass("datepicker");
				$("#endTime").css("display","");
			}
		}
	});
	
	$("#addPicModal").on("hide.bs.modal",function(){
		$("#addPicModal form").find("input[name='title']").val("");
		$("#addPicModal form").find("textarea[name='content']").val("");
		$(".ajax-uploader").find("img.preview").attr("src", "../Resources/default.jpg");
		$(".ajax-uploader").find("input:hidden").val("../Resources/default.jpg");
		$("#addPicModal form").find("input[name='title']").popover("hide");
	});
	
	$("#addPicModal form").find("input[name='title']").change(function(){
		var name = $("#addPicModal form").find("input[name='title']").val();
		var url = "../Control/Dumbbell.php?action=picture&operate=validateRename";
		$.post(url,{name:name},function(data){
			var obj = eval('('+data+')');
			if(obj['state']=="0"){
				$("#addPicModal form").find("input[name='title']").popover({
					trigger:'manual',
					placement : 'right',
					content:"<div style='white-space:nowrap;color:red'>"+obj['content']+"</div>",
					html:true,
				});
				$("#addPicModal form").find("input[name='title']").popover("show");
				rename = "1";
			}
			else{
				rename = "0";
				$("#addPicModal form").find("input[name='title']").popover("hide");
			}
		});
	});
	
	$(document).on("submit", "#addPicModal form", function(e) {
		e.preventDefault();
		var form = $(this);
		var modal = $(this).closest(".modal");
		var btn = modal.data("target");
		var name = form.find("input[name='title']").val();
		var pic = form.find("input[name='avatar']").val().replace("..","");
		var content = form.find("textarea[name='content']").val();
		if(name==""){
			alert("标题不能为空");
			return;
		}
		else if(rename=="1"){
			alert("标题重复");
			return;
		}
		$.post("../Control/Dumbbell.php?action=picture&operate=add", {name:name,content:content,pic:pic},
			function(data) {
				var obj = eval('('+data+')');
				if(obj['state']=="1"){
					$(".modal").modal('hide');
					picPageProc($("#pic-list-pager"),1,20,"","","");
				}
				alert(obj['content']);
		})
	});
	
	$("#showAll").click(function(){//显示全部
		$("#picture_content").html("");
		picPageProc($("#pic-list-pager"),1,20,"","","");
	});
	
	
	$("#searchBtn").click(function(){//搜索
		$("#picture_content").html("");
		var searchItem = $("#condition").val();
		var searchText1 = "";
		var searchText2 = "";
		if(searchItem!="senddate"){
			searchText1 = $("#detail").val();
			searchText2 = "";
		}
		else{
			searchText1 = $("#startTime").val()==""?"1970-01-01":$("#startTime").val();
			searchText2 = $("#endTime").val()==""?"2100-01-01":$("#endTime").val();
		}
		picPageProc($("#pic-list-pager"),1,20,searchItem,searchText1,searchText2);
	});
});





