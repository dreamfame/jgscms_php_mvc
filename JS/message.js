/**
 * 
 */
var state = "";
var messageBtn = "<button class='btn btn-default btn-sm' data-toggle='modal' data-target='#messageModal'><span class='glyphicon glyphicon-list-alt'></span>留言记录</button>";

function remindMsg(){
	$.post("../Control/Dumbbell.php?action=message&operate=remind",function(data){
		var obj = eval('('+data+')');
		if(obj['state']=="0"){
			$("#remindNum").text("");
			$("#remindcontent").html("<li><a>暂无新消息</a></li>");
		}
		else{
			$("#remindNum").text(obj['total']);
			var html = "";
			for(var a in obj["content"]){
				var remind = obj['content'][a];
				html += "<li><a id='"+remind['cid']+"' onclick='RemindSearch(this)'>"+remind['customer']+"<span id='remindNum' style='background-color:red' class='badge pull-right'>"+remind['remindNum']+"</a></li>";
			}
			$("#remindcontent").html(html);
		}
	});
}

$(document).ready(function(){
	$("#2").attr("class","");//菜单栏活动状态
	$("#0").attr("class","");
	$("#1").attr("class","");
	$("#3").attr("class","");
	$("#4").attr("class","");
	$("#5").attr("class","");
	$("#6").attr("class","");
	$("#7").attr("class","active");
	state = "all";
	ShowCustomer(1);
	Paging("","","");
	remindMsg();
	setInterval("remindMsg()", 3000);
	
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
	
	$("#showAll").click(function(){
		state = "all";
		$("#detail").val("");
		ShowCustomer(1);
	});
	
	$("#searchBtn").click(function(){
		state = "search";
		$("#userinfo_content").html("");
		//用户信息点击搜索
		Search(1);
	});
	
	function messagePageProc(pager, page, pageSize) {
		var id = pager.closest(".modal").data('id');
		$.get("../Control/Dumbbell.php?action=message&operate=query&userid=" + id, {page: page, pageSize: pageSize}, function(data) {
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
					var special = list[i]['special']==null?"无":list[i]['special'];
					var replytime =  list[i]['replytime']==null?"无":list[i]['replytime'];
					var reply = list[i]['reply']==null?"无":list[i]['reply'];
					var btn = list[i]['reply'] == null?"<span class='btn btn-primary btn-sm glyphicon glyphicon-edit' data-toggle='modal' data-target='#replyModal'>&nbsp;回复</span>":"已回复";
					html += "<tr data-id='" + list[i]['id'] + "'><td>" + ((page - 1) * pageSize + i + 1) + "</td><td>"
						+ list[i]['customer'] + "</td><td>"
						+ special + "</td><td>" + list[i]['message'] + "</td><td>" + reply + "</td><td>"
						+ list[i]['sendtime'] + "</td><td>" + replytime + "</td><td>"+btn
						+ "</td></tr>"
				}
				$("#message-list").html(html);
				pager.data("pageProc", messagePageProc).data("pageSize", pageSize).data("page", page).data("total", totalPages).trigger("paging");
			}
			else{
				$("#message-list").html("");
			}
		});
	}
	
	$(document).on("show.bs.modal", "#messageModal", function(e) {
		var btn = $(e.relatedTarget);
		var id = btn.closest("tr").data("id");
		$(this).data("id", id);
		$(this).data("target", btn);
		messagePageProc($("#message-list-pager"), 1, 20);
	});
	
	$(document).on("hidden.bs.modal", "#messageModal", function(e) {
		$(this).data('target').blur();
	});
	
	$(document).on("paging", "#message-list-pager", function() {
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
	
	$(document).on("click", "#message-list-pager a", function(e) {
		var $pager = $(this).closest("#message-list-pager");
		$pager.data("pageProc")($pager, $(this).data("page"), $pager.data("pageSize"));
		e.preventDefault();
	});
	
	$(document).on("show.bs.modal", "#replyModal", function(e){
		$(this).data("target", $(e.relatedTarget));
		var btn = $(e.relatedTarget);
		var id = btn.closest("tr").data("id");
	});
	
	$(document).on("submit", "#replyModal form", function(e) {
		e.preventDefault();
		var form = $(this);
		var modal = $(this).closest(".modal");
		var btn = modal.data("target");
		var id = btn.closest("tr").data("id");
		var replyMsg = form.find("textarea[name='replyMsg']").val();
		if(replyMsg==""){
			alert("回复内容不能为空");
			return;
		}
		$.post("../Control/Dumbbell.php?action=message&operate=reply&id=" + id, {replyMsg: replyMsg},
				function() {
				alert("回复完成！");
				form.find("textarea[name='replyMsg']").val("");
				$(".modal").modal('hide');
		})
	});
});

