layui.config({
    base : "js/"
}).use(['form','layer','jquery','layedit','upload'],function(){
    var form = layui.form(),
        layer = parent.layer === undefined ? layui.layer : parent.layer,
        layedit = layui.layedit,
        $ = layui.jquery;

    layedit.set({
        uploadImage: {
            url: '/index.php/picture/JudgeOperate/uploadImg', //接口url
            type: 'post' //默认post
        }
    });


    //创建一个编辑器
    var editIndex = layedit.build('bug_intro');
    var sendBug;
    form.on("submit(sendBug)",function(data){
        var content = layedit.getContent(editIndex);
        sendBug = '{"title":"'+data.field.tname+'",';  //文章名称
        content = content.replace(/\"/g,"'");
        sendBug += '"content":"'+ content +'"}'; //内容
        //弹出loading
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/system/JudgeOperate/bug";
        $.ajax({
            data: JSON.parse(sendBug),
            type: "POST",
            dataType: "JSON",
            url: url,
            beforeSend: function () {

            },
            complete: function () {

            },
            success: function (result) {
                if(result.state=="1"){
                    top.layer.close(index);
                    top.layer.msg("发送成功！问题解决中，请耐心等待。");
                    layer.closeAll("iframe");
                    layedit.setContent("");
                    $(".tname").val("");
                }
                else{
                    top.layer.close(index);
                    top.layer.msg(result.content);
                    layer.closeAll("iframe");
                }
            },
            error:function(data){
                console.log(data.responseText);
                top.layer.close(index);
                top.layer.msg("发送失败!"+data.responseText);
                layer.closeAll("iframe");
            }
        })
        return false;
    })

})
