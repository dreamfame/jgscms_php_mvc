var $;
layui.config({
	base : "../../js/"
}).use(['form','layer','layedit'],function(){
    var form = layui.form(),
        layer = parent.layer === undefined ? layui.layer : parent.layer,
        layedit = layui.layedit;
        $ = layui.jquery;

    //消息回复
    var editIndex = layedit.build('msgReply',{
         tool: ['face'],
         height:100
    });
        
    form.on('select(selectMsg)',function(data){
        var len = $(".msgHtml tr").length;
        if(len==0){
            $(".msgHtml").append("<tr class='no_msg' align='center'><td colspan='4'>暂无消息</td></tr>");
        }
        else {
            var n = 0;
            for (var i = 0; i < len; i++) {
                if (data.value == "-1") {
                    n++;
                    $(".msgHtml tr").eq(i).show();
                    $(".msgHtml tr.no_msg").remove();
                } else if (data.value == "1") {
                    if ($(".msgHtml tr").eq(i).find("td.msg_reply").text() == "已回复") {
                        $(".msgHtml tr").eq(i).show();
                        n++;
                    } else {
                        $(".msgHtml tr").eq(i).hide();
                    }
                }
                else {
                    if ($(".msgHtml tr").eq(i).find("td.msg_reply").text() == "已回复") {
                        $(".msgHtml tr").eq(i).hide();
                    } else {
                        n++;
                        $(".msgHtml tr").eq(i).show();
                    }
                }
            }
            if(n==0){
                $(".msgHtml").append("<tr class='no_msg' align='center'><td colspan='4'>暂无消息</td></tr>")
            }
        }
        /*if(data.value=="1" && $(".msgHtml tr").find(".msg_collect i.icon-star").length=="0"){
            $(".msgHtml").append("<tr class='no_msg' align='center'><td colspan='4'>暂无收藏消息</td></tr>")
        }*/
    })

    var userData;

    //加载数据
    $.get("/index.php/message/JudgeOperate/list",function(data){
        var data = eval('(' + data + ')');
        if(data.state=="1"){
            userData = data.content;
            var content = data.content;
            var msgHtml = '',msgReply;
            for(var i=0; i<content.length; i++){
                if(content[i].status=="1"){
                    msgReply = "已回复";
                }else{
                    msgReply = "";
                }
                msgHtml += '<tr>';
                msgHtml += '  <td class="msg_info">';
                msgHtml += '    <img src="'+content[i].user_head+'" width="50" height="50"><input type="hidden" value="'+content[i].id+'">';
                msgHtml += '    <div class="user_info">';
                msgHtml += '        <h2>'+content[i].user_nickname+'</h2>';
                msgHtml += '        <p>'+content[i].msg+'</p>';
                msgHtml += '    </div>';
                msgHtml += '  </td>';
                msgHtml += '  <td class="msg_time">'+content[i].msg_time+'</td>';
                msgHtml += '  <td class="msg_reply">'+msgReply+'</td>';
                msgHtml += '  <td class="msg_opr">';
                msgHtml += '    <a class="layui-btn layui-btn-mini reply_msg"><i class="layui-icon">&#xe611;</i> 回复</a>';
                msgHtml += '  </td>';
                msgHtml += '</tr>';
            }
            $(".msgHtml").html(msgHtml);
        }
       else{
            $(".msgHtml").append("<tr class='no_msg' align='center'><td colspan='4'>暂无消息</td></tr>")
        }
    })

    //操作
    $("body").on("click",".msg_collect",function(){  //收藏
        if($(this).text().indexOf("已收藏") > 0){
            layer.msg("取消收藏成功！");
            $(this).html("<i class='layui-icon'>&#xe600;</i> 收藏");
        }else{
            layer.msg("收藏成功！");
            $(this).html("<i class='iconfont icon-star'></i> 已收藏");
        }
    })

    //回复
    $("body").on("click",".reply_msg,.msgHtml .user_info h2,.msgHtml .msg_info>img",function(){
        var id = $(this).parents("tr").find("input[type=hidden]").val();
        window.sessionStorage.setItem("msg_id",id);
        var userName = $(this).parents("tr").find(".user_info h2").text();
        var status = "";
        var user;
        for (var i = 0; i < userData.length; i++)
        {
            if (userData[i].id == id) {
                status = userData[i].status;
                user = userData[i];
            }
        }
        window.sessionStorage.setItem("status",status);
        var index = layui.layer.open({
            title : "与 "+userName+" 的聊天",
            type : 2,
            content : "messageReply.html",
            success : function(layero, index){
                setTimeout(function(){
                    layui.layer.tips('点击此处返回消息列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                },500)
                var body = layui.layer.getChildFrame('body', index);
                //加载回复信息
                $.get("/index.php/message/JudgeOperate/msg/",{"id":id,"status":status},function(data){
                    var data = eval('(' + data + ')');
                    if(data.state=="1") {
                        var reply = data.content[0];
                        var msgReplyHtml = '', msgReply;
                        if (user.status=="1") {
                            msgReply = "已回复";
                        } else {
                            msgReply = "";
                        }
                        msgReplyHtml += '<tr>';
                        msgReplyHtml += '  <td class="msg_info">';
                        msgReplyHtml += '    <img src="' + reply.admin_head + '" width="50" height="50">';
                        msgReplyHtml += '    <div class="user_info">';
                        msgReplyHtml += '        <h2>' + reply.admin_username + '</h2>';
                        msgReplyHtml += '        <p>' + reply.reply + '</p>';
                        msgReplyHtml += '    </div>';
                        msgReplyHtml += '  </td>';
                        msgReplyHtml += '  <td class="msg_time">' + reply.reply_time + '</td>';
                        msgReplyHtml += '  <td class="msg_reply"></td>';
                        msgReplyHtml += '</tr>';
                        msgReplyHtml += '<tr>';
                        msgReplyHtml += '  <td class="msg_info">';
                        msgReplyHtml += '    <img src="' + user.user_head + '" width="50" height="50">';
                        msgReplyHtml += '    <div class="user_info">';
                        msgReplyHtml += '        <h2>' + user.user_nickname + '</h2>';
                        msgReplyHtml += '        <p>' + user.msg + '</p>';
                        msgReplyHtml += '    </div>';
                        msgReplyHtml += '  </td>';
                        msgReplyHtml += '  <td class="msg_time">' + user.msg_time + '</td>';
                        msgReplyHtml += '  <td id="msg_info" class="msg_reply">' + msgReply + '</td>';
                        msgReplyHtml += '</tr>';
                    }
                    else{
                        var msgReplyHtml = '', msgReply;
                        if (user.status=="1") {
                            msgReply = "已回复";
                        } else {
                            msgReply = "";
                        }
                        msgReplyHtml += '<tr>';
                        msgReplyHtml += '  <td class="msg_info">';
                        msgReplyHtml += '    <img src="' + user.user_head + '" width="50" height="50">';
                        msgReplyHtml += '    <div class="user_info">';
                        msgReplyHtml += '        <h2>' + user.user_nickname + '</h2>';
                        msgReplyHtml += '        <p>' + user.msg + '</p>';
                        msgReplyHtml += '    </div>';
                        msgReplyHtml += '  </td>';
                        msgReplyHtml += '  <td class="msg_time">' + user.msg_time + '</td>';
                        msgReplyHtml += '  <td id="msg_info" class="msg_reply">' + msgReply + '</td>';
                        msgReplyHtml += '</tr>';
                    }
                    body.find(".msgReplyHtml").html(msgReplyHtml);
                })
            },
            cancel: function(){
                window.location.reload();
            }
        })
        //改变窗口大小时，重置弹窗的高度，防止超出可视区域（如F12调出debug的操作）
        $(window).resize(function(){
            layui.layer.full(index);
        })
        layui.layer.full(index);
    })

    //提交回复
    var message = [];
    $(".send_msg").click(function(){
        var status = window.sessionStorage.getItem("status");
        if(layedit.getContent(editIndex) != '') {
            if(status=="0")
            {
                var content = layedit.getContent(editIndex);
                var username = window.sessionStorage.getItem("username");
                var id = window.sessionStorage.getItem("msg_id");
                $.ajax({
                    data:{"reply":content,"id":id,"username":username},
                    url: "/index.php/message/JudgeOperate/reply",
                    type: "post",
                    dataType: "json",
                    success: function (data) {
                        if(data.state=="1"){
                            layer.msg("回复成功");
                            var replyData = data.content[0];
                            var replyHtml = '', msgStr;
                            replyHtml += '<tr>';
                            replyHtml += '  <td class="msg_info">';
                            replyHtml += '    <img src="'+replyData.admin_head+'" width="50" height="50">';
                            replyHtml += '    <div class="user_info">';
                            replyHtml += '        <h2>' + replyData.admin_username + '</h2>';
                            replyHtml += '        <p>' + replyData.reply + '</p>';
                            replyHtml += '    </div>';
                            replyHtml += '  </td>';
                            replyHtml += '  <td class="msg_time">' + replyData.reply_time + '</td>';
                            replyHtml += '  <td class="msg_reply"></td>';
                            replyHtml += '</tr>';
                            $(".msgReplyHtml").prepend(replyHtml);
                            $("#LAY_layedit_1").contents().find("body").html('');
                            $("#msg_info").text("已回复");
                            window.sessionStorage.setItem("status","1");
                        }
                        else{
                            layer.msg("回复失败");
                        }
                    },
                    error:function(data){
                        console.log(data.responseText);
                        layer.msg(data.responseText);
                    }
                })
            }
            else{
                layer.msg("此消息已回复");
            }
        }
        else{
            layer.msg("请输入回复信息");
        }
        return false;
    })
})

