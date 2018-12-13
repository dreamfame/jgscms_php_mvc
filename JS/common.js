Date.prototype.format = function(format) {
	var o = {
		"M+" : this.getMonth() + 1, // month
		"d+" : this.getDate(), // day
		"h+" : this.getHours(), // hour
		"m+" : this.getMinutes(), // minute
		"s+" : this.getSeconds(), // second
		"q+" : Math.floor((this.getMonth() + 3) / 3), // quarter
		"S" : this.getMilliseconds()
	// millisecond
	}
	if (/(y+)/.test(format))
		format = format.replace(RegExp.$1, (this.getFullYear() + "")
				.substr(4 - RegExp.$1.length));
	for ( var k in o)
		if (new RegExp("(" + k + ")").test(format))
			format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k]
					: ("00" + o[k]).substr(("" + o[k]).length));
	return format;
}
// 计算年龄
function getAge(birth) {
	if (birth === null || birth === undefined) {
		return "";
	}
	birth = new Date(birth);
	var d = new Date();
	return d.getFullYear()-birth.getFullYear()-((d.getMonth()<birth.getMonth()|| d.getMonth()==birth.getMonth() && d.getDate()<birth.getDate())?1:0);  
}
try {
	Highcharts.setOptions({
		global: {
			useUTC: false
		},
		lang: {
			resetZoom: "重置缩放"
		},
		colors: ['#4AD1E8', '#00CC00', '#FFBB33', '#DC0000', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
	}); 
} catch (e) {
	
}
try {
	$.validator.setDefaults({
	    highlight: function(element) {
	        $(element).closest('.form-group').addClass('has-error');
	    },
	    unhighlight: function(element) {
	        $(element).closest('.form-group').removeClass('has-error');
	    },
	    errorElement: 'span',
	    errorClass: 'help-block',
	    errorPlacement: function(error, element) {
	        if(element.parent('.input-group').length) {
	            error.insertAfter(element.parent());
	        } else if (element.parent("label").parent(".radio-inline").length) {
	        	
	        } else if (element.parent("label").length) {
	        	error.insertAfter(element.parent());
	        } else {
	            error.insertAfter(element);
	        }
	    }
	});
	$.validator.addMethod("pattern", function(value, element, param) {
	    if (typeof param === 'string')
	        param = new RegExp(param);
	    return this.optional(element) || param.test(value);
	}, "输入格式错误");
	$.validator.messages = {
	    required: "输入不能为空.",
	    remote: "用户名已经存在.",    // 自己定义
	    email: "请输入一个有效的电子邮件地址.",
	    url: "请输入一个有效的URL.",
	    date: "请输入一个有效的日期.",
	    dateISO: "请输入一个有效的日期 ( ISO ) ( 例：2014/08/28 ).",
	    number: "请输入一个有效的数字.",
	    digits: "请输入一个正整数.",
	    creditcard: "请输入一个有效的信用卡号.",
	    equalTo: "请再次输入相同的值.",
	    maxlength: $.validator.format( "请输入不超过{0}个字符." ),
	    minlength: $.validator.format( "请输入至少{0}个字符." ),
	    rangelength: $.validator.format( "请输入一个字符长{0}至{1}的字符." ),
	    range: $.validator.format( "请输入一个{0}至{1}的数." ),
	    max: $.validator.format( "请输入一个值小于或等于{0}的数." ),
	    min: $.validator.format( "请输入一个值大于或等于{0}的数." )
	};
} catch (e) {};

/**
 * @param options, title：图表标题，target：目标容器id，type：图表类型
 */
function trendChart(options) {
	return new Highcharts.Chart({
		credits: { enabled: false },
		exporting: { enabled: false },
        chart: { renderTo: options.target, zoomType: 'x', type: options.type },
        title: { text: options.title || ''},
        xAxis: { type: 'datetime',
            labels: { style: {color: '#aaa'}, step: 1, formatter: function () { return Highcharts.dateFormat('%Y年%m月', this.value); }}},
        yAxis: { title: {text: ''},
            gridLineColor: '#eee',
            labels: { style: {color: '#aaa'} },
            stackLabels: { enabled: true,
                style: { fontWeight: 'bold', color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray' }
            }},
        tooltip: { shared: true,
            backgroundColor: '#000000',
            borderColor: '#000000',
            style: {color: '#fff'},
            crosshairs: { color: '#aaa', dashStyle: 'ShortDot' },
            formatter: function() {
            	var s = "";
            	s = Highcharts.dateFormat("%Y年%m月", this.x);
            	$.each(this.points, function(i, point) {
                    s += '<br/><span style="color: ' + point.series.color + ';">'+ point.series.name +': <b>'+ point.y +'</b></span>';
                });
                return s;
            }},
        legend: { borderWidth: 0,
        	borderRadius: 5,
        	backgroundColor: '#eee',
    		itemHoverStyle: { color: '#D00'},
    		enabled: true},
        plotOptions: {
            area: { lineWidth: 1,
                marker: { enabled: true, radius: 4 },
                shadow: false,
                states: { hover: { lineWidth: 1 }},
                dataLabels: { enabled: true, color: "#CCC", }},
            line: { lineWidth: 1,
            	marker: {enabled: true, radius: 4},
            	dataLabels: { enabled: false, color: "#CCC", },
            	states: { hover: { lineWidth: 1 } } },
            column: { stacking: 'normal', 
            	dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: { textShadow: '0 0 2px black, 0 0 2px black' } } } },
		});
}
(function($){
	// 分页处理脚本
	$(document).on("paging", ".ajaxPager", function() {
		var total = $(this).data("total");
		var page = $(this).data("page");
		var pageLinks = "";
		
		if (page > 1) {
			pageLinks += "<li><a data-page='" + 1 + "'>«</a></li>";
			pageLinks += "<li><a data-page='" + (page - 1) + "'>‹</a></li>";
		}
		if (page <= 3 || total <= 5) {
			for (var i = 1; i <= 5 && i <= total; i++) {
				if (i == page) pageLinks += "<li class='active'><a data-page='" + i + "'>" + i + "</a></li>";
				else pageLinks += "<li><a data-page='" + i + "'>" + i + "</a></li>";
			}        		
		} else if (page > total - 2) {
			for (var i = total - 4; i <= total; i++) {
				if (i == page) pageLinks += "<li class='active'><a data-page='" + i + "'>" + i + "</a></li>";
				else pageLinks += "<li><a data-page='" + i + "'>" + i + "</a></li>";
			}
		} else {
			for (var i = page - 2; i <= page + 2; i++) {
				if (i == page) pageLinks += "<li class='active'><a data-page='" + i + "'>" + i + "</a></li>";
				else pageLinks += "<li><a data-page='" + i + "'>" + i + "</a></li>";
			}
		}
		if (page < total) {
			pageLinks += "<li><a data-page='" + (1 + page) + "'>›</a></li>";
			pageLinks += "<li><a data-page='" + total + "'>»</a></li>";
		}
		
		$(this).html('<nav><ul class="pagination">' + pageLinks + '</ul></nav>');		
	});
	
	$(document).on("click", ".ajaxPager a", function(e) {
		var $pager = $(this).closest(".ajaxPager");
		$pager.data("pageProc")($pager, $(this).data("page"), $pager.data("pageSize"));
		e.preventDefault();
	});
	

	// 整数输入框
	$(document).on('click', '.spinner .btn:first', function() {
		var input = $(this).closest(".spinner").find("input");
		input.val(parseInt(input.val(), 10) + 1);
	});
	$(document).on('click', '.spinner .btn:last', function() {
		var input = $(this).closest(".spinner").find("input");
		input.val(parseInt(input.val(), 10) - 1);
	});
	
	// 带有输入框的radio组
	$(document).on("click", ".radio-polymic input[type='radio']", function(e) {
		$(this).closest(".radio-polymic").find("input[type='text']").val("");
	});
	
	$(document).on("focus", ".radio-polymic input[type='text']", function(e) {
		$(this).closest(".radio-polymic").find("input[type='radio']").prop("checked", false);
	});
	
	$(document).on("setRadioPolymic", ".radio-polymic", function(e, val) {
		var radio = $(this).find("input[type='radio'][value='" + val + "']");
		if (radio.size() > 0) {
			radio.prop("checked", true);
		} else {
			$(this).find("input[type='text']").val(val);
		}
	});
	
	// 日期时间输入
	$(document).ready(function() {
		$(".form-group input.form-control.datepicker, .form-group input.form-control.timepicker, .form-group input.form-control.datetimepicker").after("<span style='position: absolute; top: 12px; right: 25px;' class='glyphicon glyphicon-calendar'></span>");
	});
	$(document).on("focus click", "input.datepicker", function(e) {
		$(this).datetimepicker({minView: "month", format: "yyyy-mm-dd", language: "zh-CN", autoclose: true});
	});
	
	$(document).on("focus click", "input.timepicker", function(e) {
		$(this).datetimepicker({startView: "day", minView: "hour", maxView: "day", format: "hh:ii", language: "zh-CN", autoclose: true});
	});
	
	$(document).on("focus click", "input.datetimepicker", function(e) {
		$(this).datetimepicker({startView: "day", minView: "hour", maxView: "month", format: "yyyy-mm-dd hh:ii", language: "zh-CN", autoclose: true});
	});
	
	$(document).on("show.bs.modal", "input.datepicker, input.timepicker, input.datetimepicker", function(e) {
		e.stopPropagation(); // 阻止日期时间选择插件发出的show.bs.modal事件向父节点传播。
	});
	
	$(document).on("hide.bs.modal", "input.datepicker, input.timepicker, input.datetimepicker", function(e) {
		$(e.target).blur();
	});
	
	// ajax 文件上传
	$(document).on("click", ".ajax-uploader .change-file", function(e) {
		e.preventDefault();
		var uploader = $(this).closest(".ajax-uploader");
		$(this).blur();
		if (uploader.find(":file").length) return;
		uploader.data("name", uploader.find("input:hidden").attr("name"));
		var modal  = $("<div class='modal fade' class='upload-modal' role='dialog'>" +
			"<div class='modal-dialog'><div class='modal-content'>" +
				"<div class='modal-header'>" + 
					"<button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span></button>" +
					"<h4 class='modal-title'>文件上传</h4>" +
				"</div>" +
				"<div class='modal-body'>" +
					"<form style='display: inline-block;' method='post' target='upload' action='" + uploader.data("url") + "' enctype='multipart/form-data'>" +
						"<div class='form-group'><label>文件上传</label><input type='file' name='" + uploader.data("name") + "' /></div>" +
						"<div class='form-group'><img id='previewImg' style='display: none;' class='ajax-loader' src='../Resources/loader.gif' width='30' height='30' />" +
						"<span class='btn btn-warning'>取消上传图片</span>" +
					"</form>" +
				"</div>" +
			"</div></div></div>");
		var iframe = $("<iframe name='upload' id='uploader' style='width: auto; height: auto; overflow: scroll; border: none;'><span class='loader'></span></iframe>");
		$(document).find("body").append(modal);
		$(document).find("body").append(iframe);
		modal.modal("show");
		//file.click(); // 激活上传按钮点击事件，IE不兼容
		//文件提交完毕后
		var file = modal.find(":file");
		var form = modal.find("form");
		var btn  = modal.find("span.btn");
		file.change(function() {
			modal.find(".ajax-loader").show();
			$(this).closest("form").submit();
		});
		form.on("submit", function(e) {
			e.stopPropagation();
		});
		modal.on('hidden.bs.modal', function(e) {
			$(this).remove();
		});
		iframe.load(function() {
			var path = $("#previewImg").attr("src");
			iframe.remove();
			modal.modal('hide');
			uploader.find("img.preview").attr("src", path);
			uploader.find("input:hidden").val(path);
		});
		btn.click(function() {
			uploader.find(".ajax-loader").hide();
			iframe.remove();
			modal.modal('hide');
		});
	});
	$(document).on("setImgSrc", ".ajax-uploader img.preview", function(e, src) {
		var path = $(this).attr("src");
		path = path.substring(0, path.lastIndexOf("/") + 1);
		$(this).attr("src", path + src);
		$(this).siblings("input").val(src);
	});
	/* formdata 文件上传
	$(document).on("change", ".ajax-uploader :file", function(e) {
		var form = new FormData();
		var wrapper = $(this).closest(".ajax-uploader");
		form.append(wrapper.data("name"), $(this)[0].files[0]);
		$.ajax({url: wrapper.data("url"), type: "POST", data: form, processData: false, contentType: false})
			.done(function(json) {
				if (json.result == false) {
					alert(json.message);
					return;
				}
				wrapper.empty().append(wrapper.data("html"));
				var path = wrapper.find("img").attr("src");
				path = path.substring(0, path.lastIndexOf("/") + 1);
				wrapper.find("img").attr("src", path + json.data);
				wrapper.find("input:hidden").val(json.data);
			}).fail(function() {
				alert("上传失败！上传大小不可超过5MB！");
			});
	});*/
	
	
})(jQuery);