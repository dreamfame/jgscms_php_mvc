$(document).ready(function(){
	$("#5").attr("class","active");
	$("#0").attr("class","");
	$("#2").attr("class","");
	$("#1").attr("class","");
	$("#3").attr("class","");
	$("#4").attr("class","");
	$("#6").attr("class","");
	$("#7").attr("class","");
	
	$.post("../Control/Dumbbell.php?action=integral&operate=query",function(data){
		var obj = eval('('+data+')');
		if(obj['state']=="1"){
			var html = "";
			var list = obj['content'];
			for (var i = 0; i < obj['content'].length; i++) {
				var integral = list[i]['integral']==null?0:list[i]['integral'];
				var name = list[i]['name'] == null?"无":list[i]['name'];
				html += "<li><i>" + (i + 1) + "</i><span style='padding:0 0 0 130px'>" + integral + "</span><span style='padding:0 0 0 80px'>" + list[i]['userid'] + "</span><b>" + name + "</b></li>";
			}
			$("#rankScoreSp").html(html);
		}
		else{
			$("#rankScoreSp").html("");
		}
	});
	
	$.post("../Control/Dumbbell.php?action=integral&operate=gettime",function(data){
		var obj = eval('('+data+')');
		if(obj['state']=="1"){
			var html = "";
			var list = obj['content'];
			for (var i = 0; i < obj['content'].length; i++) {
				var time = list[i]['time']==null?0:list[i]['time'];
				var name = list[i]['name'] == null?"无":list[i]['name'];
				html += "<li><i>" + (i + 1) + "</i><span style='padding:0 0 0 130px'>" + time + "</span><span style='padding:0 0 0 80px'>" + list[i]['userid'] + "</span><b>" + name + "</b></li>";
			}
			$("#rankTimeSp").html(html);
		}
		else{
			$("#rankTimeSp").html("");
		}
	});
});