var searchItem = "";
var condition1 = "";
var condition2 = "";

function Search(page){
	var searchItem = $("#condition").val();
	var searchText = $("#detail").val();
	var url = "../Control/Dumbbell.php?action=message&operate=conditionQuery&condition="+searchItem+"&conditionText="+searchText+"&page="+page;
	$.get(url,function(data){
		var obj = eval('('+data+')');
		if(obj["state"]=="0"){
			$("#message_content").html("");
		}
		else{
			var num=0;
	    	$("#table_head").html("<th>序号</th><th>姓名</th><th>性别</th><th>手机号</th><th>座机</th><th>年龄</th><th>操作</th></hr>");
			for(var a in obj["content"]){
				num++;
				var customer = obj['content'][a];
				var No = parseInt(num+(page-1)*20);
				var td = "<tr data-id='"+customer.id+"'><td>"+No+"</td><td id='cname"+num+"'>"+customer.name+"</td><td id='sex"+num+"'>"+customer.sex+"</td><td id='phone"+num+"'>"+customer.phone+"</td><td id='telephone"+num+"'>"+customer.telephone+"</td><td id='age"+num+"'>"+customer.age+"</td><td>"+messageBtn+"</td><tr>";
				$("#message_content").append("<tr>"+td+"</tr>");
			}
		}
	});
}

function RemindSearch(obj){
	$("#message_content").html("");
	var searchItem = "id";
	var searchText = obj.id;
	var url = "../Control/Dumbbell.php?action=customer&operate=conditionQuery&condition="+searchItem+"&conditionText="+searchText+"&page=1";
	$.get(url,function(data){
		var obj = eval('('+data+')');
		if(obj["state"]=="0"){
			$("#message_content").html("");
		}
		else{
			var num=0;
			for(var a in obj["content"]){
				num++;
				var customer = obj['content'][a];
				var No = parseInt(num+(1-1)*20);
				var td = "<tr data-id='"+customer.id+"'><td>"+No+"</td><td id='cname"+num+"'>"+customer.name+"</td><td id='sex"+num+"'>"+customer.sex+"</td><td id='phone"+num+"'>"+customer.phone+"</td><td id='telephone"+num+"'>"+customer.telephone+"</td><td id='age"+num+"'>"+customer.age+"</td><td>"+messageBtn+"</td><tr>";
				$("#message_content").append("<tr>"+td+"</tr>");
			}
		}
	});
}

function setCondition(type){
		$("#detail").attr("disabled","disabled");
     	$("#man").css("display","none");
		$("#woman").css("display","none");
		$("#detail").css("display","");
		$("#startTime").css("display","none");		
		$("#endTime").css("display","none");
 	    $("#condition").html("<option value=0>搜索条件...</option>");
		var select = document.getElementById('condition');
		switch(type){
 		case 1:
				select.options.add(new Option("电话","phone"));
				$("#man").css("display","");
				$("#woman").css("display","");
				break;			
			case 2:
				select.options.add(new Option("Id","id"));
				select.options.add(new Option("用户Id","customerId"));
				select.options.add(new Option("时间","time"));
				break;
			case 3:
				select.options.add(new Option("Id","id"));
				select.options.add(new Option("用户Id","customerId"));
				break;
			case 4:
				select.options.add(new Option("Id","id"));
				select.options.add(new Option("类型","type"));
				select.options.add(new Option("名称","name"));
				break;
		}
	}

