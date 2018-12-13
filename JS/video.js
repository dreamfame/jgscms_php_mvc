function videoPageProc(pager, page, pageSize,condition,conditionText) {
	$.get("../Control/Dumbbell.php?action=video&operate=conditionQuery", {condition:condition,conditionText:conditionText,page: page, pageSize: pageSize}, function(data) {
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
					+ list[i]['name'] + "</td><td><img src='.."
					+ list[i]['pngsrc'] + "'></td><td><img style='zoom:0.5' src='.."
					+ list[i]['gifsrc']+"'></td><td>"
					+ list[i]['type'] +"</td><td>"
				    + list[i]['intro'] +"</td><td><span class='btn btn-warning btn-sm glyphicon glyphicon-edit' data-toggle='modal' data-target='#editActionModal'>&nbsp;编辑</span>&nbsp;<span class='btn btn-danger btn-sm glyphicon glyphicon-remove-circle' onclick='delVideo(this)'>&nbsp;删除</span></td></tr>"
			}
			$("#video_content").html(html);
			pager.data("pageProc", videoPageProc).data("pageSize", pageSize).data("page", page).data("total", totalPages).trigger("paging");
		}
		else{
			$("#video_content").html("");
		}
	});
}

function delVideo(obj){//删除一行信息
	var id = $(obj).closest("tr").data("id");
	var img = obj.parentNode.parentNode.childNodes[2].childNodes[0].src;
	var gif = obj.parentNode.parentNode.childNodes[3].childNodes[0].src;
	if(confirm("确定要删除该数据吗？"))
	{
		var url= "../Control/Dumbbell.php?action=video&operate=del&id="+id;
			$.post(url,function(data){
				var obj = eval('('+data+')');
				alert(obj.content);
				videoPageProc($("#video-list-pager"),1,20,"","","");
				if(obj['state']=="1"){
					var url= "../Control/Dumbbell.php?action=video&operate=DelServer&img="+img+"&gif="+gif;
					$.post(url,function(){});
				}
			});
	}
}

function setCondition(type){
		$("#detail").attr("disabled","disabled");
		$("#detail").css("display","");
		$("#condition").html("<option value=0>搜索条件...</option>");
		var select = document.getElementById('condition');
		select.options.add(new Option("名称","name"));
	}

function SelectAll(thisTable, obj) {
    var table = document.getElementById(thisTable);
    var isSelect = obj.checked;
    var tbody = table.tBodies[1];
    for (var i = 0; i < tbody.rows.length; i++) {
        var chkOrder = tbody.rows[i].cells[0].firstChild;
        chkOrder.checked = isSelect;
    }
}

