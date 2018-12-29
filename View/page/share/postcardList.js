layui.config({
	base : "js/"
}).use(['form','layer','jquery','laypage'],function(){
	var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		$ = layui.jquery;

    var start="";
    var end = "";

    laydate.render({
        elem: '#start', //指定元素
        range:true,
        done: function(value, date, endDate){
            if(value!="") {
                start = date.year + "-" + date.month + "-" + date.date;
                end = endDate.year + "-" + endDate.month + "-" + endDate.date;
                Search(start,end);
            }
            else{
                start = "";
                end = "";
                Search(start,end);
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

	//加载页面数据
	var postcardData = '';

	$.get("/index.php/postcard/JudgeOperate/list", function(data){
		var newArray = [];
        var data = eval('(' + data + ')');
        if(data.state=="0")
        {
            var dataHtml = '<tr><td colspan="7">暂无数据</td></tr>';
            $(".postcard_content").html(dataHtml);
            $('.postcard_list thead input[type="checkbox"]').prop("checked",false);
            form.render();
        }
        else{
            var newArray = [];
            postcardData = data.content;
            //执行加载数据的方法
            postcardList();
        }
	})

	function Search(start,end){
        var newArray = [];
        var index = layer.msg('查询中，请稍候',{icon: 16,time:false,shade:0.8});
            $.ajax({
                url : "../../json/postcardList.json",
                type : "get",
                dataType : "json",
                success : function(data){
                    postcardData = data;
                    for(var i=0;i<postcardData.length;i++){
                        var postcardStr = postcardData[i];
                        if(start!=""&&end!=""){
                            if(!DateUtil(postcardStr.date,start,end)){
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
                        if(selectStr!="") {
                            //文章标题
                            if (postcardStr.name.indexOf(selectStr) > -1) {
                                postcardStr["name"] = changeStr(postcardStr.name);
                            }
                            //发布人
                            if (postcardStr.wx.indexOf(selectStr) > -1) {
                                postcardStr["wx"] = changeStr(postcardStr.wx);
                            }
                            //浏览量
                            if (postcardStr.wishes.indexOf(selectStr) > -1) {
                                postcardStr["wishes"] = changeStr(postcardStr.wishes);
                            }
                        }
                        if(postcardStr.name.indexOf(selectStr)>-1 || postcardStr.wx.indexOf(selectStr)>-1 ||  postcardStr.wishes.indexOf(selectStr)>-1 ){
                            newArray.push(postcardStr);
                        }
                    }
                    postcardData = newArray;
                    postcardList(postcardData);
                }
            })
            layer.close(index);
	}

	//查询
	$(".search_btn").click(function(){
		if($(".search_input").val() != ''){
			Search(start,end);
		}else{
			layer.msg("请输入需要查询的内容");
		}
	})

    //显示全部
    $(".showAll_btn").click(function(){
        var index = layer.msg('加载中，请稍候',{icon: 16,time:false,shade:0.8});
        $(".search_input").val("");
        $("#start").val("");
            $.ajax({
                url : "../../json/postcardList.json",
                type : "get",
                dataType : "json",
                success : function(data){
                	postcardData = data;
                    postcardList(postcardData);
                }
            })
            layer.close(index);
    })

	//批量删除
	$(".batchDel").click(function(){
		var $checkbox = $('.postcard_list tbody input[type="checkbox"][name="checked"]');
		var $checked = $('.postcard_list tbody input[type="checkbox"][name="checked"]:checked');
		if($checkbox.is(":checked")){
			layer.confirm('确定删除选中的信息？',{icon:3, title:'提示信息'},function(index){
				var index = layer.msg('删除中，请稍候',{icon: 16,time:false,shade:0.8});
	            	//删除数据
                    var delinfo = [];
	            	for(var j=0;j<$checked.length;j++){
	            		for(var i=0;i<postcardData.length;i++){
							if(postcardData[i].id == $checked.eq(j).parents("tr").find(".postcard_del").attr("data-id")){
								delinfo.push(postcardData[i].id);
							    postcardData.splice(i,1);
								postcardList(postcardData);
							}
						}
	            	}
                $.ajax({
                    data: {"del_id":delinfo},
                    type: "POST",
                    dataType: "JSON",
                    url: "/index.php/postcard/JudgeOperate/batchDel",
                    success: function (result) {
                        if(result.state=="1"){
                            $('.news_list thead input[type="checkbox"]').prop("checked",false);
                            form.render();
                            layer.close(index);
                            layer.msg("删除成功");
                        }
                    },
                    error:function(data){
                        console.log(data.responseText);
                    }
                })
	        })
		}else{
			layer.msg("请选择需要删除的文章");
		}
	})

	//全选
	form.on('checkbox(allChoose)', function(data){
		var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]:not([name="show"]):not([name="top"])');
		child.each(function(index, item){
			item.checked = data.elem.checked;
		});
		form.render('checkbox');
	});

	//通过判断文章是否全部选中来确定全选按钮是否选中
	form.on("checkbox(choose)",function(data){
		var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]:not([name="show"]):not([name="top"])');
		var childChecked = $(data.elem).parents('table').find('tbody input[type="checkbox"]:not([name="show"]):not([name="top"]):checked')
		if(childChecked.length == child.length){
			$(data.elem).parents('table').find('thead input#allChoose').get(0).checked = true;
		}else{
			$(data.elem).parents('table').find('thead input#allChoose').get(0).checked = false;
		}
		form.render('checkbox');
	})

    $("body").on("click",".postcard_del",function(){  //删除
        var _this = $(this);
        layer.confirm('确定删除此信息？',{icon:3, title:'提示信息'},function(index){
            //_this.parents("tr").remove();
            var url = "/index.php/postcard/JudgeOperate/del";
            $.ajax({
                data: {"id":_this.attr("data-id")},
                type: "POST",
                dataType: "JSON",
                url: url,
                beforeSend: function () {

                },
                complete: function () {

                },
                success: function (result) {
                    if(result.state=="1"){
                        for(var i=0;i<postcardData.length;i++){
                            if(postcardData[i].id == _this.attr("data-id")){
                                postcardData.splice(i,1);
                                postcardList(postcardData);
                            }
                        }
                    }
                },
                error:function(data){
                    console.log(data.responseText);
                }
            })
            layer.close(index);
        });
    })

	function postcardList(that){
		//渲染数据
		function renderDate(data,curr){
			var dataHtml = '';
			if(!that){
				currData = postcardData.concat().splice(curr*nums-nums, nums);
			}else{
				currData = that.concat().splice(curr*nums-nums, nums);
			}
			if(currData.length != 0){
				for(var i=0;i<currData.length;i++){
                    var wx = currData[i].wx==''?'-':currData[i].wx;
                    var name = currData[i].name==''?'-':currData[i].name;
                    var pic = currData[i].pic==''?'-':currData[i].pic;
                    var date = currData[i].date==''?'-':currData[i].date;
                    var wishes = currData[i].wishes==''?'-':currData[i].wishes;
					dataHtml += '<tr>'
			    	+'<td><input type="checkbox" name="checked" lay-skin="primary" lay-filter="choose"></td>'
			    	+'<td>'+name+'</td>'
                    +'<td>'+wx+'</td>'
                    +'<td><a href="#" onclick="fileSelect('+currData[i].id+')"><img id="am'+currData[i].id+'" src="'+pic+'" width="200" height="200" /></a></td>'
			    	+ '<td>'+date+'</td>'
			    	+'<td align="left">'+wishes+'</td>'
			    	+'<td>'
					+'<a class="layui-btn layui-btn-danger layui-btn-mini postcard_del" data-id="'+data[i].id+'"><i class="layui-icon">&#xe640;</i> 删除</a>'
			        +'</td>'
			    	+'</tr>';
				}
			}else{
				dataHtml = '<tr><td colspan="7">暂无数据</td></tr>';
			}
		    return dataHtml;
		}

		//分页
		var nums = 10; //每页出现的数据量
		if(that){
			postcardData = that;
		}
		laypage({
			cont : "page",
			pages : Math.ceil(postcardData.length/nums),
			jump : function(obj){
				$(".postcard_content").html(renderDate(postcardData,obj.curr));
				$('.postcard_list thead input[type="checkbox"]').prop("checked",false);
		    	form.render();
			}
		})
	}
})