function ShowCustomer(page){
	$("#message_content").html("");
	setCondition(1);
	$("#showAll").attr("page","1");
	$("#searchBtn").attr("page","1");
	$("#editbtn").attr("page","1");
	var url = "../Control/Dumbbell.php?action=customer&operate=conditionQuery&condition=&conditionText=&page="+page;
	$.get(url,function(data){ 
		var obj = eval('(' + data + ')');
		if(obj["state"]=="0"){
			alert("获取信息失败");
		}
		else{
			var num=0;
	    	$("#table_head").html("<th>序号</th><th>姓名</th><th>性别</th><th>手机号</th><th>座机</th><th>年龄</th><th>操作</th></hr>");
			for(var a in obj["content"]){
				num++;
				var customer = obj['content'][a];
				var No = parseInt(num+(page-1)*20);
				var td = "<tr data-id='"+customer.id+"'><td>"+No+"</td><td id='cname"+num+"'>"+customer.name+"</td><td id='sex"+num+"'>"+customer.sex+"</td><td id='phone"+num+"'>"+customer.phone+"</td><td id='telephone"+num+"'>"+customer.telephone+"</td><td id='age"+num+"'>"+customer.age+"</td><td>"+messageBtn+"</td><tr>";
				$("#message_content").append("<tr>"+td+"</tr>");
			}
		}
	});
}

function ChangePage(page){//换页
	$("#message_content").html("");
	if(state=="all"){
		ShowCustomer(page);
	}else{
		var url = "../Control/Dumbbell.php?action=customer&operate=conditionQuery&condition="+searchItem+"&conditionText1="+condition1+"&conditionText2="+condition2+"&page="+page;
		$.get(url,function(data){
			var obj = eval('('+data+')');
			if(obj["state"]=="0"){
				alert("获取信息失败");
			}
			else{
				var num = 0;
				$("#table_head").html("<th>序号</th><th>姓名</th><th>性别</th><th>手机号</th><th>座机</th><th>年龄</th><th>操作</th></hr>");
				for(var a in obj["content"]){
					num++;
					var sports = obj["content"][a];
					var No = parseInt(num+(page-1)*20);
					var td = "<tr data-id='"+customer.id+"'><td>"+No+"</td><td id='cname"+num+"'>"+customer.name+"</td><td id='sex"+num+"'>"+customer.sex+"</td><td id='phone"+num+"'>"+customer.phone+"</td><td id='telephone"+num+"'>"+customer.telephone+"</td><td id='age"+num+"'>"+customer.age+"</td><td>"+messageBtn+"</td><tr>";
					$("#message_content").append("<tr>"+td+"</tr>");
				}
			}
		});
	}
	if(page==1){
		if(pageNum>1){
			$("#nextPage").css("display","");
			$("#lastPage").css("display","");
			$("#forwardPage").css("display","none");
			$("#firstPage").css("display","none");
			$("#page1").find("a").css("border-top-left-radius","4px");
			$("#page1").find("a").css("border-bottom-left-radius","4px");
			$("#page1").find("a").css("border-top-right-radius","0");
			$("#page1").find("a").css("border-bottom-right-radius","0");
			$("#page"+pageNum).find("a").css("border-top-left-radius","0");
			$("#page"+pageNum).find("a").css("border-bottom-left-radius","0");
			$("#page"+pageNum).find("a").css("border-top-right-radius","0");
			$("#page"+pageNum).find("a").css("border-bottom-right-radius","0");
		}else{
			$("#page1").find("a").css("border-top-left-radius","4px");
			$("#page1").find("a").css("border-top-right-radius","4px");
			$("#page1").find("a").css("border-bottom-left-radius","4px");
			$("#page1").find("a").css("border-bottom-right-radius","4px");
			$("#nextPage").css("display","none");
			$("#lastPage").css("display","none");
			$("#forwardPage").css("display","none");
			$("#firstPage").css("display","none");
		}
	}else if(page==pageNum){
		$("#nextPage").css("display","none");
		$("#lastPage").css("display","none");
		$("#forwardPage").css("display","");
		$("#firstPage").css("display","");
		$("#page1").find("a").css("border-top-right-radius","0");
		$("#page1").find("a").css("border-bottom-right-radius","0");
		$("#page1").find("a").css("border-top-left-radius","0");
		$("#page1").find("a").css("border-bottom-left-radius","0");
		$("#page"+pageNum).find("a").css("border-top-right-radius","4px");
		$("#page"+pageNum).find("a").css("border-top-left-radius","0");
		$("#page"+pageNum).find("a").css("border-bottom-left-radius","0");
		$("#page"+pageNum).find("a").css("border-bottom-right-radius","4px");
	}else{
		$("#nextPage").css("display","");
		$("#lastPage").css("display","");
		$("#forwardPage").css("display","");
		$("#firstPage").css("display","");
		$("#page1").find("a").css("border-top-right-radius","0");
		$("#page1").find("a").css("border-bottom-right-radius","0");
		$("#page1").find("a").css("border-top-left-radius","0");
		$("#page1").find("a").css("border-bottom-left-radius","0");
		$("#page"+pageNum).find("a").css("border-top-right-radius","0");
		$("#page"+pageNum).find("a").css("border-top-left-radius","0");
		$("#page"+pageNum).find("a").css("border-bottom-left-radius","0");
		$("#page"+pageNum).find("a").css("border-bottom-right-radius","0");
	}
}

