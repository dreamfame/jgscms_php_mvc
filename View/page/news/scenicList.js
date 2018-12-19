layui.config({
	base : "js/"
}).use(['form','layer','jquery','laypage'],function(){
	var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		$ = layui.jquery;

	//加载页面数据
	var scenicData = '';

	$.get("/index.php/scenic/JudgeOperate/list", function(data){
		var newArray = [];
        var data = eval('(' + data + ')');
        if(data.state=="0")
        {
            var dataHtml = '<tr><td colspan="7">暂无数据</td></tr>';
            $(".scenic_content").html(dataHtml);
            $('.scenic_list thead input[type="checkbox"]').prop("checked",false);
            form.render();
        }
        else{
            var newArray = [];
            scenicData = data.content;
            //执行加载数据的方法
            scenicList();
        }
	})

	//查询
	$(".search_btn").click(function(){
		var newArray = [];
		if($(".search_input").val() != ''){
			var index = layer.msg('查询中，请稍候',{icon: 16,time:false,shade:0.8});
            setTimeout(function(){
            	$.ajax({
					url : "../../json/scenicList.json",
					type : "get",
					dataType : "json",
					success : function(data){
						scenicData = data;
						for(var i=0;i<scenicData.length;i++){
							var scenicStr = scenicData[i];
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
		            		//文章标题
		            		if(scenicStr.title.indexOf(selectStr) > -1){
			            		scenicStr["title"] = changeStr(scenicStr.title);
		            		}
		            		//发布人
		            		if(scenicStr.operator.indexOf(selectStr) > -1){
			            		scenicStr["operator"] = changeStr(scenicStr.operator);
		            		}
		            		//浏览量
		            		if(scenicStr.see.indexOf(selectStr) > -1){
			            		scenicStr["see"] = changeStr(scenicStr.see);
		            		}
		            		//发布时间
		            		if(scenicStr.created_at.indexOf(selectStr) > -1){
			            		scenicStr["created_at"] = changeStr(scenicStr.created_at);
		            		}
		            		if(scenicStr.title.indexOf(selectStr)>-1 || scenicStr.operator.indexOf(selectStr)>-1 ||  scenicStr.see.indexOf(selectStr)>-1 || scenicStr.created_at.indexOf(selectStr)>-1){
		            			newArray.push(scenicStr);
		            		}
		            	}
		            	scenicData = newArray;
		            	scenicList(scenicData);
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
                url : "../../json/scenicList.json",
                type : "get",
                dataType : "json",
                success : function(data){
                	scenicData = data;
                    scenicList(scenicData);
                }
            })
            layer.close(index);
        },2000);
    })

	//添加文章
	//改变窗口大小时，重置弹窗的高度，防止超出可视区域（如F12调出debug的操作）
	$(window).one("resize",function(){
		$(".scenicAdd_btn").click(function(){
			var index = layui.layer.open({
				title : "添加文章",
				type : 2,
				content : "scenicAdd.html",
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
		var $checkbox = $('.scenic_list tbody input[type="checkbox"][name="checked"]');
		var $checked = $('.scenic_list tbody input[type="checkbox"][name="checked"]:checked');
		if($checkbox.is(":checked")){
			layer.confirm('确定删除选中的信息？',{icon:3, title:'提示信息'},function(index){
				var index = layer.msg('删除中，请稍候',{icon: 16,time:false,shade:0.8});
	            setTimeout(function(){
	            	//删除数据
	            	for(var j=0;j<$checked.length;j++){
	            		for(var i=0;i<scenicData.length;i++){
							if(scenicData[i].scenicId == $checked.eq(j).parents("tr").find(".scenic_del").attr("data-id")){
								scenicData.splice(i,1);
								scenicList(scenicData);
							}
						}
	            	}
	            	$('.scenic_list thead input[type="checkbox"]').prop("checked",false);
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

	//是否展示
	form.on('switch(isShow)', function(data){
        var index = layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/scenic/JudgeOperate/show";
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
        var url = "/index.php/scenic/JudgeOperate/top";
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
	$("body").on("click",".scenic_edit",function(e){  //编辑
        var no = $(e.currentTarget).data('id');
        var str = JSON.stringify(scenicData[no]);
        var typestr = JSON.stringify(typeData);
        window.sessionStorage.setItem("edit_scenic",str);
        window.sessionStorage.setItem("type",typestr);
        var index = layui.layer.open({
			title : "编辑文章",
			type : 2,
			content : "scenicEdit.html",
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

    $("body").on("click",".scenic_del",function(){  //删除
        var _this = $(this);
        layer.confirm('确定删除此信息？',{icon:3, title:'提示信息'},function(index){
            //_this.parents("tr").remove();
            var url = "/index.php/scenic/JudgeOperate/del";
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
                        for(var i=0;i<scenicData.length;i++){
                            if(scenicData[i].id == _this.attr("data-id")){
                                scenicData.splice(i,1);
                                scenicList(scenicData);
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

	function scenicList(that){
		//渲染数据
		function renderDate(data,curr){
			var dataHtml = '';
			if(!that){
				currData = scenicData.concat().splice(curr*nums-nums, nums);
			}else{
				currData = that.concat().splice(curr*nums-nums, nums);
			}
			if(currData.length != 0){
				for(var i=0;i<currData.length;i++){
                    var title = currData[i].title==''?'-':currData[i].title;
                    var type = currData[i].type==''?'-':currData[i].type;
                    var top = currData[i].top==1?"checked":"";
                    var show = currData[i].show==1?"checked":"";
                    var operator = currData[i].operator==''?'-':currData[i].operator;
                    var time = currData[i].created_at==''?'-':currData[i].created_at;
                    var see = currData[i].see==''?'-':currData[i].see;
					dataHtml += '<tr>'
			    	+'<td><input type="checkbox" name="checked" lay-skin="primary" lay-filter="choose"></td>'
			    	+'<td align="left">'+title+'</td>'
                    +'<td>'+type+'</td>'
                    +'<td>'+operator+'</td>'
			    	+ '<td>'+see+'</td>'
			    	+'<td><input type="checkbox" name="show" lay-skin="switch" data-id="'+data[i].id+'" lay-text="是|否" lay-filter="isShow"'+show+'></td>'
                    +'<td><input type="checkbox" name="top" lay-skin="switch" data-id="'+data[i].id+'" lay-text="是|否" lay-filter="isTop"'+top+'></td>'
			    	+'<td>'+time+'</td>'
			    	+'<td>'
					+  '<a class="layui-btn layui-btn-mini scenic_edit" data-id="'+i+'"><i class="iconfont icon-edit"></i> 编辑</a>'
					+  '<a class="layui-btn layui-btn-danger layui-btn-mini scenic_del" data-id="'+data[i].id+'"><i class="layui-icon">&#xe640;</i> 删除</a>'
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
			scenicData = that;
		}
		laypage({
			cont : "page",
			pages : Math.ceil(scenicData.length/nums),
			jump : function(obj){
				$(".scenic_content").html(renderDate(scenicData,obj.curr));
				$('.scenic_list thead input[type="checkbox"]').prop("checked",false);
		    	form.render();
			}
		})
	}
})
