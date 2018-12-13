/**
 * 
 */


function recommendPageProc(pager, page, pageSize,condition,conditionText) {
	$.get("../Control/Dumbbell.php?action=recommend&operate=conditionQuery", {condition:condition,conditionText:conditionText,page: page, pageSize: pageSize}, function(data) {
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
				html += "<tr data-id='" + list[i]['planlistid'] + "'><td>" + ((page - 1) * pageSize + i + 1) + "</td><td>"
					+ list[i]['type'] + "</td><td>"
					+ "<span class='btn btn-primary btn-sm glyphicon glyphicon-list' data-toggle='modal' data-target='#planlistModal'>&nbsp;计划列表</span></td></tr>"
			}
			$("#recommend_content").html(html);
			pager.data("pageProc", recommendPageProc).data("pageSize", pageSize).data("page", page).data("total", totalPages).trigger("paging");
		}
		else{
			$("#recommend_content").html("");
		}
	});
}

function setCondition(){
		$("#detail").attr("disabled","disabled");
		$("#detail").css("display","");
		$("#condition").html("<option value=0>搜索条件...</option>");
		var select = document.getElementById('condition');
		select.options.add(new Option("类型","type"));
}

function delPlanlist(obj){
	if(confirm("确定删除该条数据吗？"))
	{
		var id = $(obj).closest("tr").data("id");
		var url = "../Control/Dumbbell.php?action=recommend&operate=delplanlist";
		$.post(url,{id:id},function(data){
			alert(data);
			$(".modal").modal("hide");
		});
	}
}