var pageNum = 0;

function Paging(searchItem,param1,param2){//分页栏显示
	$("#customer-list-pager .pagination").html("");
	var url = "../Control/Dumbbell.php?action=customer&operate=paging&condition="+searchItem+"&conditionText1="+param1+"&conditionText2="+param2;
	$.post(url,function(data){
		var obj = eval('('+data+')');
		var totalNum = obj["content"];
		if(totalNum<20){
			pageNum = 1;
		}
		else{
			if(totalNum%20==0)
			{
				pageNum = parseInt(totalNum/20);
			}else{
				pageNum = parseInt(totalNum/20)+1;
			}
		}
		for(var i = 1;i<=pageNum;i++){
			if(i==1){
				$("#customer-list-pager .pagination").append("<li><a id='firstPage' style='display:none;' data-page='1'>«</a></li><li><a id='forwardPage' style='display:none;' data-page='1'>‹</a></li><li id='page"+i+"' class='active'><a style='border-top-left-radius:4px;border-bottom-left-radius:4px;' data-page='"+i+"'>"+i+"</a></li>");
				$("#forwardPage").click(function(){
					var page = $("#forwardPage").attr("data-page");
					ChangePage(page);
					$("#nextPage").attr("data-page",parseInt(page)-1);
					SetActive(pageNum,page);
				});
				$("#firstPage").click(function(){
					var page = 1;
					$("#forwardPage").attr("data-page",1);
					$("#nextPage").attr("data-page",2);
					ChangePage(page);
					SetActive(pageNum,page);
				});
			}
			else{
				$("#customer-list-pager .pagination").append("<li id='page"+i+"'><a data-page='"+i+"'>"+i+"</a></li>");
			}
			$("#page"+i).click(function(){
				var page = $("#"+this.id).find("a").attr("data-page");
				ChangePage(page);
				SetActive(pageNum,page);
			});
		}
		if(pageNum>1){
			$("#customer-list-pager .pagination").append("<li><a id='nextPage' data-page='2'>›</a></li><li><a id='lastPage' data-page='"+pageNum+"'>»</a></li>");
			$("#nextPage").click(function(){
				var page = $("#nextPage").attr("data-page");
				ChangePage(page);
				$("#nextPage").attr("data-page",parseInt(page)+1);
				$("#forwardPage").attr("data-page",parseInt(page)-1);
				SetActive(pageNum,page);
			});
			$("#lastPage").click(function(){
				var page = $("#lastPage").attr("data-page");
				ChangePage(page);
				SetActive(pageNum,page);
			});
		}
	});
}

function SetActive(pageNum,page){
	for(var j=1;j<=pageNum;j++){
		if(j==page){
			$("#page"+j).addClass("active");
			$("#nextPage").attr("data-page",parseInt(page)+1);
		}
		else{
			$("#page"+j).removeClass("active");
		}
	}
}