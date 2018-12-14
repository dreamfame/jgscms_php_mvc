layui.config({
	base : "js/"
}).use(['form','layer','jquery','laypage'],function(){
	var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		$ = layui.jquery;

	//加载页面数据
	var adminData = '';
	$.get("/index.php/admin/JugdeOperate/list", function(data){
        var data = eval('(' + data + ')');
		if(data.state=="0")
		{
            layer.msg("请求出错啦");
            return;
		}
		else{
			var newArray = [];
			adminData = data.content;
			if(window.sessionStorage.getItem("addNews")){
				var addNews = window.sessionStorage.getItem("addNews");
				newsData = JSON.parse(addNews).concat(newsData);
			}
			//执行加载数据的方法
			adminList();
		}
	})

	//查询
	$(".search_btn").click(function(){
		var newArray = [];
		if($(".search_input").val() != ''){
			var index = layer.msg('查询中，请稍候',{icon: 16,time:false,shade:0.8});
            setTimeout(function(){
            	$.ajax({
					url : "../../json/adminList.json",
					type : "get",
					dataType : "json",
					success : function(data){
						if(window.sessionStorage.getItem("addNews")){
							var addNews = window.sessionStorage.getItem("addNews");
							adminData = JSON.parse(addNews).concat(data);
						}else{
                            adminData = data;
						}
						for(var i=0;i<adminData.length;i++){
							var adminStr = adminData[i];
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
		            		//用户名
		            		if(adminStr.username.indexOf(selectStr) > -1){
                                adminStr["username"] = changeStr(adminStr.username);
		            		}
		            		//昵称
		            		if(adminStr.nickname.indexOf(selectStr) > -1){
                                adminStr["nickname"] = changeStr(adminStr.nickname);
		            		}
		            		//年龄
		            		if(adminStr.age.indexOf(selectStr) > -1){
                                adminStr["age"] = changeStr(adminStr.age);
		            		}
		            		//手机号
		            		if(adminStr.phone.indexOf(selectStr) > -1){
                                adminStr["phone"] = changeStr(adminStr.phone);
		            		}
		            		//邮箱
                            if(adminStr.email.indexOf(selectStr) > -1){
                                adminStr["email"] = changeStr(adminStr.email);
                            }
                            //角色
                            if(adminStr.role.indexOf(selectStr) > -1){
                                adminStr["role"] = changeStr(adminStr.role);
                            }
		            		if(adminStr.username.indexOf(selectStr)>-1 || adminStr.nickname.indexOf(selectStr)>-1 || adminStr.role.indexOf(selectStr)>-1 || adminStr.age.indexOf(selectStr)>-1 || adminStr.phone.indexOf(selectStr)>-1||adminStr.email.indexOf(selectStr) > -1){
		            			newArray.push(adminStr);
		            		}
		            	}
		            	adminData = newArray;
		            	adminList(adminData);
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
                url : "../../json/adminList.json",
                type : "get",
                dataType : "json",
                success : function(data){
                    if(window.sessionStorage.getItem("addNews")){
                        var addNews = window.sessionStorage.getItem("addNews");
                        adminData = JSON.parse(addNews).concat(data);
                    }else{
                        adminData = data;
                    }
                    adminList(adminData);
                }
            })
            layer.close(index);
        },2000);
    })

	//添加文章
	//改变窗口大小时，重置弹窗的高度，防止超出可视区域（如F12调出debug的操作）
	$(window).one("resize",function(){
		$(".adminAdd_btn").click(function(){
			var index = layui.layer.open({
				title : "添加管理员",
				type : 2,
				content : "adminAdd.html",
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

	//批量删除
	$(".batchDel").click(function(){
		var $checkbox = $('.news_list tbody input[type="checkbox"][name="checked"]');
		var $checked = $('.news_list tbody input[type="checkbox"][name="checked"]:checked');
		if($checkbox.is(":checked")){
			layer.confirm('确定删除选中的信息？',{icon:3, title:'提示信息'},function(index){
				var index = layer.msg('删除中，请稍候',{icon: 16,time:false,shade:0.8});
	            setTimeout(function(){
	            	//删除数据
	            	for(var j=0;j<$checked.length;j++){
	            		for(var i=0;i<newsData.length;i++){
							if(newsData[i].newsId == $checked.eq(j).parents("tr").find(".news_del").attr("data-id")){
								newsData.splice(i,1);
								newsList(newsData);
							}
						}
	            	}
	            	$('.news_list thead input[type="checkbox"]').prop("checked",false);
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
		var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]:not([name="show"])');
		child.each(function(index, item){
			item.checked = data.elem.checked;
		});
		form.render('checkbox');
	});

	//通过判断是否全部选中来确定全选按钮是否选中
	form.on("checkbox(choose)",function(data){
		var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]:not([name="show"])');
		var childChecked = $(data.elem).parents('table').find('tbody input[type="checkbox"]:not([name="show"]):checked')
		if(childChecked.length == child.length){
			$(data.elem).parents('table').find('thead input#allChoose').get(0).checked = true;
		}else{
			$(data.elem).parents('table').find('thead input#allChoose').get(0).checked = false;
		}
		form.render('checkbox');
	})

	//禁用
	form.on('switch(isShow)', function(data){
		var index = layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.8});
        setTimeout(function(){
            layer.close(index);
			layer.msg("状态修改成功！");
        },2000);
	})
 
	//操作
	$("body").on("click",".news_edit",function(){  //编辑
		layer.alert('您点击了编辑按钮，由于是纯静态页面，所以暂时不存在编辑内容，后期会添加，敬请谅解。。。',{icon:6, title:'文章编辑'});
	})

	$("body").on("click",".news_collect",function(){  //收藏.
		if($(this).text().indexOf("已收藏") > 0){
			layer.msg("取消收藏成功！");
			$(this).html("<i class='layui-icon'>&#xe600;</i> 收藏");
		}else{
			layer.msg("收藏成功！");
			$(this).html("<i class='iconfont icon-star'></i> 已收藏");
		}
	})

	$("body").on("click",".admin_del",function(){  //删除
		var _this = $(this);
		layer.confirm('确定删除此信息？',{icon:3, title:'提示信息'},function(index){
			//_this.parents("tr").remove();
            var url = "/index.php/admin/JugdeOperate/del";
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
                        for(var i=0;i<adminData.length;i++){
                            if(adminData[i].id == _this.attr("data-id")){
                                adminData.splice(i,1);
                                adminList(adminData);
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

	function adminList(that){
		//渲染数据
		function renderDate(data,curr){
			var dataHtml = '';
			if(!that){
				currData = adminData.concat().splice(curr*nums-nums, nums);
			}else{
				currData = that.concat().splice(curr*nums-nums, nums);
			}
			if(currData.length != 0){
				for(var i=0;i<currData.length;i++){
					var username = currData[i].username==''?'-':currData[i].username;
					var nickname = currData[i].nickname==''?'-':currData[i].nickname;
					var role = currData[i].role==''?'-':currData[i].role;
					var age = currData[i].age==''?'-':currData[i].age;
					var phone = currData[i].phone==''?'-':currData[i].phone;
                    var email = currData[i].email==''?'-':currData[i].email;
                    var status = currData[i].status==1?"checked":"";
					dataHtml += '<tr>'
			    	+'<td><input type="checkbox" name="checked" lay-skin="primary" lay-filter="choose"></td>'
			    	+'<td>'+username+'</td>'
			    	+'<td>'+nickname+'</td>'
					+'<td>'+role+'</td>'
                    +'<td>'+age+'</td>'
                    +'<td>'+phone+'</td>'
                    +'<td>'+email+'</td>'
					+'<td><input type="checkbox" name="show" lay-skin="switch" lay-text="启用|禁用" lay-filter="isShow"'+status+'></td>'
			    	+'<td>'
					+  '<a class="layui-btn layui-btn-mini admin_edit" data-id="'+data[i].id+'"><i class="iconfont icon-edit"></i> 编辑</a>'
					+  '<a class="layui-btn layui-btn-normal layui-btn-mini admin_reset"><i class="layui-icon">&#xe600;</i> 重置密码</a>'
					+  '<a class="layui-btn layui-btn-danger layui-btn-mini admin_del" data-id="'+data[i].id+'"><i class="layui-icon">&#xe640;</i> 删除</a>'
			        +'</td>'
			    	+'</tr>';
				}
			}else{
				dataHtml = '<tr><td colspan="8">暂无数据</td></tr>';
			}
		    return dataHtml;
		}

		//分页
		var nums = 13; //每页出现的数据量
		if(that){
			adminData = that;
		}
		laypage({
			cont : "page",
			pages : Math.ceil(adminData.length/nums),
			jump : function(obj){
				$(".admin_content").html(renderDate(adminData,obj.curr));
				$('.admin_list thead input[type="checkbox"]').prop("checked",false);
		    	form.render();
			}
		})
	}
})
