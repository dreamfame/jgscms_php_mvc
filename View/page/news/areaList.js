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
	var areaData = '';

	$.get("/index.php/area/JudgeOperate/list", function(data){
		var newArray = [];
        var data = eval('(' + data + ')');
        if(data.state=="0")
        {
            var dataHtml = '<tr><td colspan="9">暂无数据</td></tr>';
            $(".area_content").html(dataHtml);
            $('.area_list thead input[type="checkbox"]').prop("checked",false);
            form.render();
        }
        else{
            var newArray = [];
            areaData = data.content;
            //执行加载数据的方法
            areaList();
        }
	})

    function Search(start,end){
        var userArray = [];
        var index = layer.msg('查询中，请稍候',{icon: 16,time:false,shade:0.8});
        $.ajax({
                url : "../../json/areaList.json",
                type : "get",
                dataType : "json",
                success : function(data){
                    if(window.sessionStorage.getItem("addArea")){
                        var addArea = window.sessionStorage.getItem("addArea");
                        areaData = JSON.parse(addArea).concat(data);
                    }else{
                        areaData = data;
                    }
                    for(var i=0;i<areaData.length;i++){
                        var areaStr = areaData[i];
                        if(start!=""&&end!=""){
                            if(!DateUtil(areaStr.created_at,start,end)){
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
                            if (areaStr.name.indexOf(selectStr) > -1) {
                                areaStr["name"] = changeStr(areaStr.name);
                            }

                            if (areaStr.recommend.indexOf(selectStr) > -1) {
                                areaStr["recommend"] = changeStr(areaStr.recommend);
                            }
                        }
                        if (areaStr.name.indexOf(selectStr) > -1 || areaStr.recommend.indexOf(selectStr) > -1 ) {
                            userArray.push(areaStr);
                        }
                    }
                    areaData = userArray;
                    areaList(areaData);
                }
            })
		layer.close(index);
    }

	//查询
	$(".search_btn").click(function(){
        var newArray = [];
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
                url : "../../json/areaList.json",
                type : "get",
                dataType : "json",
                success : function(data){
                	areaData = data;
                    areaList(areaData);
                }
        	})
		layer.close(index);
    })

	//添加文章
	//改变窗口大小时，重置弹窗的高度，防止超出可视区域（如F12调出debug的操作）
	$(window).one("resize",function(){
		$(".areaAdd_btn").click(function(){
			var index = layui.layer.open({
				title : "添加景点",
				type : 2,
				content : "areaAdd.html",
				success : function(layero, index){
					setTimeout(function(){
						layui.layer.tips('点击此处返回文章列表', '.layui-layer-setwin .layui-layer-close', {
							tips: 3
						});
					},500)
				}
			})			
			layui.layer.full(index);
		})
	}).resize();

	//批量删除
	$(".batchDel").click(function(){
		var $checkbox = $('.area_list tbody input[type="checkbox"][name="checked"]');
		var $checked = $('.area_list tbody input[type="checkbox"][name="checked"]:checked');
		if($checkbox.is(":checked")){
			layer.confirm('确定删除选中的信息？',{icon:3, title:'提示信息'},function(index){
				var index = layer.msg('删除中，请稍候',{icon: 16,time:false,shade:0.8});
	            	//删除数据
                    var delinfo = [];
	            	for(var j=0;j<$checked.length;j++){
	            		for(var i=0;i<areaData.length;i++){
							if(areaData[i].id == $checked.eq(j).parents("tr").find(".area_del").attr("data-id")){
                                delinfo.push(areaData[i].id)
							    areaData.splice(i,1);
								areaList(areaData);
							}
						}
	            	}
                $.ajax({
                    data: {"del_id":delinfo},
                    type: "POST",
                    dataType: "JSON",
                    url: "/index.php/area/JudgeOperate/batchDel",
                    success: function (result) {
                        if(result.state=="1"){
                            $('.area_list thead input[type="checkbox"]').prop("checked",false);
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

	//是否展示
	form.on('switch(isShow)', function(data){
        var index = layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/area/JudgeOperate/show";
        var show = this.checked?1:0;
        var _this = $(this);
        $.ajax({
            data: {"id":_this.attr("data-id"),"show":show},
            type: "POST",
            dataType: "JSON",
            url: url,
            beforeSend: function () {

            },
            complete: function () {

            },
            success: function (result) {
                if(result.state=="1"){
                    layer.close(index);
                    layer.msg("展示状态修改成功！");
                }
            },
            error:function(data){
                console.log(data.responseText);
            }
        })
	})

    //是否置顶
    form.on('switch(isTop)', function(data){
        var index = layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/area/JudgeOperate/top";
        var top = this.checked?1:0;
        var _this = $(this);
        $.ajax({
            data: {"id":_this.attr("data-id"),"top":top},
            type: "POST",
            dataType: "JSON",
            url: url,
            beforeSend: function () {

            },
            complete: function () {

            },
            success: function (result) {
                if(result.state=="1"){
                    layer.close(index);
                    layer.msg("置顶状态修改成功！");
                }
            },
            error:function(data){
                console.log(data.responseText);
            }
        })
    })
 
	//操作
    $(window).one("resize",function(){
        $("body").on("click",".area_edit",function(e){  //编辑
            var no = $(e.currentTarget).data('id');
            var str = JSON.stringify(areaData[no]);
            window.sessionStorage.setItem("edit_area",str);
            var index = layui.layer.open({
                title : "编辑文章",
                type : 2,
                content : "areaEdit.html",
                success : function(layero, index){
                    setTimeout(function(){
                        layui.layer.tips('点击此处返回信息列表', '.layui-layer-setwin .layui-layer-close', {
                            tips: 3
                        });
                    },500)
                }
            })
            layui.layer.full(index);
        })
    }).resize();

    $("body").on("click",".area_del",function(){  //删除
        var _this = $(this);
        layer.confirm('确定删除此信息？',{icon:3, title:'提示信息'},function(index){
            //_this.parents("tr").remove();
            var url = "/index.php/area/JudgeOperate/del";
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
                        for(var i=0;i<areaData.length;i++){
                            if(areaData[i].id == _this.attr("data-id")){
                                areaData.splice(i,1);
                                areaList(areaData);
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

	function areaList(that){
		//渲染数据
		function renderDate(data,curr){
			var dataHtml = '';
			if(!that){
				currData = areaData.concat().splice(curr*nums-nums, nums);
			}else{
				currData = that.concat().splice(curr*nums-nums, nums);
			}
			if(currData.length != 0){
				for(var i=0;i<currData.length;i++){
					var area_map = currData[i].area_map==''?'-':currData[i].area_map;
                    var name = currData[i].name==''?'-':currData[i].name;
                    var see = currData[i].see==''?'-':currData[i].see;
                    var top = currData[i].top==1?"checked":"";
                    var show = currData[i].show==1?"checked":"";
                    var time = currData[i].created_at==''?'-':currData[i].created_at;
                    var recommend = currData[i].recommend==''?'-':currData[i].recommend;
					dataHtml += '<tr>'
			    	+'<td><input type="checkbox" name="checked" lay-skin="primary" lay-filter="choose"></td>'
			    	+'<td align="left">'+name+'</td>'
					+'<td><a href="#" onclick="fileSelect('+currData[i].id+')"><img id="am'+currData[i].id+'" src="'+area_map+'" width="200" height="200" /></a></td>'
                    +'<td>'+see+'</td>'
			    	+'<td><input type="checkbox" name="show" id="aa" lay-skin="switch" data-id="'+data[i].id+'" lay-text="是|否" lay-filter="isShow"'+show+'></td>'
                    +'<td><input type="checkbox" name="top" lay-skin="switch" data-id="'+data[i].id+'" lay-text="是|否" lay-filter="isTop"'+top+'></td>'
			    	+'<td>'+time+'</td>'
                    +'<td>'+recommend+'</td>'
			    	+'<td>'
					+  '<a class="layui-btn layui-btn-mini area_edit" data-id="'+i+'"><i class="iconfont icon-edit"></i> 编辑</a>'
					+  '<a class="layui-btn layui-btn-danger layui-btn-mini area_del" data-id="'+data[i].id+'"><i class="layui-icon">&#xe640;</i> 删除</a>'
			        +'</td>'
			    	+'</tr>';
				}
			}else{
				dataHtml = '<tr><td colspan="9">暂无数据</td></tr>';
			}
		    return dataHtml;
		}

		//分页
		var nums = 10; //每页出现的数据量
		if(that){
			areaData = that;
		}
		laypage({
			cont : "page",
			pages : Math.ceil(areaData.length/nums),
			jump : function(obj){
				$(".area_content").html(renderDate(areaData,obj.curr));
				$('.area_list thead input[type="checkbox"]').prop("checked",false);
		    	form.render();
			}
		})
	}
})
