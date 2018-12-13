/**
 * 

 */
$(document).ready(function(){
		var data = {"jiangsu":{"value":"0.0%","index":"1","stateInitColor":"7"},"henan":{"value":"0.0%","index":"2","stateInitColor":"7"},"anhui":{"value":"0.0%","index":"3","stateInitColor":"7"},"zhejiang":{"value":"0.0%","index":"4","stateInitColor":"7"},"liaoning":{"value":"0.0%","index":"5","stateInitColor":"7"},"beijing":{"value":"0.0%","index":"6","stateInitColor":"7"},"hubei":{"value":"0.0%","index":"7","stateInitColor":"7"},"jilin":{"value":"0.0%","index":"8","stateInitColor":"7"},"shanghai":{"value":"0.0%","index":"9","stateInitColor":"7"},"guangxi":{"value":"0.0%","index":"10","stateInitColor":"7"},"sichuan":{"value":"0.0%","index":"11","stateInitColor":"7"},"guizhou":{"value":"0.0%","index":"12","stateInitColor":"7"},"hunan":{"value":"0.0%","index":"13","stateInitColor":"7"},"shandong":{"value":"0.0%","index":"14","stateInitColor":"7"},"guangdong":{"value":"0.0%","index":"15","stateInitColor":"7"},"jiangxi":{"value":"0.0%","index":"16","stateInitColor":"7"},"fujian":{"value":"0.0%","index":"17","stateInitColor":"7"},"yunnan":{"value":"0.0%","index":"18","stateInitColor":"7"},"hainan":{"value":"0.0%","index":"19","stateInitColor":"7"},"shanxi":{"value":"0.0%","index":"20","stateInitColor":"7"},"hebei":{"value":"0.0%","index":"21","stateInitColor":"7"},"neimongol":{"value":"0.0%","index":"22","stateInitColor":"7"},"tianjin":{"value":"0.0%","index":"23","stateInitColor":"7"},"gansu":{"value":"0.0%","index":"24","stateInitColor":"7"},"shaanxi":{"value":"0.0%","index":"25","stateInitColor":"7"},"macau":{"value":"0.0%","index":"26","stateInitColor":"7"},"hongkong":{"value":"0.0%","index":"27","stateInitColor":"7"},"taiwan":{"value":"0.0%","index":"28","stateInitColor":"7"},"qinghai":{"value":"0.0%","index":"29","stateInitColor":"7"},"xizang":{"value":"0.0%","index":"30","stateInitColor":"7"},"ningxia":{"value":"0.0%","index":"31","stateInitColor":"7"},"xinjiang":{"value":"0.0%","index":"32","stateInitColor":"7"},"heilongjiang":{"value":"0.0%","index":"33","stateInitColor":"7"},"chongqing":{"value":"0.0%","index":"34","stateInitColor":"7"}};
		$.post("../stat/stat.php?operate=areamap",function(response){
			var obj = eval(response);
			for(var k in data){
				for(var c in obj){
					if(ZnToEn(obj[c]['area']) == k){
						data[k].value = obj[c]['per'];
						if(parseFloat(obj[c]['per'].replace(/[^0-9]/ig,""))>10){
							data[k].stateInitColor = "0";
						}
						else if(parseFloat(obj[c]['per'].replace(/[^0-9]/ig,""))<10&&parseFloat(obj[c]['per'].replace(/[^0-9]/ig,""))>5){
							data[k].stateInitColor = "1";
						}
						else if(parseFloat(obj[c]['per'].replace(/[^0-9]/ig,""))<5&&parseFloat(obj[c]['per'].replace(/[^0-9]/ig,""))>1){
							data[k].stateInitColor = "2";
						}
						else if(parseFloat(obj[c]['per'].replace(/[^0-9]/ig,""))<2&&parseFloat(obj[c]['per'].replace(/[^0-9]/ig,""))>1){
							data[k].stateInitColor = "3";
						}
						else if(parseFloat(obj[c]['per'].replace(/[^0-9]/ig,""))<1&&parseFloat(obj[c]['per'].replace(/[^0-9]/ig,""))>0){
							data[k].stateInitColor = "4";
						}
					}
				}
			}
			var i = 1;
	        for(k in data){
	            if(i <= 12){
	                var _cls = "";//i < 4 ? 'active' : '';
	                $('#MapControl .list1').append('<li name="'+k+'"><div class="mapInfo"><i class="'+_cls+'">'+(i++)+'</i><span>'+chinaMapConfig.names[k]+'</span><b>'+data[k].value+'</b></div></li>')
	            }else if(i <= 24){
	                $('#MapControl .list2').append('<li name="'+k+'"><div class="mapInfo"><i>'+(i++)+'</i><span>'+chinaMapConfig.names[k]+'</span><b>'+data[k].value+'</b></div></li>')
	            }else{
	                $('#MapControl .list3').append('<li name="'+k+'"><div class="mapInfo"><i>'+(i++)+'</i><span>'+chinaMapConfig.names[k]+'</span><b>'+data[k].value+'</b></div></li>')
	            }
	        }
	 
	        var mapObj_1 = {};
	        var stateColorList = ['003399', '0058B0', '0071E1', '1C8DFF', '51A8FF', '82C0FF', 'AAD5FF'];
	         
	        $('#RegionMap').SVGMap({
	            external: mapObj_1,
	            mapName: 'china',
	            mapWidth: 350,
	            mapHeight: 350,
	            stateData: data,
	            // stateTipWidth: 118,
	            // stateTipHeight: 47,
	            // stateTipX: 2,
	            // stateTipY: 0,
	            stateTipHtml: function (mapData, obj) {
	                var _value = mapData[obj.id].value;
	                var _idx = mapData[obj.id].index;
	                var active = '';
	                //_idx < 4 ? active = 'active' : active = '';
	                var tipStr = '<div class="mapInfo"><i class="' + active + '">' + _idx + '</i><span>' + obj.name + '</span><b>' + _value + '</b></div>';
	                return tipStr;
	            }
	        });
	        $('#MapControl li').hover(function () {
	            var thisName = $(this).attr('name');
	            var thisHtml = $(this).html();
	            $('#MapControl li').removeClass('select');
	            $(this).addClass('select');
	            $(document.body).append('<div id="StateTip"></div');
	 
	            $('#StateTip').css({
	                left: $(mapObj_1[thisName].node).offset().left - 50,
	                top: $(mapObj_1[thisName].node).offset().top - 40
	            }).html(thisHtml).show();
	            mapObj_1[thisName].attr({
	                fill: '#E99A4D'
	            });
	        }, function () {
	            var thisName = $(this).attr('name');
	            $('#StateTip').remove();
	            var n = parseInt(data[$(this).attr('name')].stateInitColor)==7?6:parseInt(data[$(this).attr('name')].stateInitColor);
	            $('#MapControl li').removeClass('select');
	            mapObj_1[$(this).attr('name')].attr({fill: "#" + stateColorList[n]});
	        });
	         
	        $('#MapColor').show();
		});
});

