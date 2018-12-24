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
            var sex = $('input:radio[name="sex"]:checked').val();
        	if(value!="") {
                start = date.year + "-" + date.month + "-" + date.date;
                end = endDate.year + "-" + endDate.month + "-" + endDate.date;
                Search(sex,start,end);
            }
            else{
            	start = "";
            	end = "";
                Search(sex,start,end);
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
	var usersData = '';

    $.get("/index.php/user/JudgeOperate/list", function(data){
        var newArray = [];
        var data = eval('(' + data + ')');
        if(data.state=="0")
        {
            var dataHtml = '<tr><td colspan="9">暂无数据</td></tr>';
            $(".users_content").html(dataHtml);
            $('.users_list thead input[type="checkbox"]').prop("checked",false);
            form.render();
        }
        else{
            var newArray = [];
            usersData = data.content;
            //执行加载数据的方法
            usersList();
        }
    })

	//性别查询
    form.on('radio(sex)', function(data){
        Search(data.value,start,end);
    });

    function Search(sex,start,end){
        var userArray = [];
        var index = layer.msg('查询中，请稍候',{icon: 16,time:false,shade:0.8});
            $.ajax({
                url : "../../json/userList.json",
                type : "get",
                dataType : "json",
                success : function(data){
                    if(window.sessionStorage.getItem("addUser")){
                        var addUser = window.sessionStorage.getItem("addUser");
                        usersData = JSON.parse(addUser).concat(data);
                    }else{
                        usersData = data;
                    }
                    for(var i=0;i<usersData.length;i++){
                        var usersStr = usersData[i];
                        if(sex!="-1"){
                            if(usersStr.gender!=sex){
                            	continue;
							}
						}
						if(start!=""&&end!=""){
                            if(!DateUtil(usersStr.created_at,start,end)){
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
                            if (usersStr.openid.indexOf(selectStr) > -1) {
                                usersStr["openid"] = changeStr(usersStr.openid);
                            }

                            if (usersStr.wx.indexOf(selectStr) > -1) {
                                usersStr["wx"] = changeStr(usersStr.wx);
                            }

                            if (usersStr.nickname.indexOf(selectStr) > -1) {
                                usersStr["nickname"] = changeStr(usersStr.nickname);
                            }

                            if (usersStr.city.indexOf(selectStr) > -1) {
                                usersStr["city"] = changeStr(usersStr.city);
                            }
                            if (usersStr.country.indexOf(selectStr) > -1) {
                                usersStr["country"] = changeStr(usersStr.country);
                            }
                        }
                        if (usersStr.openid.indexOf(selectStr) > -1 || usersStr.wx.indexOf(selectStr) > -1 || usersStr.nickname.indexOf(selectStr) > -1 || usersStr.city.indexOf(selectStr) > -1 || usersStr.country.indexOf(selectStr) > -1) {
                        	userArray.push(usersStr);
                        }
                    }
                    usersData = userArray;
                    usersList(usersData);
                }
            })

            layer.close(index);
    }

	//查询
	$(".search_btn").click(function(){
		if($(".search_input").val() != '') {
            var sex = $('input:radio[name="sex"]:checked').val();
            Search(sex,start,end);
        }
		else
		{
			layer.msg("请输入需要查询的内容");
		}
	})

	//添加会员
	$(".usersAdd_btn").click(function(){
		var index = layui.layer.open({
			title : "添加会员",
			type : 2,
			content : "addUser.html",
			success : function(layero, index){
				setTimeout(function(){
					layui.layer.tips('点击此处返回会员列表', '.layui-layer-setwin .layui-layer-close', {
						tips: 3
					});
				},500)
			}
		})
		//改变窗口大小时，重置弹窗的高度，防止超出可视区域（如F12调出debug的操作）
		$(window).resize(function(){
			layui.layer.full(index);
		})
		layui.layer.full(index);
	})

	//批量删除
	$(".batchDel").click(function(){
		var $checkbox = $('.users_list tbody input[type="checkbox"][name="checked"]');
		var $checked = $('.users_list tbody input[type="checkbox"][name="checked"]:checked');
		if($checkbox.is(":checked")){
			layer.confirm('确定删除选中的信息？',{icon:3, title:'提示信息'},function(index){
				var index = layer.msg('删除中，请稍候',{icon: 16,time:false,shade:0.8});
	            setTimeout(function(){
	            	//删除数据
	            	for(var j=0;j<$checked.length;j++){
	            		for(var i=0;i<usersData.length;i++){
							if(usersData[i].newsId == $checked.eq(j).parents("tr").find(".news_del").attr("data-id")){
								usersData.splice(i,1);
								usersList(usersData);
							}
						}
	            	}
	            	$('.users_list thead input[type="checkbox"]').prop("checked",false);
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

	//通过判断文章是否全部选中来确定全选按钮是否选中
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

    $(".showAll_btn").click(function(){
        var index = layer.msg('加载中，请稍候',{icon: 16,time:false,shade:0.8});
        $(".search_input").val("");
        $("input[name='sex']").get(0).checked=true;
        $("#start").val("");
        start = "";
        end = "";
            $.ajax({
                url : "../../json/userList.json",
                type : "get",
                dataType : "json",
                success : function(data){
                    if(window.sessionStorage.getItem("addUser")){
                        var addUser = window.sessionStorage.getItem("addUser");
                        usersData = JSON.parse(addUser).concat(data);
                    }else{
                        usersData = data;
                    }
                    usersList(usersData);
                }
            })
            layer.close(index);
    })

	//操作
	$("body").on("click",".users_edit",function(){  //编辑
		layer.alert('您点击了会员编辑按钮，由于是纯静态页面，所以暂时不存在编辑内容，后期会添加，敬请谅解。。。',{icon:6, title:'文章编辑'});
	})

	$("body").on("click",".users_del",function(){  //删除
		var _this = $(this);
		layer.confirm('确定删除此用户？',{icon:3, title:'提示信息'},function(index){
			//_this.parents("tr").remove();
			for(var i=0;i<usersData.length;i++){
				if(usersData[i].usersId == _this.attr("data-id")){
					usersData.splice(i,1);
					usersList(usersData);
				}
			}
			layer.close(index);
		});
	})

	function usersList(){
		//渲染数据
		function renderDate(data,curr){
			var dataHtml = '';
			currData = usersData.concat().splice(curr*nums-nums, nums);
			if(currData.length != 0){
				for(var i=0;i<currData.length;i++){
                    var gender = currData[i].gender == 0?"女":"男";
					dataHtml += '<tr>'
			    	+  '<td><input type="checkbox" name="checked" lay-skin="primary" lay-filter="choose"></td>'
                    +  '<td><img id="am'+currData[i].id+'" src="'+currData[i].avatar+'" height="132" /></td>'
                    +  '<td>'+currData[i].openid+'</td>'
					+  '<td>'+currData[i].wx+'</td>'
			    	+  '<td>'+gender+'</td>'
			    	+  '<td>'+currData[i].nickname+'</td>'
			    	+  '<td>'+currData[i].city+'</td>'
			    	+  '<td>'+currData[i].country+'</td>'
                    +  '<td>'+currData[i].created_at+'</td>'
			    	+'</tr>';
				}
			}else{
				dataHtml = '<tr><td colspan="9">暂无数据</td></tr>';
			}
		    return dataHtml;
		}

		//分页
		var nums = 13; //每页出现的数据量
		laypage({
			cont : "page",
			pages : Math.ceil(usersData.length/nums),
			jump : function(obj){
				$(".users_content").html(renderDate(usersData,obj.curr));
				$('.users_list thead input[type="checkbox"]').prop("checked",false);
		    	form.render();
			}
		})
	}
        
})

