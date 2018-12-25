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
	var photoData = '';

	$.get("/index.php/photo/JudgeOperate/list", function(data){
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

	function Search(start,end){
        var newArray = [];
        var index = layer.msg('查询中，请稍候',{icon: 16,time:false,shade:0.8});
            $.ajax({
                url : "../../json/photoList.json",
                type : "get",
                dataType : "json",
                success : function(data){
                    photoData = data;
                    for(var i=0;i<photoData.length;i++){
                        var photoStr = photoData[i];
                        if(start!=""&&end!=""){
                            if(!DateUtil(photoStr.created_at,start,end)){
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
                            //景点
                            if (photoStr.uid.indexOf(selectStr) > -1) {
                                photoStr["uid"] = changeStr(photoStr.uid);
                            }
                            //推荐星级
                            if (photoStr.des.indexOf(selectStr) > -1) {
                                photoStr["des"] = changeStr(photoStr.des);
                            }
                            //浏览量
                            if (photoStr.operator.indexOf(selectStr) > -1) {
                                photoStr["operator"] = changeStr(photoStr.operator);
                            }
                        }
                        if(photoStr.uid.indexOf(selectStr)>-1 || photoStr.des.indexOf(selectStr)>-1 ||  photoStr.operator.indexOf(selectStr)>-1){
                            newArray.push(photoStr);
                        }
                    }
                    photoData = newArray;
                    photoList(photoData);
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
                url : "../../json/photoList.json",
                type : "get",
                dataType : "json",
                success : function(data){
                	photoData = data;
                    photoList(photoData);
                }
            })
            layer.close(index);
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

    //是否置顶
    form.on('switch(isTop)', function(data){
        var index = layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.8});
        var url = "/index.php/photo/JudgeOperate/top";
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
            title : "分享图库",
            type : 2,
            content : "photos.html?id="+no,
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

    $("body").on("click",".photo_verify",function(e){  //景区图库
        var no = $(e.currentTarget).data('id');
        var id = photoData[no].id;
        var curVerify = photoData[no].verify;
        var operator = window.sessionStorage.getItem("username");
        var verify_layer = layui.layer.open({
            title : "图片审核",
            area : ["400px","160px"],
            type : "1",
            content : '<div class="skins_box">'+
                        '<form class="layui-form">'+
                            '<div class="layui-form-item">'+
                                '<input type="radio" name="verify" value="0" title="待审核" lay-filter="default">'+
                                '<input type="radio" name="verify" value="1" title="审核通过" lay-filter="pass">'+
                                '<input type="radio" name="verify" value="2" title="审核未通过" lay-filter="deny">'+
                            '</div>'+
                        '<div class="layui-form-item skinBtn">'+
                            '<a href="javascript:;" class="layui-btn layui-btn-small layui-btn-normal" lay-submit="" lay-filter="changeVerify">确定</a>'+
                            '<a href="javascript:;" class="layui-btn layui-btn-small layui-btn-primary" lay-submit="" lay-filter="noChangeVerify">取消</a>'+
                        '</div>'+
                        '</form>'+
                        '</div>',
            success : function(index, layero){
                $("[name='verify'][value='"+curVerify+"']").prop("checked", "checked");
                form.render();
                var verify_status = $('input:radio[name="verify"]:checked').val();
                $(".skins_box").removeClass("layui-hide");
                $(".skins_box .layui-form-radio").on("click",function(){
                    verify_status = $('input:radio[name="verify"]:checked').val();
                });
                form.on("submit(changeVerify)",function(data){
                    var param = '{"id":"'+id+'",';  //网站名称
                    param += '"operator":"'+operator+'",';
                    param += '"verify":"'+verify_status+'"}'; //网站备案号
                    var title = $('input:radio[name="verify"]:checked').attr("title");
                    layui.layer.close(verify_layer);
                    var load = layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
                    $.ajax({
                        data:JSON.parse(param),
                        url : "/index.php/photo/JudgeOperate/verify",
                        type : "post",
                        dataType : "json",
                        success : function(data){
                            if(data.state=="1"){
                                var color = "";
                                if(title=="待审核")
                                {
                                    color = "red";
                                }
                                else if(title=="审核通过"){
                                    color = "green";
                                }
                                else{
                                    color = "grey";
                                }
                                $("#photo_verify"+no).css("color",color);
                                $("#photo_verify"+no).text(title);
                                $("#photo_operator"+no).text(data.content);
                                photoData[no].verify = verify_status;
                                layer.msg("审核完成！");
                                layer.close(load);
                                form.render();
                            }
                            else{
                                layer.msg("审核出错！");
                                layer.close(load);
                            }
                        },
                        error:function(data){
                            layer.close(load);
                            layer.msg(data.responseText);
                        }
                    })
                    //layui.layer.close(verify_layer);
                });
                form.on("submit(noChangeVerify)",function(){
                    layui.layer.close(verify_layer);
                });
            }
        })
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
                    var verify = currData[i].verify==0?"<span id='photo_verify"+i+"' style='color:red'>待审核</span>":currData[i].verify==1?"<span id='photo_verify"+i+"' style='color:green'>审核通过</span>":"<span id='photo_verify"+i+"' style='color:grey'>审核未通过</span>";
                    var time = currData[i].created_at==''?'-':currData[i].created_at;
                    var operator = currData[i].operator==''?'-':currData[i].operator;
                    var top = currData[i].top==1?"checked":"";
					dataHtml += '<tr>'
			    	+'<td><input type="checkbox" name="checked" lay-skin="primary" lay-filter="choose"></td>'
			    	+'<td align="left">'+wx+'</td>'
                    +'<td>'+des+'</td>'
                    +'<td>'+time+'</td>'
			    	+'<td>'+verify+'</td>'
                    +'<td><span id="photo_operator'+i+'">'+operator+'</span></td>'
                    +'<td><input type="checkbox" name="top" lay-skin="switch" data-id="'+data[i].id+'" lay-text="是|否" lay-filter="isTop"'+top+'></td>'
			    	+'<td>'
                    +  '<a class="layui-btn layui-btn-warm layui-btn-mini photo_pic" data-id="'+data[i].id+'"><i class="layui-icon">&#xe65d;</i> 分享图库</a>'
                    +  '<a class="layui-btn layui-btn-normal layui-btn-mini photo_verify" data-id="'+i+'"><i class="layui-icon">&#xe6b2;</i> 审核</a>'
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
