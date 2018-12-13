/**
 * 
 */
$(document).ready(function(){
	loaddata();
	setInterval("loaddata()",1000);
	chartdata();
	
	$("#2").attr("class","");//菜单栏活动状态
	$("#0").attr("class","");
	$("#1").attr("class","");
	$("#3").attr("class","");
	$("#4").attr("class","");
	$("#5").attr("class","");
	$("#6").attr("class","");
	$("#7").attr("class","");
	$("#8").attr("class","");
	$("#9").attr("class","active");
	
	$(document).on("click", "#selectionul :checkbox", function() {
		var data = [];
		var label = [];
		$("input:checkbox").each(function () {  
			if(this.checked == true){
				if($(this).attr("data-id")=="0"){
					data.push(a);
					label.push("新用户");
				}
				if($(this).attr("data-id")=="1"){
					data.push(b);
					label.push("启动次数");
				}
				if($(this).attr("data-id")=="2"){
					data.push(c);
					label.push("启动用户");
				}
			}
        });
		$.post("../stat/stat.php?operate=chart",function(res){
			plot1.destroy();
			var max = 0;
			var obj = eval('('+res+')');
			max = parseInt(obj['total']);
			plot1 = j.jqplot.diagram.base("chart1", data, label, "时段分析", x, "", "", max+50, 1);
		});
	});
});

function loaddata(){
	$.post("../stat/stat.php?operate=get",function(data){
		var obj = eval('('+data+')');
		var y = parseInt(obj['total'])-parseInt(obj['tu']);
		var pt = (parseInt(obj['tu'])/parseInt(obj['total'])*100).toFixed(2)+"%";
		var py = (parseInt(obj['yu'])/y*100).toFixed(2)+"%";
		var html = "<tr><td>今日</td><td>"+obj['total']+"</td><td>"+obj['tt']+"</td><td>"+obj['tn']+
		"</td><td>"+obj['tu']+"</td><td>"+pt+"</td></tr><tr><td>昨日</td><td>"+y+"</td><td>"+obj['yt']+"</td><td>"+obj['yn']+"</td><td>"+obj['yu']+"</td><td>"+py+"</td></tr>";
		$("#stattable").find("tbody").html(html);
	});
}

function showArea(obj){
	if($(obj).html()=="查看全部"){
		$("#area").css("display","");
		$("#area").attr("src","area.html");
		$(obj).html("隐藏");
	}else{
		$("#area").css("display","none");
		$("#area").attr("src","");
		$(obj).html("查看全部");
	}
}

function DayNumOfMonth(year,month)
{
    return 32-new Date(year,month,32).getDate();
}

var plot1;
var a = [];
var b = [];
var c = [];
var empty = [];
var x = [];

function chartdata(){
	var d = new Date();
	var daynum = DayNumOfMonth(d.getFullYear(),d.getMonth());
	//var a = [];
	//var b = [];
	//var c = [];
	//var x = [];
	$.post("../stat/stat.php?operate=chart",function(data){
		var obj = eval('('+data+')');
		var d = new Date();
		var daynum = DayNumOfMonth(d.getFullYear(),d.getMonth());
		var num = 1;
		while(num<=daynum)
		{
			var value = 0;
			var tvalue = 0;
			var nvalue = 0;
			for(var one in obj['u']){
				if(obj['u'][one]['day']==num){
					value = obj['u'][one]['num'];
				}
			}
			for(var one in obj['s']){
				if(obj['s'][one]['date']==num){
					tvalue = obj['s'][one]['times'];
					nvalue = obj['s'][one]['no'];
				}
			}
			a.push(value);
			b.push(tvalue);
			c.push(nvalue);
			x.push(num);
			num++;
		}
		var data = [a,b,c];
		var data_max = parseInt(obj['total'])+50; //Y轴最大刻度
		var line_title = ["新用户","启动次数","启动用户"]; //曲线名称
		var y_label = ""; //Y轴标题
		var x_label = ""; //X轴标题
		var title = "这是标题"; //统计图标标题
		plot1 = j.jqplot.diagram.base("chart1", data, line_title, "时段分析", x, x_label, y_label, data_max, 1);
		otherdata();
	});
}

function otherdata(){
	$.post("../stat/stat.php?operate=area",function(data){
		var obj = eval(data);
		var sa = [];
		var s = [];
		var st = [];
		for(var one in obj){
			s.push(obj[one]['area']);
			sa.push(obj[one]['num']);
			st.push(obj[one]['times']);
		}
		var data2 = [sa,st];
		var data_max = 0;
		$.post("../stat/stat.php?operate=all",function(res){
			data_max = parseInt(res);
			var line_title = ["启动用户数","启动次数"];
			var y_label = ""; //Y轴标题
			var x_label = ""; //X轴标题
			j.jqplot.diagram.base("chart2", data2, line_title, "地区分析", s, x_label, y_label, data_max+50, 2);
		});
	});
}