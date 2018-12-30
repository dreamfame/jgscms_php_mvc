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
                        imgList.push('<li><a class="photo_pic"><img style="height:215px;cursor:pointer;" src="' + images[i].src + '"></a><div class="operate"><div class="check"><input type="checkbox" name="belle" lay-filter="choose" lay-skin="primary" title="' + images[i].name + '"></div><i data-id="'+images[i].id+'" class="layui-icon img_del">&#xe640;</i></div></li>')
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
                var index = top.layer.msg('删除中，请稍候',{icon: 16,time:false,shade:0.8});
                var delinfo = [];
                for(var j=0;j<$checked.length;j++){
                    for(var i=0;i<imagesData.length;i++){
                        if(imagesData[i].id == $checked.eq(j).parents("div.operate").find(".img_del").attr("data-id")){
                            delinfo.push(imagesData[i].id);
                            imagesData.splice(i,1);
                        }
                    }
                }
                $.ajax({
                    data: {"del_id":delinfo},
                    type: "POST",
                    dataType: "JSON",
                    url: "/index.php/img/JudgeOperate/batchDel",
                    success: function (result) {
                        if(result.state=="1"){
                            $checked.each(function(){
                                $(this).parents("li").hide(400);
                                $(this).parents("li").remove();
                            })
                            $('#Images li input[type="checkbox"]').prop("checked",false);
                            form.render();
                            top.layer.close(index);
                            layer.msg("删除成功");
                        }
                    },
                    error:function(data){
                        console.log(data.responseText);
                    }
                })
            })
        }else{
            layer.msg("请选择需要删除的图片");
        }
    })

    $("body").on("click",".photo_pic",function(){
        var _this = $(this);
        var url = _this.find("img").attr("src");
        if(!url || url==""){
            layer.msg("没有发现图片！");
            return ;
        }
        $("<img/>").attr("src", url).load(function(){
            var height = this.height;
            var width = this.width;
            var max_height = $(window).height() - 100;
            var max_width = $(window).width();
            var rate1 = max_height/height;
            var rate2 = max_width/width;
            var rate3 = 1;
            var rate = Math.min(rate1,rate2,rate3);
            //等比例缩放
            var imgHeight = height * rate; //获取图片高度
            var imgWidth = width  * rate; //获取图片宽度
            var imgHtml = "<img src='" + url + "' width='"+imgWidth+"px' height='"+imgHeight+"px'/>";
            //弹出层
            top.layer.open({
                type: 1,
                shade: 0.8,
                offset: 'auto',
                area: [imgWidth + 'px',imgHeight +'px'], ////宽，高
                shadeClose:true,
                scrollbar: false,
                title: false, //不显示标题
                content: imgHtml, //捕获的元素，注意：最好该指定的元素要存放在body最外层，否则可能被其它的相对元素所影响
                cancel: function () {
                    //layer.msg('捕获就是从页面已经存在的元素上，包裹layer的结构', { time: 5000, icon: 6 });
                }
            });
        });
    });
})