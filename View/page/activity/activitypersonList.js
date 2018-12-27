layui.config({
    base : "js/"
}).use(['form','layer','jquery','laypage'],function(){
    var form = layui.form(),
        layer = parent.layer === undefined ? layui.layer : parent.layer,
        laypage = layui.laypage,
        $ = layui.jquery;

    //加载页面数据
    var activitypersonData = '';

    var start="";
    var end = "";

    laydate.render({
        elem: '#start', //指定元素
        range:true,
        done: function(value, date, endDate){
            var prize = $('input:radio[name="prize"]:checked').val();
            if(value!="") {
                start = date.year + "-" + date.month + "-" + date.date;
                end = endDate.year + "-" + endDate.month + "-" + endDate.date;
                Search(prize,start,end);
            }
            else{
                start = "";
                end = "";
                Search(prize,start,end);
            }
            //Search("-1");
        }
    });

    function DateUtil(date0,date1,date2){
        var oDate0 = new Date(date0);
        var oDate1 = new Date(date1);
        var oDate2 = new Date(date2);
        if(oDate0.getTime() >= oDate1.getTime()&&oDate0.getTime() <=oDate2.getTime()){
            return true;
        } else {
            return false;
        }
    }

    //中奖状态查询
    form.on('radio(prize)', function(data){
        Search(data.value,start,end);
    });

    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null) return unescape(r[2]); return null; //返回参数值
    }

    var activity_id = getUrlParam("id");

    $.get("/index.php/activityPerson/JudgeOperate/list/?activity_id="+activity_id, function(data){
        var newArray = [];
        var data = eval('(' + data + ')');
        if(data.state=="0")
        {
            var dataHtml = '<tr><td colspan="5">暂无数据</td></tr>';
            $(".activityperson_content").html(dataHtml);
            $('.activityperson_list thead input[type="checkbox"]').prop("checked",false);
            form.render();
        }
        else{
            var newArray = [];
            activitypersonData = data.content;
            //执行加载数据的方法
            activitypersonList();
        }
    })

    function Search(prize,start,end){
        var newArray = [];
        var index = layer.msg('查询中，请稍候',{icon: 16,time:false,shade:0.8});
        $.ajax({
            url : "../../json/activitypersonList.json",
            type : "get",
            dataType : "json",
            success : function(data){
                activitypersonData = data;
                for(var i=0;i<activitypersonData.length;i++){
                    var activitypersonStr = activitypersonData[i];
                    if(prize!="-1"){
                        if(activitypersonStr.prize!=prize){
                            continue;
                        }
                    }
                    if(start!=""&&end!=""){
                        if(!DateUtil(activitypersonStr.time,start,end)){
                            continue;
                        }
                    }
                    var selectStr = $(".search_input").val();
                    function changeStr(data){
                        var dataStr = '';
                        var showNum = data.split(eval("/"+selectStr+"/ig")).length - 1;
                        if(showNum > 1){
                            for (var j=0;j<showNum;j++) {
                                dataStr += data.split(eval("/"+selectStr+"/ig"))[j] + "<i style='color:#03c339;font-weight:bold;'>" + selectStr + "</i>";
                            }
                            dataStr += data.split(eval("/"+selectStr+"/ig"))[showNum];
                            return dataStr;
                        }else{
                            dataStr = data.split(eval("/"+selectStr+"/ig"))[0] + "<i style='color:#03c339;font-weight:bold;'>" + selectStr + "</i>" + data.split(eval("/"+selectStr+"/ig"))[1];
                            return dataStr;
                        }
                    }
                    //景点
                    if(selectStr!="") {
                        if (activitypersonStr.nickname.indexOf(selectStr) > -1) {
                            activitypersonStr["nickname"] = changeStr(activitypersonStr.nickname);
                        }
                        //推荐星级
                        if (activitypersonStr.phone.indexOf(selectStr) > -1) {
                            activitypersonStr["phone"] = changeStr(activitypersonStr.phone);
                        }
                    }
                    if(activitypersonStr.nickname.indexOf(selectStr)>-1 || activitypersonStr.phone.indexOf(selectStr)>-1 ){
                        newArray.push(activitypersonStr);
                    }
                }
                activitypersonData = newArray;
                activitypersonList(activitypersonData);
            }
        })

        layer.close(index);
    }

    //查询
    $(".search_btn").click(function(){
        if($(".search_input").val() != ''){
            var prize = $('input:radio[name="prize"]:checked').val();
            Search(prize,start,end);
        }else{
            layer.msg("请输入需要查询的内容");
        }
    })

    //显示全部
    $(".showAll_btn").click(function(){
        $(".search_input").val("");
        $("input[name='prize']").get(0).checked=true;
        $("#start").val("");
        start = "";
        end = "";
        var index = layer.msg('加载中，请稍候',{icon: 16,time:false,shade:0.8});
        $.ajax({
            url : "../../json/activitypersonList.json",
            type : "get",
            dataType : "json",
            success : function(data){
                activitypersonData = data;
                activitypersonList(activitypersonData);
            }
        })
        layer.close(index);
    })

    //是否中奖
    form.on('switch(isPrize)', function(data){
        var index = top.layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/activityPerson/JudgeOperate/prize";
        var prize = this.checked?1:0;
        var _this = $(this);
        $.ajax({
            data: {"id":_this.attr("data-id"),"activity_id":activity_id,"prize":prize},
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
                    top.layer.msg("中奖状态修改成功！");
                }
            },
            error:function(data){
                console.log(data.responseText);
            }
        })
    })


    function activitypersonList(that){
        //渲染数据
        function renderDate(data,curr){
            var dataHtml = '';
            if(!that){
                currData = activitypersonData.concat().splice(curr*nums-nums, nums);
            }else{
                currData = that.concat().splice(curr*nums-nums, nums);
            }
            if(currData.length != 0){
                for(var i=0;i<currData.length;i++){
                    var phone = currData[i].phone==''?'-':currData[i].phone;
                    var nickname = currData[i].nickname==''?'-':currData[i].nickname;
                    var time = currData[i].date==''?'-':currData[i].time;
                    var prize = currData[i].prize==1?"checked":"";
                    dataHtml += '<tr>'
                        +'<td><input type="checkbox" name="checked" lay-skin="primary" lay-filter="choose"></td>'
                        +'<td>'+nickname+'</td>'
                        +'<td>'+phone+'</td>'
                        +'<td>'+time+'</td>'
                        +'<td><input type="checkbox" name="prize" lay-skin="switch" data-id="'+data[i].id+'" lay-text="中奖|未中" lay-filter="isPrize"'+prize+'></td>'
                        +'</tr>';
                }
            }else{
                dataHtml = '<tr><td colspan="5">暂无数据</td></tr>';
            }
            return dataHtml;
        }

        //分页
        var nums = 10; //每页出现的数据量
        if(that){
            activitypersonData = that;
        }
        laypage({
            cont : "page",
            pages : Math.ceil(activitypersonData.length/nums),
            jump : function(obj){
                $(".activityperson_content").html(renderDate(activitypersonData,obj.curr));
                $('.activityperson_list thead input[type="checkbox"]').prop("checked",false);
                form.render();
            }
        })
    }
})
