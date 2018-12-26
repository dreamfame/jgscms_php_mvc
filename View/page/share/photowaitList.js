layui.config({
	base : "js/"
}).use(['form','layer','jquery','laypage'],function(){
	var form = layui.form(),
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		$ = layui.jquery;

	//加载页面数据
	var photoData = '';

	$.post("/index.php/photo/JudgeOperate/query",{"verify":0}, function(data){
		var newArray = [];
        var data = eval('(' + data + ')');
        if(data.state=="0")
        {
            var dataHtml = '<tr><td colspan="7">暂无数据</td></tr>';
            $(".photo_content").html(dataHtml);
            $('.photo_list thead input[type="checkbox"]').prop("checked",false);
            form.render();
        }
        else{
            var newArray = [];
            photoData = data.content;
            //执行加载数据的方法
            photoList();
        }
	})

	//查询
	$(".search_btn").click(function(){
		var newArray = [];
		if($(".search_input").val() != ''){
			var index = layer.msg('查询中，请稍候',{icon: 16,time:false,shade:0.8});
            setTimeout(function(){
            	$.ajax({
					url : "../../json/photoList.json",
					type : "get",
					dataType : "json",
					success : function(data){
						photoData = data;
						for(var i=0;i<photoData.length;i++){
							var photoStr = photoData[i];
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
		            		if(photoStr.name.indexOf(selectStr) > -1){
			            		photoStr["name"] = changeStr(photoStr.name);
		            		}
		            		//推荐星级
		            		if(photoStr.recommend.indexOf(selectStr) > -1){
			            		photoStr["recommend"] = changeStr(photoStr.recommend);
		            		}
		            		//浏览量
		            		if(photoStr.see.indexOf(selectStr) > -1){
			            		photoStr["see"] = changeStr(photoStr.see);
		            		}
		            		//发布时间
		            		if(photoStr.created_at.indexOf(selectStr) > -1){
			            		photoStr["created_at"] = changeStr(photoStr.created_at);
		            		}
		            		if(photoStr.name.indexOf(selectStr)>-1 || photoStr.recommend.indexOf(selectStr)>-1 ||  photoStr.see.indexOf(selectStr)>-1 || photoStr.created_at.indexOf(selectStr)>-1){
		            			newArray.push(photoStr);
		            		}
		            	}
		            	photoData = newArray;
		            	photoList(photoData);
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
                url : "../../json/photoList.json",
                type : "get",
                dataType : "json",
                success : function(data){
                	photoData = data;
                    photoList(photoData);
                }
            })
            layer.close(index);
        },2000);
    })

	//批量删除
	$(".batchDel").click(function(){
        var $checkbox = $('.photo_list tbody input[type="checkbox"][name="checked"]');
        var $checked = $('.photo_list tbody input[type="checkbox"][name="checked"]:checked');
        if($checkbox.is(":checked")){
            layer.confirm('确定删除选中的信息？',{icon:3, title:'提示信息'},function(index){
                var index = layer.msg('删除中，请稍候',{icon: 16,time:false,shade:0.8});
                //删除数据
                var delinfo = [];
                for(var j=0;j<$checked.length;j++){
                    for(var i=0;i<photoData.length;i++){
                        if(photoData[i].id == $checked.eq(j).parents("tr").find(".photo_del").attr("data-id")){
                            delinfo.push(photoData[i].id);
                            photoData.splice(i,1);
                            photoList(photoData);
                        }
                    }
                }
                $.ajax({
                    data: {"del_id":delinfo},
                    type: "POST",
                    dataType: "JSON",
                    url: "/index.php/photo/JudgeOperate/batchDel",
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

	//操作
	$("body").on("click",".photo_edit",function(e){  //编辑
        var no = $(e.currentTarget).data('id');
        var str = JSON.stringify(photoData[no]);
        window.sessionStorage.setItem("edit_photo",str);
        var index = layui.layer.open({
			title : "编辑文章",
			type : 2,
			content : "photoEdit.html",
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

    $("body").on("click",".photo_pic",function(e){  //景区图库
        var no = $(e.currentTarget).data('id');
        var index = layui.layer.open({
            title : "图库",
            type : 2,
            content : "../img/images.html?id="+no,
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

    $("body").on("click",".photo_del",function(){  //删除
        var _this = $(this);
        layer.confirm('确定删除此信息？',{icon:3, title:'提示信息'},function(index){
            //_this.parents("tr").remove();
            var url = "/index.php/photo/JudgeOperate/del";
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
                        for(var i=0;i<photoData.length;i++){
                            if(photoData[i].id == _this.attr("data-id")){
                                photoData.splice(i,1);
                                photoList(photoData);
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

	function photoList(that){
		//渲染数据
		function renderDate(data,curr){
			var dataHtml = '';
			if(!that){
				currData = photoData.concat().splice(curr*nums-nums, nums);
			}else{
				currData = that.concat().splice(curr*nums-nums, nums);
			}
			if(currData.length != 0){
				for(var i=0;i<currData.length;i++){
                    var wx = currData[i].uid==''?'-':currData[i].uid;
                    var des = currData[i].des==''?'-':currData[i].des;
                    var verify = currData[i].verify==1?"checked":"";
                    var time = currData[i].created_at==''?'-':currData[i].created_at;
                    var operator = currData[i].operator==''?'-':currData[i].operator;
					dataHtml += '<tr>'
			    	+'<td><input type="checkbox" name="checked" lay-skin="primary" lay-filter="choose"></td>'
			    	+'<td align="left">'+wx+'</td>'
                    +'<td>'+des+'</td>'
                    +'<td>'+time+'</td>'
			    	+'<td><input type="checkbox" name="verify" lay-skin="switch" data-id="'+data[i].id+'" lay-text="通过|拒绝" lay-filter="isVerify"'+verify+'></td>'
                    +'<td>'+operator+'</td>'
			    	+'<td>'
                    +  '<a class="layui-btn layui-btn-warm layui-btn-mini photo_pic" data-id="'+data[i].id+'"><i class="layui-icon">&#xe65d;</i>分享图库</a>'
					+  '<a class="layui-btn layui-btn-mini photo_edit" data-id="'+i+'"><i class="iconfont icon-edit"></i> 编辑</a>'
					+  '<a class="layui-btn layui-btn-danger layui-btn-mini photo_del" data-id="'+data[i].id+'"><i class="layui-icon">&#xe640;</i> 删除</a>'
			        +'</td>'
			    	+'</tr>';
				}
			}else{
				dataHtml = '<tr><td colspan="7">暂无数据</td></tr>';
			}
		    return dataHtml;
		}

		//分页
		var nums = 13; //每页出现的数据量
		if(that){
			photoData = that;
		}
		laypage({
			cont : "page",
			pages : Math.ceil(photoData.length/nums),
			jump : function(obj){
				$(".photo_content").html(renderDate(photoData,obj.curr));
				$('.photo_list thead input[type="checkbox"]').prop("checked",false);
		    	form.render();
			}
		})
	}
})