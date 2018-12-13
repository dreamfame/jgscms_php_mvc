/**
 * 
 */
$(document).ready(function(){
	$.get("../ajax.PHP/judgeSession.php",function(data){
		if(data=="1"){
			window.location.href = "../View/login.html";
		}
	});
});