function ZnToEn(name){
	var trans = "";
	switch(name){
		case "江苏":
			trans = "jiangsu";break;
		case "河南":
			trans = "henan";break;
		case "安徽":
			trans = "anhui";break;
		case "浙江":
			trans = "zhejiang";break;
		case "辽宁":
			trans = "liaoning";break;
		case "北京":
			trans = "beijing";break;
		case "湖北":
			trans = "hubei";break;
		case "吉林":
			trans = "jilin";break;
		case "上海":
			trans = "shanghai";break;
		case "广西":
			trans = "guangxi";break;
		case "四川":
			trans = "sichuan";break;
		case "贵州":
			trans = "guizhou";break;
		case "湖南":
			trans = "hunan";break;
		case "山东":
			trans = "shandong";break;
		case "广东":
			trans = "guangdong";break;
		case "江西":
			trans = "jiangxi";break;
		case "福建":
			trans = "fujian";break;
		case "云南":
			trans = "yunnan";break;
		case "海南":
			trans = "hainan";break;
		case "山西":
			trans = "shanxi";break;
		case "河北":
			trans = "hebei";break;
		case "内蒙古":
			trans = "neimongol";break;
		case "天津":
			trans = "tianjin";break;
		case "甘肃":
			trans = "gansu";break;
		case "陕西":
			trans = "shaanxi";break;
		case "澳门":
			trans = "macau";break;
		case "香港":
			trans = "hongkong";break;
		case "台湾":
			trans = "taiwan";break;
		case "青海":
			trans = "qinghai";break;
		case "西藏":
			trans = "xizang";break;
		case "宁夏":
			trans = "ningxia";break;
		case "新疆":
			trans = "xinjiang";break;
		case "黑龙江":
			trans = "heilongjiang";break;
		case "重庆":
			trans = "chongqing";break;
	}
	return trans;
}