$(document).ready(function(){
	var rename = "0";
	
	$("#4").attr("class","active");
	$("#0").attr("class","");
	$("#2").attr("class","");
	$("#3").attr("class","");
	$("#1").attr("class","");
	$("#5").attr("class","");
	$("#6").attr("class","");
	$("#7").attr("class","");
	
	setCondition();
	
	videoPageProc($("#video-list-pager"),1,20,"","");
	
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
			if(selectedText=="时间"){
				$("#detail").css("display","none");
				$("#startTime").css("display","");
				$("#endTime").css("display","");
			}
		}
	});
	
	var oldname = "";
	
	$("#editActionModal").on("show.bs.modal",function(e){
		var id = $(e.relatedTarget).closest("tr").data("id");
		$("#editLabel").attr("data-id",id);
		$.post("../Control/Dumbbell.php?action=video&operate=query",{id:id},function(data){
			var obj = eval('('+data+')');
			var list = obj['content'][0];
			if(obj['state']=="1"){
				$("input:checkbox").each(function () {  
					if(list.type.indexOf(this.value) != -1){
						this.checked = true;
					}
					else{
						this.checked = false;
					}
		        });
				oldname = list.name;
				$("#editActionModal form").find("input[name='actionname']").val(list.name);
				$("#editActionModal form").find("textarea[name='intro']").val(list.intro);
				$("#editActionModal #thumbImg").attr("src",".."+list.pngsrc);
				$("#editActionModal #actionImg").attr("src",".."+list.gifsrc);
				$(".a").find("input:hidden").val(".."+list.pngsrc);
				$(".ajax-uploader").find("input:hidden").val(".."+list.gifsrc);
			}
		});
	});
	
	$("#editActionModal form").find("input[name='actionname']").change(function(){
		var name = $("#editActionModal form").find("input[name='actionname']").val();
			var url = "../Control/Dumbbell.php?action=video&operate=validateRename";
			$.post(url,{name:name},function(data){
				var obj = eval('('+data+')');
				$("#editActionModal form").find("input[name='actionname']").popover({
					trigger:'manual',
					placement : 'right',
					content:"<div style='white-space:nowrap;color:red'>"+obj['content']+"</div>",
					html:true,
				});
				if(obj['state']=="0"){
					if(oldname!=name){
						$("#editActionModal form").find("input[name='actionname']").popover("show");
						rename = "1";
					}else{
						rename = "0";
						$("#editActionModal form").find("input[name='actionname']").popover("destroy");
					}
				}
				else{
					rename = "0";
					$("#editActionModal form").find("input[name='actionname']").popover("destroy");
				}
			});
	});
	
	$(document).on("submit", "#editActionModal form", function(e) {
		e.preventDefault();
		var form = $(this);
		var modal = $(this).closest(".modal");
		var btn = modal.data("target");
		var id = $("#editLabel").data("id");
		var name = form.find("input[name='actionname']").val();
		var pic = form.find("input[name='avatar']").val().replace("..","");
		var intro = form.find("textarea[name='intro']").val();
		var thumb = form.find("input[name='thumb']").val().replace("..","");
		var str = "";
		$("#editActionModal form input:checkbox").each(function(){ 
            if(this.checked){
                str += $(this).val()+","
            }
        })
        str = str.substr(0,str.length-1);
		var type = str;
		if(name==""){
			alert("名称不能为空");
			return;
		}
		else if(rename=="1"){
			alert("名称重复");
			return;
		}
		else if(type==""){
			alert("类型不能为空");
			return;
		}
		$.post("../Control/Dumbbell.php?action=video&operate=edit", {id:id,name:name,type:type,intro:intro,pic:pic,thumb,thumb},
			function(data) {
				var obj = eval('('+data+')');
				if(obj['state']=="1"){
					$(".modal").modal('hide');
					videoPageProc($("#video-list-pager"),1,20,"","");
				}
				alert(obj['content']);
				rename = "";
		})
	});
	
	$("#addActionModal").on("hide.bs.modal",function(){
		$("input:checkbox").each(function () {  
            this.checked = false;
        });
		$("#addActionModal form").find("input[name='actionname']").val("");
		$("#addActionModal form").find("textarea[name='intro']").val("");
		$(".a").find("img.previewthumb").attr("src", "../Resources/default.jpg");
		$(".a").find("input:hidden").val("../Resources/default.jpg");
		$(".ajax-uploader").find("img.preview").attr("src", "../Resources/default.jpg");
		$(".ajax-uploader").find("input:hidden").val("../Resources/default.jpg");
		$("#addActionModal form").find("input[name='actionname']").popover("hide");
	});
	
	$("#addActionModal form").find("input[name='actionname']").change(function(){
		var name = $("#addActionModal form").find("input[name='actionname']").val();
		var url = "../Control/Dumbbell.php?action=video&operate=validateRename";
		$.post(url,{name:name},function(data){
			var obj = eval('('+data+')');
			$("#addActionModal form").find("input[name='actionname']").popover({
				trigger:'manual',
				placement : 'right',
				content:"<div style='white-space:nowrap;color:red'>"+obj['content']+"</div>",
				html:true,
			});
			if(obj['state']=="0"){
				$("#addActionModal form").find("input[name='actionname']").popover("show");
				rename = "1";
			}
			else{
				rename = "0";
				$("#addActionModal form").find("input[name='actionname']").popover("destroy");
			}
		});
	});
	
	$(document).on("submit", "#addActionModal form", function(e) {
		e.preventDefault();
		var form = $(this);
		var modal = $(this).closest(".modal");
		var btn = modal.data("target");
		var name = form.find("input[name='actionname']").val();
		var pic = form.find("input[name='avatar']").val().replace("..","");
		var intro = form.find("textarea[name='intro']").val();
		var thumb = form.find("input[name='thumb']").val().replace("..","");
		var str = "";
		$("input:checkbox").each(function(){ 
            if(this.checked){
                str += $(this).val()+","
            }
        })
        str = str.substr(0,str.length-1);
		var type = str;
		if(name==""){
			alert("名称不能为空");
			return;
		}
		else if(rename=="1"){
			alert("名称重复");
			return;
		}
		else if(type==""){
			alert("类型不能为空");
			return;
		}
		$.post("../Control/Dumbbell.php?action=video&operate=add", {name:name,type:type,intro:intro,pic:pic,thumb,thumb},
			function(data) {
				var obj = eval('('+data+')');
				if(obj['state']=="1"){
					$(".modal").modal('hide');
					videoPageProc($("#video-list-pager"),1,20,"","");
				}
				alert(obj['content']);
				rename = "";
		})
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
					"<form style='display: inline-block;' method='post' target='uploadthumb' action='" + uploader.data("url") + "' enctype='multipart/form-data'>" +
						"<div class='form-group'><label>文件上传</label><input type='file' name='" + uploader.data("name") + "' /></div>" +
						"<div class='form-group'><img id='previewthumbImg' style='display: none;' class='ajax-loader' src='../Resources/loader.gif' width='30' height='30' />" +
						"<span class='btn btn-warning'>取消上传图片</span>" +
					"</form>" +
				"</div>" +
			"</div></div></div>");
		var iframe = $("<iframe name='uploadthumb' id='uploadthumb' style='width: auto; height: auto; overflow: scroll; border: none;'><span class='loader'></span></iframe>");
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
			var path = $("#previewthumbImg").attr("src");
			iframe.remove();
			modal.modal('hide');
			uploader.find("img.previewthumb").attr("src", path);
			uploader.find("input:hidden").val(path);
		});
		btn.click(function() {
			uploader.find(".a").hide();
			iframe.remove();
			modal.modal('hide');
		});
	});
	$(document).on("setImgSrc", ".a img.previewthumb", function(e, src) {
		var path = $(this).attr("src");
		path = path.substring(0, path.lastIndexOf("/") + 1);
		$(this).attr("src", path + src);
		$(this).siblings("input").val(src);
	});
	
	$("#showAll").click(function(){
		$("#video_content").html("");
		videoPageProc($("#video-list-pager"),1,20,"","");
	});
	
	$("#searchBtn").click(function(){
		$("#video_content").html("");
		var searchItem = $("#condition").val();
		var searchText = $("#detail").val();
		videoPageProc($("#video-list-pager"),1,20,searchItem,searchText);
	});
});