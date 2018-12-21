layui.config({
	base : "js/"
}).use(['form','layer','jquery','laypage'],function(){
	var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		$ = layui.jquery;

	//加载页面数据
	var activityData = '';

	$.get("/index.php/activity/JudgeOperate/list", function(data){
		var newArray = [];
        var data = eval('(' + data + ')');
        if(data.state=="0")
        {
            var dataHtml = '<tr><td colspan="9">暂无数据</td></tr>';
            $(".activity_content").html(dataHtml);
            $('.activity_list thead input[type="checkbox"]').prop("checked",false);
            form.render();
        }
        else{
            var newArray = [];
            activityData = data.content;
            //执行加载数据的方法
            activityList();
        }
	})

	//查询
	$(".search_btn").click(function(){
		var newArray = [];
		if($(".search_input").val() != ''){
			var index = layer.msg('查询中，请稍候',{icon: 16,time:false,shade:0.8});
            setTimeout(function(){
            	$.ajax({
					url : "../../json/activityList.json",
					type : "get",
					dataType : "json",
					success : function(data){
						activityData = data;
						for(var i=0;i<activityData.length;i++){
							var activityStr = activityData[i];
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
		            		if(activityStr.name.indexOf(selectStr) > -1){
			            		activityStr["name"] = changeStr(activityStr.name);
		            		}
		            		//推荐星级
		            		if(activityStr.recommend.indexOf(selectStr) > -1){
			            		activityStr["recommend"] = changeStr(activityStr.recommend);
		            		}
		            		//浏览量
		            		if(activityStr.see.indexOf(selectStr) > -1){
			            		activityStr["see"] = changeStr(activityStr.see);
		            		}
		            		//发布时间
		            		if(activityStr.created_at.indexOf(selectStr) > -1){
			            		activityStr["created_at"] = changeStr(activityStr.created_at);
		            		}
		            		if(activityStr.name.indexOf(selectStr)>-1 || activityStr.recommend.indexOf(selectStr)>-1 ||  activityStr.see.indexOf(selectStr)>-1 || activityStr.created_at.indexOf(selectStr)>-1){
		            			newArray.push(activityStr);
		            		}
		            	}
		            	activityData = newArray;
		            	activityList(activityData);
					}
				})
            	
                layer.close(index);
            },2000);
		}else{
			layer.msg("请输入需要查询的内容");
		}
	})

    //显示全部
    $(".showAll_btn").click(function(){
        var index = layer.msg('加载中，请稍候',{icon: 16,time:false,shade:0.8});
        setTimeout(function(){
            $.ajax({
                url : "../../json/activityList.json",
                type : "get",
                dataType : "json",
                success : function(data){
                	activityData = data;
                    activityList(activityData);
                }
            })
            layer.close(index);
        },2000);
    })

	//添加文章
	//改变窗口大小时，重置弹窗的高度，防止超出可视区域（如F12调出debug的操作）
	$(window).one("resize",function(){
		$(".activityAdd_btn").click(function(){
			var index = layui.layer.open({
				title : "添加活动",
				type : 2,
				content : "activityAdd.html",
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
		var $checkbox = $('.activity_list tbody input[type="checkbox"][name="checked"]');
		var $checked = $('.activity_list tbody input[type="checkbox"][name="checked"]:checked');
		if($checkbox.is(":checked")){
			layer.confirm('确定删除选中的信息？',{icon:3, title:'提示信息'},function(index){
				var index = layer.msg('删除中，请稍候',{icon: 16,time:false,shade:0.8});
	            setTimeout(function(){
	            	//删除数据
	            	for(var j=0;j<$checked.length;j++){
	            		for(var i=0;i<activityData.length;i++){
							if(activityData[i].activityId == $checked.eq(j).parents("tr").find(".activity_del").attr("data-id")){
								activityData.splice(i,1);
								activityList(activityData);
							}
						}
	            	}
	            	$('.activity_list thead input[type="checkbox"]').prop("checked",false);
	            	form.render();
	                layer.close(index);
					layer.msg("删除成功");
	            },2000);
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

    //是否可用
    form.on('switch(isEnable)', function(data){
        var index = layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/activity/JudgeOperate/enable";
        var enable = this.checked?1:0;
        var _this = $(this);
        $.ajax({
            data: {"id":_this.attr("data-id"),"enable":enable},
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
                    layer.msg("活动状态修改成功！");
                }
            },
            error:function(data){
                console.log(data.responseText);
            }
        })
    })
 
	//操作
	$("body").on("click",".activity_edit",function(e){  //编辑
        var no = $(e.currentTarget).data('id');
        var str = JSON.stringify(activityData[no]);
        window.sessionStorage.setItem("edit_activity",str);
        var index = layui.layer.open({
			title : "编辑文章",
			type : 2,
			content : "activityEdit.html",
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

    $("body").on("click",".activity_del",function(){  //删除
        var _this = $(this);
        layer.confirm('确定删除此信息？',{icon:3, title:'提示信息'},function(index){
            //_this.parents("tr").remove();
            var url = "/index.php/activity/JudgeOperate/del";
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
                        for(var i=0;i<activityData.length;i++){
                            if(activityData[i].id == _this.attr("data-id")){
                                activityData.splice(i,1);
                                activityList(activityData);
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

	function activityList(that){
		//渲染数据
		function renderDate(data,curr){
			var dataHtml = '';
			if(!that){
				currData = activityData.concat().splice(curr*nums-nums, nums);
			}else{
				currData = that.concat().splice(curr*nums-nums, nums);
			}
			if(currData.length != 0){
				for(var i=0;i<currData.length;i++){
					var pic = currData[i].pic==''?'-':currData[i].pic;
                    var name = currData[i].name==''?'-':currData[i].name;
                    var date = currData[i].date==''?'-':currData[i].date;
                    var enable = currData[i].enable==1?"checked":"";
                    var num = currData[i].num==''?'-':currData[i].num;
                    var prize = currData[i].prize==''?'-':currData[i].prize;
                    var phone = currData[i].phone==''?'-':currData[i].phone;
					dataHtml += '<tr>'
			    	+'<td><input type="checkbox" name="checked" lay-skin="primary" lay-filter="choose"></td>'
			    	+'<td>'+name+'</td>'
					+'<td><a href="#" onclick="fileSelect('+currData[i].id+')"><img id="ap'+currData[i].id+'" src="'+pic+'" height="100" /></a></td>'
                    +'<td>'+date+'</td>'
                    +'<td>'+prize+'</td>'
					+'<td>'+phone+'</td>'
                    +'<td>'+num+'</td>'
                    +'<td><input type="checkbox" name="enable" lay-skin="switch" data-id="'+data[i].id+'" lay-text="启用|禁用" lay-filter="isEnable"'+enable+'></td>'
			    	+'<td>'
                    +  '<a class="layui-btn layui-btn-warm layui-btn-mini activity_person" data-id="'+i+'"><i class="layui-icon">&#xe612;</i> 查看参与者</a>'
					+  '<a class="layui-btn layui-btn-mini activity_edit" data-id="'+i+'"><i class="iconfont icon-edit"></i> 编辑</a>'
					+  '<a class="layui-btn layui-btn-danger layui-btn-mini activity_del" data-id="'+data[i].id+'"><i class="layui-icon">&#xe640;</i> 删除</a>'
			        +'</td>'
			    	+'</tr>';
				}
			}else{
				dataHtml = '<tr><td colspan="9">暂无数据</td></tr>';
			}
		    return dataHtml;
		}

		//分页
		var nums = 13; //每页出现的数据量
		if(that){
			activityData = that;
		}
		laypage({
			cont : "page",
			pages : Math.ceil(activityData.length/nums),
			jump : function(obj){
				$(".activity_content").html(renderDate(activityData,obj.curr));
				$('.activity_list thead input[type="checkbox"]').prop("checked",false);
		    	form.render();
			}
		})
	}
})
