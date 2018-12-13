$(function(){
    var nickname = localStorage.getItem("nickname");
    $("#nickname").html(nickname);
    $(".userName").html(nickname);
})