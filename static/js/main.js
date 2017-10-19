function delCfm(url, msg){
	if(!msg){
		msg = '确定要删除这条记录么？';
	}
	if(!confirm(msg)){
		return false;
	}
	window.location.href = url;
}
var ioss_modal_url = new Array();
function ioss_url_modal(url, options){
	if(!options){
		options = {};
	}
	var modal_info = null;
	for(var i=0;i<ioss_modal_url.length;i++){
		if(ioss_modal_url[i].url == url){
			modal_info = ioss_modal_url[i];
			break;
		}
	}
	if(modal_info == null){
		var html = "";
		html += '<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">';
		if('big' in options){
			html += '	<div class="modal-dialog modal-lg"';
		}else{
			html += '	<div class="modal-dialog"';
		}
		if('width' in options){
			html += ' style="width:'+options.width+'px;"';
		}
		html += '>';
		html += '		<div class="modal-content" style="background:#f7f7f7"></div>';
		html += '	</div>';
		html += '</div>';
		modal_obj = $(html);
		modal_obj.appendTo($("body"));
		modal_info = {url:url, obj:modal_obj};
		ioss_modal_url[ioss_modal_url.length] = modal_info;
	}
	modal_info.obj.removeData();
	options.remote = modal_info.url;
	if(!('keyboard' in options)){options.keyboard = true;}
	if(!('backdrop' in options)){options.backdrop = 'static';}
	modal_info.obj.find('.modal-dialog').attr('shown_flg', '1');
	modal_info.obj.on('shown.bs.modal', function () {
		if(modal_info.obj.find('.modal-dialog').attr('shown_flg') != '1'){return;}
		modal_info.obj.find('.modal-dialog').attr('shown_flg', '2');
		if(('modal_init' in window) && typeof(window.modal_init) == 'function'){
			window.modal_init();
			delete window.modal_init;
		}
	});
	modal_info.obj.modal({
	      keyboard: true
	      , width: 900
	      , backdrop: 'static'
	      , remote: modal_info.url
	});
}
function ajaxLink(links, showid){
	$(links).bind('click', function(){
		var tmp = $.trim($(this).attr("href")).toLowerCase();
		if(tmp.indexOf("#") < 0 && tmp.indexOf("javascript") < 0){
			$.ajax({
				url: $.trim($(this).attr("href")), type: "GET",
				success: function(msg){
					$(showid).parent().html(msg);
				}
			});
		}
		return false;
	});
}
function refreshShow(obj){
	if(!obj){obj=$(document);}
	if(typeof(obj) == 'string'){obj=$(obj);}
	obj.find('input[type="checkbox"]').uniform();
	obj.find('input[type="radio"]').uniform();
	obj.find('.select2').select2({
        placeholder: "Select",
        allowClear: true
    });
}
/** 提交表单 */
function submitForm(url, data, method){
	var m = 'post';
	if(arguments.length >= 3 && method.toLowerCase() == 'get'){
		m = 'get';
	}
	if(window.submited){return false;}else{window.submited = true;}
	var form = $("<form action='"+url+"' method='"+m+"'></form>");
	form.css('display','none');
	for(var prop in data){
		if(data[prop].length < 1){continue;}
		var ipt = $("<input type='hidden' name='"+prop+"' />");
		ipt.val(data[prop]);
		form.append(ipt);
	}
	form.appendTo("body");
	form.submit();
	return false;
}
