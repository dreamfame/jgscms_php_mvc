layui.config({
    base : "js/"
}).use(['form','tree', 'layer'], function(){
    var layer = layui.layer
        ,$ = layui.jquery;
    var form = layui.form();

    var area_result = [];

    var trees = [];
    var index = layer.msg('数据加载中，请稍候',{icon: 16,time:false,shade:0.5});
    $.ajax({
        url : "/index.php/area/JudgeOperate/id_name",
        type : "get",
        dataType : "json",
        success : function(data){
            if(data.state=="1") {
                var area_data = data.content;
                $.ajax({
                    url: "/index.php/scenic/JudgeOperate/id_name",
                    type: "get",
                    dataType: "json",
                    success: function (result) {
                        if(result.state=="1"){
                            var scenic_data = result.content;
                            for(var i=0;i<area_data.length;i++){
                                node = new Object();
                                node.name = area_data[i].name;
                                node.id = area_data[i].id;
                                node.spread = true;
                                node.alias = "area"+node.id;
                                var childlist = [];
                                for(var j=0;j<scenic_data.length;j++){
                                    if(area_data[i].id==scenic_data[j].area_id){
                                        child = new Object();
                                        child.name = scenic_data[j].name;
                                        child.id = scenic_data[j].id;
                                        child.alias = "scenic"+child.id;
                                        childlist.push(child);
                                    }
                                }
                                node.children = childlist;
                                trees.push(node);
                            }
                            layui.tree({
                                elem: '#area_tree' //指定元素
                                ,target: '_blank' //是否新选项卡打开（比如节点返回href才有效）
                                ,click: function(item){ //点击节点回调
                                    var name = item.name;
                                    if(array_contain(area_result,name)){
                                        return;
                                    }
                                    if($('#content').val()==""){
                                        $('#content').val($('#content').val()+name);
                                    }
                                    else{
                                        $('#content').val($('#content').val()+","+name);
                                    }
                                    area_result.push(name);
                                }
                                ,nodes: trees
                            });
                            if(window.sessionStorage.getItem("area")!=null){
                                fillData(window.sessionStorage.getItem("area"));
                            }
                            else {
                                $.ajax({
                                    url: "../../json/area.json",
                                    type: "get",
                                    dataType: "json",
                                    success: function (data) {
                                        fillData(data.area);
                                        window.sessionStorage.setItem("area", data.area);
                                    }
                                })
                            }
                            layer.close(index);
                        }
                    }
                })
            }
        }
    })

    $("#content").change(function(){
        var a = $("#content").val();
        area_result = [];
        if(a.indexOf(",")>-1){
            area_result = a.split(",");
        }
        else{
            area_result.push(a);
        }
    });

    form.on("submit(area)",function(data){
        var area = '{"area":"'+$("#content").val()+'"}'; //网站备案号
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
        return false;
    });

    function fillData(data){
        function nullData(data){
            if(data == '' || data == "undefined"){
                return "未定义";
            }else{
                return data;
            }
        }
        if(data.indexOf(",")>-1){
            area_result = data.split(",");
        }
        else{
            area_result.push(data);
        }
        $("#content").val(nullData(data));      //网站名称
    }

    function array_contain(array, obj){
        for (var i = 0; i < array.length; i++){
            if (array[i] == obj)
                return true;
        }
        return false;
    }
});