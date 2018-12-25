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


    var id = getUrlParam("id");
    var imagesData;

    //流加载图片
    var imgNums = 9;  //单页显示图片数量
    flow.load({
        elem: '#Images', //流加载容器
        done: function(page, next){ //加载下一页
            $.get("../../json/PhotoList.json",function(data){
                //模拟插入
                var imgList = [];
                var picList = [];
                for(var i=0;i<data.length;i++){
                    if(data[i].id==id){
                        if(data[i].img1!=""||data[i].img1!=null){
                            imgList.push(data[i].img1);
                        }
                        if(data[i].img2!=""||data[i].img2!=null){
                            imgList.push(data[i].img2);
                        }
                        if(data[i].img3!=""||data[i].img3!=null){
                            imgList.push(data[i].img3);
                        }
                        if(data[i].img4!=""||data[i].img4!=null){
                            imgList.push(data[i].img4);
                        }
                        if(data[i].img5!=""||data[i].img5!=null){
                            imgList.push(data[i].img5);
                        }
                        if(data[i].img6!=""||data[i].img6!=null){
                            imgList.push(data[i].img6);
                        }
                        if(data[i].img7!=""||data[i].img7!=null){
                            imgList.push(data[i].img7);
                        }
                        if(data[i].img8!=""||data[i].img8!=null){
                            imgList.push(data[i].img8);
                        }
                        if(data[i].img9!=""||data[i].img9!=null){
                            imgList.push(data[i].img9);
                        }
                    }
                }
                if(imgList.length==0){
                    next(imgList.join(''), page < (1 / imgNums));
                    form.render();
                }
                else {
                    imagesData = imgList;
                    var images = imgList;
                    var maxPage = imgNums*page < images.length ? imgNums*page : images.length;
                        for (var j = imgNums * (page - 1); j < maxPage; j++) {
                            picList.push('<li><img src="' + images[j] + '"><div class="operate"></li>')
                        }
                        next(picList.join(''), page < (images.length / imgNums));
                        form.render();
                }
            });
        }
    });

    //全选
    form.on('checkbox(selectAll)', function(data){
        var child = $("#Images li input[type='checkbox']");
        child.each(function(index, item){
            item.checked = data.elem.checked;
        });
        form.render('checkbox');
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
})