$(document).ready(function(){
	$("#3").attr("class","active");
	$("#0").attr("class","");
	$("#2").attr("class","");
	$("#1").attr("class","");
	$("#4").attr("class","");
	$("#5").attr("class","");
	$("#6").attr("class","");
	$("#7").attr("class","");
	
	var typeStr = "";
	
	setCondition();
	
	recommendPageProc($("#recommend-list-pager"),1,20,"","");
	
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
	
	$(document).on("paging", ".ajaxPager", function() {
		var total = $(this).data("total");
		var page = $(this).data("page");
		var pageLinks = "";
		
		if (page > 1) {
			pageLinks += "<li><a data-page='" + 1 + "'>«</a></li>";
			pageLinks += "<li><a data-page='" + (page - 1) + "'>‹</a></li>";
		}
		if (page <= 3 || total <= 5) {
			for (var i = 1; i <= 5 && i <= total; i++) {
				if (i == page) pageLinks += "<li class='active'><a data-page='" + i + "'>" + i + "</a></li>";
				else pageLinks += "<li><a data-page='" + i + "'>" + i + "</a></li>";
			}        		
		} else if (page > total - 2) {
			for (var i = total - 4; i <= total; i++) {
				if (i == page) pageLinks += "<li class='active'><a data-page='" + i + "'>" + i + "</a></li>";
				else pageLinks += "<li><a data-page='" + i + "'>" + i + "</a></li>";
			}
		} else {
			for (var i = page - 2; i <= page + 2; i++) {
				if (i == page) pageLinks += "<li class='active'><a data-page='" + i + "'>" + i + "</a></li>";
				else pageLinks += "<li><a data-page='" + i + "'>" + i + "</a></li>";
			}
		}
		if (page < total) {
			pageLinks += "<li><a data-page='" + (1 + page) + "'>›</a></li>";
			pageLinks += "<li><a data-page='" + total + "'>»</a></li>";
		}
		
		$(this).html('<nav><ul class="pagination">' + pageLinks + '</ul></nav>');		
	});
	
	$("#video option").mouseover(function(){
		var id = $(this).val();
	});
	
	$(document).on("click", ".ajaxPager a", function(e) {
		var $pager = $(this).closest("。ajaxPager");
		$pager.data("pageProc")($pager, $(this).data("page"), $pager.data("pageSize"));
		e.preventDefault();
	});
	
	$("#showAll").click(function(){
		$("#recommend_content").html("");
		recommendPageProc($("#recommend-list-pager"),1,20,"","");
	});
	
	$("#searchBtn").click(function(){
		$("#recommend_content").html("");
		var searchItem = $("#condition").val();
		var searchText = $("#detail").val();
		recommendPageProc($("#recommend-list-pager"),1,20,searchItem,searchText);
	});
	
	$(document).on("show.bs.modal", "#planlistModal", function(e) {
		var btn = $(e.relatedTarget);
		typeStr = e.relatedTarget.parentNode.parentNode.childNodes[1].innerHTML;
		var id = btn.closest("tr").data("id");
		$(this).data("id", id);
		$(this).data("target", btn);
		$("#addplan").attr("pid",id);
		planlistPageProc($("#planlist-list-pager"), 1, 20);
	});
	
	function planlistPageProc(pager, page, pageSize) {
		var id = pager.closest(".modal").data('id');
		$.get("../Control/Dumbbell.php?action=recommend&operate=query&planlistid=" + id, {page: page, pageSize: pageSize}, function(data) {
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
						+ list[i]['video'] + "</td><td>" + list[i]['times'] + "</td><td><span class='btn btn-danger' onclick='delPlanlist(this)'>删除</span></td></tr>"
				}
				$("#planlist-list").html(html);
				pager.data("pageProc", planlistPageProc).data("pageSize", pageSize).data("page", page).data("total", totalPages).trigger("paging");
			}
			else{
				$("#planlist-list").html("");
			}
		});
	}
	
	$("#video").on("click","li",function(){
		$("#actionStr").attr("data-id",$(this).data("id"));
		$("#actionStr").html($(this).text());
	});
	
	$("#video").on("mouseover","li",function(){
		var id = $(this).data("id");
		var o = $(this);
		$.post("../Control/Dumbbell.php?action=video&operate=query",{id:id},function(data){
			var obj = eval('('+data+')');
			if(obj['state']=="1"){
				var src = obj['content'][0].gifsrc;
				o.popover({
					trigger:'manual',
					placement : 'right',
					content:"<img style='zoom:0.5' src='.."+src+"' />",
					html:true,
				});
				o.popover("show");
			}
		});
	});
	
	$("#video").on("mouseout","li",function(){
		$(this).popover("destroy");
	});
	
	$(document).on("show.bs.modal", "#addPlanModal", function(e) {
		$("#video").html("");
		$("#actionStr").attr("data-id","");
		$("#actionStr").text("动作");
		var url = "../Control/Dumbbell.php?action=video&operate=TypeQuery";
		$.post(url,{type:typeStr},function(data){
			var obj = eval('('+data+')');
			if(obj['state']=="1"){
				for (var i = 0; i < obj['content'].length; i++) {
					var option = "<li data-id='"+obj['content'][i].id+"' role='presentation'><a role='menuitem'  tabindex='-1'>"+obj['content'][i].name+"</a></li>";
					$("#video").append(option);
				}
			}
			else{
				$("#video").html("");
			}
		});
		planlistPageProc($("#planlist-list-pager"), 1, 20);
	});
	
	$(document).on("submit", "#addPlanModal form", function(e) {
		e.preventDefault();
		var form = $(this);
		var modal = $(this).closest(".modal");
		var btn = modal.data("target");
		var video = $("#actionStr").data("id");
		var times = form.find("input[name='times']").val();
		var planlistid = $("#addplan").attr("pid");
		if(times==""||times=="0"){
			alert("次数不能为空或0");
			return;
		}
		$.post("../Control/Dumbbell.php?action=recommend&operate=addplan", {video:video,times:times,planlistid:planlistid},
			function(data) {
				var obj = eval('('+data+')');
				if(obj['state']=="1"){
					form.find("input[name='times']").val("");
					$(".modal").modal('hide');
				}
				$("#addplan").attr("pid","");
				$("#actionStr").attr("data-id","");
				alert(obj['content']);
		})
	});
});