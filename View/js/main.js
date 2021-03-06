layui.config({
	base : "js/"
}).use(['form','element','layer','jquery','util'],function(){
	var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		element = layui.element(),
        util = layui.util;
		$ = layui.jquery;

    util.fixbar({
        bar1: true
        ,click: function(type){
            if(type === 'bar1'){
                console.log($("#sendBug"));
                $("#sendBug").click();
            }
        }
    })

	$(".panel a").on("click",function(){
        var id = $(this).attr("id");
        if(id=="picall"||id=="picwait"){
            if(role=="内容管理员"){
                layer.msg("对不起，您没有内容权限");
            }
            else{
                window.parent.addTab($(this));
            }
        }
        else if(id=="newsall"){
            if(role=="照片审核管理员"){
                layer.msg("对不起，您没有内容权限");
            }
            else{
                window.parent.addTab($(this));
            }
        }
        else{
		    window.parent.addTab($(this));
        }
	})

    $("#morenews").on("click",function(){
        if(role=="照片审核管理员"){
            layer.msg("对不起，您没有内容权限");
        }
        else{
            window.parent.addTab($(this));
        }
    })

    var role = window.sessionStorage.getItem("role");

    $.ajax({
        type: "POST",
        dataType: "text",
        url: "/index.php/news/JudgeOperate/json",
        beforeSend: function () {

        },
        complete: function () {

        },
        success: function (result) {
                //动态获取文章总数和待审核文章数量,最新文章
                $.get("../json/newsList.json",
                    function(data){
                        $(".allNews span").text(data.length);  //文章总数
                        //加载最新文章
                        var hotNewsHtml = '';
                        var n = data.length<10?data.length:10;
                        for(var i=0;i<n;i++){
                            hotNewsHtml += '<tr>'
                                +'<td align="left">'+data[i].title+'</td>'
                                +'<td>'+data[i].created_at+'</td>'
                                +'</tr>';
                        }
                        $(".hot_news").html(hotNewsHtml);
                    }
                )
        },
        error:function(data){
            console.log(data.responseText);
        }
    })

    $.ajax({
        type: "POST",
        dataType: "text",
        url: "/index.php/photo/JudgeOperate/json",
        beforeSend: function () {

        },
        complete: function () {

        },
        success: function (result) {
            //图片总数
            $.get("../json/PhotoList.json",
                function(data){
                    $(".imgAll span").text(data.length);
                    var waitPhotos = [];
                    for(var i=0;i<data.length;i++){
                        var photoStr = data[i];
                        if(photoStr["verify"] == "0"){
                            waitPhotos.push(photoStr);
                        }
                    }
                    $(".waitPhotos span").text(waitPhotos.length);  //待审核文章
                }
            )
        },
        error:function(data){
            console.log(data.responseText);
        }
    })

    $.ajax({
        type: "POST",
        dataType: "text",
        url: "/index.php/user/JudgeOperate/json",
        beforeSend: function () {

        },
        complete: function () {

        },
        success: function (result) {
            $.get("../json/UserList.json",
                function(data){
                    $(".userAll span").text(data.length);
                    var newUsers = [];
                    for(var i=0;i<data.length;i++){
                        var userStr = data[i];
                        var today = getNowFormatDate();
                        if(userStr["created_at"] == today){
                            newUsers.push(userStr);
                        }
                    }
                    $(".userNew span").text(newUsers.length);
                }
            )
        },
        error:function(data){
            console.log(data.responseText);
        }
    })

	//新消息
	$.get("../json/message.json",
		function(data){
			$(".newMessage span").text(data.length);
		}
	)


	//数字格式化
	$(".panel span").each(function(){
		$(this).html($(this).text()>9999 ? ($(this).text()/10000).toFixed(2) + "<em>万</em>" : $(this).text());	
	})

    function getNowFormatDate() {
        var date = new Date();
        var seperator1 = "-";
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var strDate = date.getDate();
        if (month >= 1 && month <= 9) {
            month = "0" + month;
        }
        if (strDate >= 0 && strDate <= 9) {
            strDate = "0" + strDate;
        }
        var currentdate = year + seperator1 + month + seperator1 + strDate;
        return currentdate;
    }

})
