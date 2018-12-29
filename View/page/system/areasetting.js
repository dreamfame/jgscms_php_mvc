layui.config({
    base : "js/"
}).use(['form', 'layer'], function(){
    var layer = layui.layer
        ,$ = layui.jquery;
    var form = layui.form();

    var index = layer.msg('数据加载中，请稍候',{icon: 16,time:false,shade:0.5});
    $.ajax({
        url: "../../json/area.json",
        type: "get",
        dataType: "json",
        success: function (data) {
            window.sessionStorage.setItem("area", JSON.stringify(data));
            layer.close(index);
        },
        error:function(data){
            window.sessionStorage.setItem("area", "");
            layer.close(index);
        }
    })

    form.on("submit(area)",function(data){
        var point1 = $("#point1").val();
        var point2 = $("#point2").val();
        var point3 = $("#point3").val();
        var point4 = $("#point4").val();
        var area = '{"point1":"'+point1+'",';
        area += '"point2":"'+point2+'",';
        area += '"point3":"'+point3+'",';
        area += '"point4":"'+point4+'"}';
        if((point1==""&&point2==""&&point3==""&&point4=="")||(point1!=""&&point2!=""&&point3!=""&&point4!="")){
            $.ajax({
                data:JSON.parse(area),
                url : "/index.php/system/JudgeOperate/area",
                type : "post",
                dataType : "text",
                success : function(data){
                    layer.msg("区域设置成功！");
                    window.sessionStorage.setItem("area", $("#content").val());
                }
            })
        }
        else{
            layer.msg("区域必须设置4个点或不设置！");
        }
        return false;
    });
});