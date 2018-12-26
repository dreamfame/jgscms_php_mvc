layui.config({
	base : "../../js/"
}).use(['flow','form','layer'],function(){
    var flow = layui.flow,
        form = layui.form(),
        layer = parent.layer === undefined ? layui.layer : parent.layer,
        $ = layui.jquery;

    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null) return unescape(r[2]); return null; //返回参数值
    }

    var scenic_id = getUrlParam("id");
    var imagesData;

    //流加载图片
    var imgNums = 15;  //单页显示图片数量
    flow.load({
        elem: '#Images', //流加载容器
        done: function(page, next){ //加载下一页
            $.get("/index.php/img/JudgeOperate/pic/?scenic_id="+scenic_id,function(data){
                //模拟插入
                var imgList = [];
                var data = eval('(' + data + ')');
                if(data.state=="0"){
                    next(imgList.join(''), page < (1 / imgNums));
                    form.render();
                }
                else {
                    imagesData = data.content;
                    var images = data.content;
                    var maxPage = imgNums*page < images.length ? imgNums*page : images.length;
                    for (var i = imgNums * (page - 1); i < maxPage; i++) {
                        imgList.push('<li><img src="' + images[i].src + '"><div class="operate"><div class="check"><input type="checkbox" name="belle" lay-filter="choose" lay-skin="primary" title="' + images[i].name + '"></div><i data-id="'+images[i].id+'" class="layui-icon img_del">&#xe640;</i></div></li>')
                    }
                    next(imgList.join(''), page < (images.length / imgNums));
                    form.render();
                }
            }); 
        }
    });

    //删除单张图片
    $("body").on("click",".img_del",function(){
        var _this = $(this);
        layer.confirm('确定删除图片"'+_this.siblings().find("input").attr("title")+'"吗？',{icon:3, title:'提示信息'},function(index){
            $.ajax({
                data: {"id":_this.attr("data-id")},
                type: "POST",
                dataType: "JSON",
                url: "/index.php/img/JudgeOperate/del",
                success: function (result) {
                    if(result.state=="1"){
                        _this.parents("li").hide(400);
                        _this.parents("li").remove();;
                    }
                },
                error:function(data){
                    console.log(data.responseText);
                }
            })
            layer.close(index);
        });
    })

    //全选
    form.on('checkbox(selectAll)', function(data){
        var child = $("#Images li input[type='checkbox']");
        child.each(function(index, item){
            item.checked = data.elem.checked;
        });
        form.render('checkbox');
    });

    var loading;
    form.on("submit(uploadPic)",function(data){
       loading = top.layer.msg('图片上传中，请稍候',{icon: 16,time:false,shade:0.8});
       $("#uploadForm").submit();
       return false;
    });

    form.on("submit(closeMark)",function(result){
        top.layer.close(loading);
        console.log(result);
        layer.msg("共上传图片"+result.field.total+"张，成功"+result.field.success+"张，失败"+result.field.fail+"张");
        return false;
    });

    //通过判断是否全部选中来确定全选按钮是否选中
    form.on("checkbox(choose)",function(data){
        var child = $(data.elem).parents('#Images').find('li input[type="checkbox"]');
        var childChecked = $(data.elem).parents('#Images').find('li input[type="checkbox"]:checked');
        if(childChecked.length == child.length){
            $(data.elem).parents('#Images').siblings("blockquote").find('input#selectAll').get(0).checked = true;
        }else{
            $(data.elem).parents('#Images').siblings("blockquote").find('input#selectAll').get(0).checked = false;
        }
        form.render('checkbox');
    })

    $(".uploadPic").click(function(){
        $(".layui-upload-file").click();
        $("#scenic_id").val(scenic_id);
    });

    //批量删除
    $(".batchDel").click(function(){
        var $checkbox = $('#Images li input[type="checkbox"]');
        var $checked = $('#Images li input[type="checkbox"]:checked');
        if($checkbox.is(":checked")){
            layer.confirm('确定删除选中的图片？',{icon:3, title:'提示信息'},function(index){
                var index = layer.msg('删除中，请稍候',{icon: 16,time:false,shade:0.8});
                setTimeout(function(){
                    //删除数据
                    $checked.each(function(){
                        $(this).parents("li").hide(1000);
                        setTimeout(function(){$(this).parents("li").remove();},950);
                    })
                    $('#Images li input[type="checkbox"]').prop("checked",false);
                    form.render();
                    layer.close(index);
                    layer.msg("删除成功");
                },2000);
            })
        }else{
            layer.msg("请选择需要删除的图片");
        }
    })
})