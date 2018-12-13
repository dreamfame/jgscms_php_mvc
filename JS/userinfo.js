function userPageProc(pager, page, pageSize,condition,conditionText) {
	$.get("../Control/Dumbbell.php?action=user&operate=conditionQuery", {condition:condition,conditionText:conditionText,page: page, pageSize: pageSize}, function(data) {
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
				var age = getAge(list[i]['birth']);
				html += "<tr data-src='"+list[i]['headimg']+"' data-id='" + list[i]['userId'] + "'><td>" + ((page - 1) * pageSize + i + 1) + "</td><td><span style='color:#BC8F8F;cursor:pointer;' onmouseover='showhead(this)' onmouseout='hidehead(this)'>"
					+ list[i]['name'] + "</span></td><td>"
					+ age + "</td><td>"
					+ list[i]['sex']+"</td><td>"
					+ list[i]['phone'] +"</td></tr>"
			}
			$("#userinfo_content").html(html);
			pager.data("pageProc", userPageProc).data("pageSize", pageSize).data("page", page).data("total", totalPages).trigger("paging");
		}
		else{
			$("#userinfo_content").html("");
		}
	});
}

function showhead(obj){
	var src = "../Resources/headimg/"+$(obj).closest('tr').attr("data-src");
	if(src=="")return;
	$(obj).popover({
		trigger:'manual',
		placement : 'right',
		content:"<img class='img-thumbnail' src='"+src+"' />",
		html:true,
	});
	$(obj).popover("show");
}

function hidehead(obj){
	$(obj).popover("hide");
}

	function setCondition(type){
		$("#detail").attr("disabled","disabled");
		$("#detail").css("display","");
 	    $("#condition").html("<option value=0>搜索条件...</option>");
		var select = document.getElementById('condition');
		select.options.add(new Option("姓名","name"));
		select.options.add(new Option("电话","phone"));
	}
	
	
	
	$(document).ready(function(){
		$("#1").attr("class","active");
		$("#0").attr("class","");
		$("#5").attr("class","");
		$("#3").attr("class","");
		$("#4").attr("class","");
		$("#6").attr("class","");
		$("#7").attr("class","");
		
		setCondition();
		
		userPageProc($("#user-list-pager"),1,20,"","");
		
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
			}
		});
		
		$("#showAll").click(function(){
			$("#userinfo_content").html("");
			userPageProc($("#user-list-pager"),1,20,"","");
		});
		
		$("#searchBtn").click(function(){
			$("#userinfo_content").html("");
			var searchItem = $("#condition").val();
			var searchText = $("#detail").val();
			userPageProc($("#user-list-pager"),1,20,searchItem,searchText);
		});
	});
	
 
	
	