/**
 * 
 */
var state = "";

$(document).ready(function(){
	$("#2").attr("class","active");//菜单栏活动状态
	$("#0").attr("class","");
	$("#1").attr("class","");
	$("#3").attr("class","");
	$("#4").attr("class","");
	
	$("#condition").change(function(){//搜索条件
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
	
	$("#showAll").click(function(){//显示全部
		$("#detail").val("");
		$("#sportinfo_content").html("");
		getSportsInfo(1);
		Paging("","","");
		state = "all";
	});
	
	$("#searchBtn").click(function(){//查询按钮
		$("#sportinfo_content").html("");
		Search(1);
	});
});

function ShowData(){
	state = "all";
	getSportsInfo(1);
	Paging("","","");
}

var searchItem = "";
var condition1 = "";
var condition2 = "";


function Search(){//查询
	state = "search";
	var conditionTxt = $("#condition").find("option:selected").text();
	if(conditionTxt == "时间"){
		searchItem = $("#condition").val();
		var startTime = $("#startTime").val();
		var endTime = $("#endTime").val();
		condition1 = startTime;
		condition2 = endTime;
		if(startTime==""||endTime==""){
			alert("请选择起止时间");
			return;
		}
		Paging(searchItem,startTime,endTime);
		var url = "../Control/Dumbbell.php?action=sports&operate=conditionQuery&condition="+searchItem+"&conditionText1="+startTime+"&conditionText2="+endTime+"&page="+1;
		$.get(url,function(data){
			var obj = eval('('+data+')');
			if(obj["state"]=="0"){
				alert("获取信息失败");
			}
			else{
				var num = 0;
				$("#table_head").html("<th>序号</th><th>Id</th><th>用户Id</th><th>时间</th><th>手臂伸展</th><th>抡臂运动</th><th>伸颈回望</th><th>展臂扭腰</th><th>扩胸运动</th><th>完成时间</th><th>编辑</th><th>删除</th>");
				for(var a in obj["content"]){
					num++;
					var sports = obj["content"][a];
					var td = "<td>"+num+"</td><td>"+sports.id+"</td><td>"+sports.userId+"</td><td>"+sports.time+"</td><td id='spa"+num+"'>"+sports.sbsz+"</td><td id='spb"+num+"'>"+sports.lbyd+"</td><td id='spc"+num+"'>"+sports.sjhw+"</td><td id='spd"+num+"'>"+sports.zbny+"</td><td id='spe"+num+"'>"+sports.kxyd+"</td><td id='spf"+num+"'>"+sports.ftime+"<td id='speditb"+num+"'><span id='speditbtn"+num+"' onclick='edit_user("+num+")' class='btn btn-sm btn-warning reset-password '><span class='glyphicon glyphicon-refresh'></span>"+"编辑"+"</span></td>"+"<td id='spdelet"+num+"' ><span onclick='deletfk_user("+num+")'  class='btn btn-sm btn-danger delete-specialist'><span class='glyphicon glyphicon-remove-circle'></span>"+"删除"+"</span></td>";
					$("#sportinfo_content").append("<tr data-id='"+sports.id+"'>"+td+"</tr>");
				}
			}
		});
	}
	else{
		searchItem = $("#condition").val();
		var searchText = $("#detail").val();
		condition1 = searchText;
		condition2 = "";
		Paging(searchItem,searchText,"");
		var url = "../Control/Dumbbell.php?action=sports&operate=conditionQuery&condition="+searchItem+"&conditionText1="+searchText+"&conditionText2="+"&page="+1;
		$.get(url,function(data){
			var obj = eval('('+data+')');
			if(obj["state"]=="0"){
					alert("获取信息失败");
			}
			else{
				var num = 0;
				$("#table_head").html("<th>序号</th><th>Id</th><th>用户Id</th><th>时间</th><th>手臂伸展</th><th>抡臂运动</th><th>伸颈回望</th><th>展臂扭腰</th><th>扩胸运动</th><th>完成时间</th><th>编辑</th><th>删除</th>");
				for(var a in obj["content"]){
					num++;
					var sports = obj["content"][a];
					var td = "<td>"+num+"</td><td>"+sports.id+"</td><td>"+sports.userId+"</td><td>"+sports.time+"</td><td id='spa"+num+"'>"+sports.sbsz+"</td><td id='spb"+num+"'>"+sports.lbyd+"</td><td id='spc"+num+"'>"+sports.sjhw+"</td><td id='spd"+num+"'>"+sports.zbny+"</td><td id='spe"+num+"'>"+sports.kxyd+"</td><td id='spf"+num+"'>"+sports.ftime+"<td id='speditb"+num+"'><span id='speditbtn"+num+"' onclick='edit_user("+num+")' class='btn btn-sm btn-warning reset-password '><span class='glyphicon glyphicon-refresh'></span>"+"编辑"+"</span></td>"+"<td id='spdelet"+num+"' ><span onclick='deletfk_user("+num+")'  class='btn btn-sm btn-danger delete-specialist'><span class='glyphicon glyphicon-remove-circle'></span>"+"删除"+"</span></td>";
					$("#sportinfo_content").append("<tr data-id='"+sports.id+"'>"+td+"</tr>");
				}
			}
		});
	}
}

function ChangePage(page){//换页
	$("#sportinfo_content").html("");
	if(state=="all"){
		getSportsInfo(page);
	}else{
		    var url = "../Control/Dumbbell.php?action=sports&operate=conditionQuery&condition="+searchItem+"&conditionText1="+condition1+"&conditionText2="+condition2+"&page="+page;
	        $.get(url,function(data){
			var obj = eval('('+data+')');
			if(obj["state"]=="0"){
				alert("获取信息失败");
			}
			else{
				var num = 0;
				$("#table_head").html("<th>序号</th><th>Id</th><th>用户Id</th><th>时间</th><th>手臂伸展</th><th>抡臂运动</th><th>伸颈回望</th><th>展臂扭腰</th><th>扩胸运动</th><th>完成时间</th><th>编辑</th><th>删除</th>");
				for(var a in obj["content"]){
					num++;
					var sports = obj["content"][a];
					var No = parseInt(num+(page-1)*10);
					
					var td = "<td>"+No+"</td><td>"+sports.id+"</td><td>"+sports.userId+"</td><td>"+sports.time+"</td><td id='spa"+num+"'>"+sports.sbsz+"</td><td id='spb"+num+"'>"+sports.lbyd+"</td><td id='spc"+num+"'>"+sports.sjhw+"</td><td id='spd"+num+"'>"+sports.zbny+"</td><td id='spe"+num+"'>"+sports.kxyd+"</td><td id='spf"+num+"'>"+sports.ftime+"<td id='speditb"+num+"'><span id='speditbtn"+num+"' onclick='edit_user("+num+")' class='btn btn-sm btn-warning reset-password '><span class='glyphicon glyphicon-refresh'></span>"+"编辑"+"</span></td>"+"<td id='spdelet"+num+"' ><span onclick='deletfk_user("+num+")'  class='btn btn-sm btn-danger delete-specialist'><span class='glyphicon glyphicon-remove-circle'></span>"+"删除"+"</span></td>";
					$("#sportinfo_content").append("<tr data-id='"+sports.id+"'>"+td+"</tr>");
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
		}else{
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
	}else{
		$("#nextPage").css("display","");
		$("#lastPage").css("display","");
		$("#forwardPage").css("display","");
		$("#firstPage").css("display","");
	}
}

var pageNum = 0;

function Paging(searchItem,param1,param2){//分页栏显示
	$(".pagination").html("");
	var url = "../Control/Dumbbell.php?action=sports&operate=paging&condition="+searchItem+"&conditionText1="+param1+"&conditionText2="+param2;
	$.post(url,function(data){
		var obj = eval('('+data+')');
		var totalNum = obj["content"];
		if(totalNum<10){
			pageNum = 1;
		}
		else{
			if(totalNum%10==0)
			{
				pageNum = parseInt(totalNum/10);
			}else{
				pageNum = parseInt(totalNum/10)+1;
			}
		}
		for(var i = 1;i<=pageNum;i++){
			if(i==1){
				$(".pagination").append("<li><a id='firstPage' style='display:none;' data-page='1'>«</a></li><li><a id='forwardPage' style='display:none;' data-page='1'>‹</a></li><li id='page"+i+"' class='active'><a data-page='"+i+"'>"+i+"</a></li>");
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
				$(".pagination").append("<li id='page"+i+"'><a data-page='"+i+"'>"+i+"</a></li>");
			}
			$("#page"+i).click(function(){
				var page = $("#"+this.id).find("a").attr("data-page");
				ChangePage(page);
				SetActive(pageNum,page);
			});
		}
		if(pageNum>1){
			$(".pagination").append("<li><a id='nextPage' data-page='2'>›</a></li><li><a id='lastPage' data-page='"+pageNum+"'>»</a></li>");
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

function getSportsInfo(page){//获取信息
	$("#userinfo_content").html("");
	setCondition(2);
	$("#showAll").attr("page","2");
	$("#searchBtn").attr("page","2");
	$("#editbtn").attr("page","2");
	var url = "../Control/Dumbbell.php?action=sports&operate=all&page="+page;
	$.get(url,function(data){
		var obj = eval('('+data+')');
		if(obj["state"]=="0"){
			alert("获取信息失败");
		}
		else{
			var num = 0;
			$("#table_head").html("<th>序号</th><th>Id</th><th>用户Id</th><th>时间</th><th>手臂伸展</th><th>抡臂运动</th><th>伸颈回望</th><th>展臂扭腰</th><th>扩胸运动</th><th>完成时间</th><th>编辑</th><th>删除</th>");
			for(var a in obj["content"]){
				num++;
				var sports = obj["content"][a];
				var No = parseInt(num+(page-1)*10);
				var td = "<td>"+No+"</td><td>"+sports.id+"</td><td>"+sports.userId+"</td><td>"+sports.time+"</td><td id='spa"+num+"'>"+sports.sbsz+"</td><td id='spb"+num+"'>"+sports.lbyd+"</td><td id='spc"+num+"'>"+sports.sjhw+"</td><td id='spd"+num+"'>"+sports.zbny+"</td><td id='spe"+num+"'>"+sports.kxyd+"</td><td id='spf"+num+"'>"+sports.ftime+"<td id='speditb"+num+"'><span id='speditbtn"+num+"' onclick='edit_user("+num+")' class='btn btn-sm btn-warning reset-password '><span class='glyphicon glyphicon-refresh'></span>"+"编辑"+"</span></td>"+"<td id='spdelet"+num+"' ><span onclick='deletfk_user("+num+")'  class='btn btn-sm btn-danger delete-specialist'><span class='glyphicon glyphicon-remove-circle'></span>"+"删除"+"</span></td>";
				$("#sportinfo_content").append("<tr data-id='"+sports.id+"'>"+td+"</tr>");
			}
		}
	});
}

function setCondition(type){
		$("#detail").attr("disabled","disabled");
		$("#sex1").css("display","none");
		$("#sex2").css("display","none");
		$("#man").css("display","none");
		$("#woman").css("display","none");
		$("#detail").css("display","");
		$("#startTime").css("display","none");
		$("#endTime").css("display","none");
		$("#condition").html("<option value=0>搜索条件...</option>");
		var select = document.getElementById('condition');
		switch(type){
			case 1:
				select.options.add(new Option("用户名","userName"));
				select.options.add(new Option("电话","phone"));
				$("#sex1").css("display","");
				$("#sex2").css("display","");
				$("#man").css("display","");
				$("#woman").css("display","");
				break;
			case 2:
				select.options.add(new Option("Id","id"));
				select.options.add(new Option("用户Id","userId"));
				select.options.add(new Option("时间","time"));
				break;
			case 3:
				select.options.add(new Option("Id","id"));
				select.options.add(new Option("用户Id","userId"));
				break;
			case 4:
				select.options.add(new Option("Id","id"));
				select.options.add(new Option("类型","type"));
				select.options.add(new Option("名称","name"));
				break;
		}
	}


function edit_user(num){//编辑记录
	var id = $("#speditb"+num).closest("tr").data("id");
	 if($("#speditb"+num).text()=="编辑"){
		 $("#speditbtn"+num).html("<span class='glyphicon glyphicon-refresh'></span>提交") ; 
			var span=$("#spa"+num).html();
			var spbn=$("#spb"+num).html();
			var spcn=$("#spc"+num).html();
			var spdn=$("#spd"+num).html();
			var spen=$("#spe"+num).html();
			 $("#spa"+num).html("<input style='width:100px' id='input-spa"+num+"' type='text' value='"+span+"'>");
			 $("#spb"+num).html("<input style='width:100px' id='input-spb"+num+"' type='text' value='"+spbn+"'>");
			 $("#spc"+num).html("<input style='width:100px' id='input-spc"+num+"' type='text' value='"+spcn+"'>");
			 $("#spd"+num).html("<input style='width:100px' id='input-spd"+num+"' type='text' value='"+spdn+"'>");
			 $("#spe"+num).html("<input style='width:100px' id='input-spe"+num+"' type='text' value='"+spen+"'>");
	 }
	 else {
		 if(confirm("确定提交修改数据吗？"))
		 {
			 var a = $("#input-spa"+num).val();
			 var b = $("#input-spb"+num).val();
			 var c = $("#input-spc"+num).val();
			 var d = $("#input-spd"+num).val();
			 var e = $("#input-spe"+num).val();
			 if(!isNaN(a)&&!isNaN(b)&&!isNaN(c)&&!isNaN(d)&&!isNaN(e))
			 {
				 $("#speditbtn"+num).html("<span class='glyphicon glyphicon-refresh'></span>编辑") ;
				 $("#spa"+num).html(a);
				 $("#spb"+num).html(b);
				 $("#spc"+num).html(c);
				 $("#spd"+num).html(d);
				 $("#spe"+num).html(e);
				 var url = "../Control/Dumbbell.php?action=sports&operate=edit";
				 $.post(url,{id:id,a:a,b:b,c:c,d:d,e:e},function(data){
					 var obj = eval('('+data+')');
					 alert(obj.content);
				 });
			 }
			 else{
				 alert("请输入数字！");
			 }
		 }
	 }
	 
}
	 
	function deletfk_user(num){//删除记录
		var id = $("#speditb"+num).closest("tr").data("id");	
		if(confirm("确定删除该条数据吗？"))
		{
			var url = "../Control/Dumbbell.php?action=sports&operate=del";
			$.post(url,{id:id},function(data){
				var obj = eval('('+data+')');
				//$("#speditb"+num).closest("tr").remove();
				alert(obj.content);
				window.location.reload();
			});
		}
	}


