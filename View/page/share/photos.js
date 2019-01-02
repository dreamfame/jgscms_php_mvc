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
                        if(data[i].img1!=""&&data[i].img1!=null){
                            imgList.push(data[i].img1);
                        }
                        if(data[i].img2!=""&&data[i].img2!=null){
                            imgList.push(data[i].img2);
                        }
                        if(data[i].img3!=""&&data[i].img3!=null){
                            imgList.push(data[i].img3);
                        }
                        if(data[i].img4!=""&&data[i].img4!=null){
                            imgList.push(data[i].img4);
                        }
                        if(data[i].img5!=""&&data[i].img5!=null){
                            imgList.push(data[i].img5);
                        }
                        if(data[i].img6!=""&&data[i].img6!=null){
                            imgList.push(data[i].img6);
                        }
                        if(data[i].img7!=""&&data[i].img7!=null){
                            imgList.push(data[i].img7);
                        }
                        if(data[i].img8!=""&&data[i].img8!=null){
                            imgList.push(data[i].img8);
                        }
                        if(data[i].img9!=""&&data[i].img9!=null){
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
                            var n = j+1;
                            picList.push('<li><a class="photo_pic"><img style="height:215px;cursor:pointer;" src="' + images[j] + '"></a><div class="operate"><div class="check">图片'+n+'</div></li>')
                        }
                        next(picList.join(''), page < (images.length / imgNums));
                        form.render();
                }
            });
        }
    });

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