$(function(){
    var nickname = localStorage.getItem("nickname");
    if(nickname==""){
        $("#nickname").html("请登录");
        $(".userName").html("请登录");
    }
    else{
        $("#nickname").html(nickname);
        $(".userName").html(nickname);
    }

    var head_pic = sessionStorage.getItem("head_pic");
    if(head_pic==""){
        $("#head_pic").html("images/face.jpg");
        $(".head_pic").html("images/face.jpg");
    }
    else{
        $("#head_pic").html(head_pic);
        $(".head_pic").html(head_pic);
    }
})