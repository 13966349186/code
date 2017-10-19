/**
 * 初始化Ajax联动下拉框
 * 调用示例：
	//URL请求方式
	$("[name='game_id']").RelativeDropdown({
		childid: "[name='type_id']",
		url: "<?=site_url('common/Ajax/GetTypes')?>/{0}",
		empty_option: '请选择类型...',
		init_child_val: "<?=filterValue('type_id')?>"
	});
	$("[name='game_id']").RelativeDropdown({
		childid: "[name='category_id']",
		url: "<?=site_url('common/Ajax/GetCategorys')?>/{0}",
		empty_option: '请选择目录...',
		init_child_val: "<?=filterValue('category_id')?>"
	});
	//静态数组绑定
	$("[name='testa']").RelativeDropdown({
		childid: "[name='testb']",
		empty_option: '请选择testb...',
		static_data: {'1':[{id:'id11', name:'testb11'}, {id:'id12', name:'testb12'}], 2:[{id:'id21', name:'testb21'}, {id:'id22', name:'testb22'}]},
	});
 */
(function($){
	var defaults = {
		key_name: 'id'//子下拉框的option的value取值的属性名
		,val_name: 'name'//子下拉框的option的显示名称取值的属性名
		//,init_child_val:null//初始化子下拉框的页面回显值
		//,empty_option: null//子下拉框的固定第一项，三种格式，第1种：不传或者传null表示不生成固定项；第2种：empty_option: {id:'0', name:'请选择...'}；第3种：empty_option: '请选择...'
		//,list_name: ''//url返回的对象中列表数组的属性名，默认值为空，表示url返回的对象本身是就是列表数组
		//,static_data: {'1':[{id:'id11', name:'name11'}, {id:'id12', name:'name12'}], 2:[{id:'id21', name:'name21'}, {id:'id22', name:'name22'}]}
		//,extern_attr: [xxx,yyy]//从数组对象中读取属性xxx和yyy，以名称data-xxx, data-yyy作为option的属性
		//  添加事件 
		//,onInit 首次刷新事件
	};
	var cache = {};
	var fathers = [];
	$.fn.extend({
		RelativeDropdown :(function(cfg){
			child_objs = ('childid' in cfg) ? $(cfg.childid) : $(this);
			father_dom = ('fatherid' in cfg) ? $(cfg.fatherid)[0] : $(this)[0];
			if(!(curr_father = findInArr(father_dom, fathers))){
				fathers[fathers.length] = {dom: father_dom, children: [], curr_val: null};
				curr_father = fathers[fathers.length-1];
				binds(curr_father);
			}
			var children = curr_father.children;
			for(var i=0;i<child_objs.length;i++){
				var tmp = {dom: child_objs[i], curr_url: '', cfg: $.extend({}, defaults, cfg, {inited:false})};
				if(curr_child = findInArr(child_objs[i], children)){
					$.extend(curr_child, tmp);
				}else{
					children[children.length] = curr_child = tmp;
				}
			}
			if(('url' in cfg) && !(cfg.url in cache)){
				cache[cfg.url] = {};
			}
			changeDo(curr_father);
		})
	});
	function binds(father){
		$(father.dom).change(function(){
			changeDo(father);
		});
	}
	function changeDo(father){
		father.curr_val = $(father.dom).find("option:selected").attr('value');
		children = father.children;
		var req_url = {};
		for(var i=0;i<children.length;i++){
			if('static_data' in children[i].cfg){
				if(father.curr_val in children[i].cfg.static_data){
					refreshList(children[i], children[i].cfg.static_data[father.curr_val]);
				}else{
					refreshList(children[i], null);
				}
				continue;
			}
			if(father.curr_val.length > 0){
				children[i].curr_url = children[i].cfg.url.replace('{0}', father.curr_val);
			}else{
				children[i].curr_url = '';
			}
			curr_cache = cache[children[i].cfg.url];
			if(children[i].curr_url.length < 1){
				refreshList(children[i], null);
			}else if((children[i].curr_url in curr_cache) && curr_cache[children[i].curr_url].done){
				refreshList(children[i], curr_cache[children[i].curr_url].msg);
			}else{
				req_url[children[i].curr_url] = children[i].cfg.url;
			}
		}
		for(curr_url in req_url){
			url = req_url[curr_url];
			if((curr_url in cache[url])){
				continue;
			}
			cache[url][curr_url] = {done: false, msg: null};
			doAjax(curr_url, url);
		}
	}
	function doAjax(curr_url, url){
		$.ajax({
			url: curr_url,
			type: "POST",
			dataType: 'json',
			success: function(msg){
				cache[url][curr_url].msg = msg;
				cache[url][curr_url].done = true;
				for(var i=0;i<fathers.length;i++){
					for(var j=0;j<fathers[i].children.length;j++){
						if(fathers[i].children[j].curr_url == curr_url){
							fathers[i].children[j].curr_url = '';
							refreshList(fathers[i].children[j], msg);
						}
					}
				}
			}
		});
	}
	function refreshList(child, msg){
		var content = "";
		var cfg = child.cfg;
		if('empty_option' in cfg){
			if(typeof(cfg.empty_option) == 'string'){
				content = "<option value=\"\">"+cfg.empty_option+"</option>";
			}else{
				content = "<option value=\""+cfg.empty_option.id+"\">"+cfg.empty_option.name+"</option>";
			}
		}
		if(msg){
			var data = [];
			if(('list_name' in cfg) && (cfg.list_name in msg)){
				data = msg[cfg.list_name];
			}else if(toString.apply(msg) === '[object Array]'){
				data = msg;
			}
			//拼接下拉框的内容
			for(var i=0;i<data.length;i++){
				content += "<option value=\""+data[i][cfg.key_name]+"\"";
				if(!cfg.inited && ('init_child_val' in cfg) && cfg.init_child_val == data[i][cfg.key_name]){
					content += " selected=\"selected\"";
				}
				if('extern_attr' in cfg){
					if(typeof(cfg.extern_attr) == 'string'){
						cfg.extern_attr = [cfg.extern_attr];
					}
					for(var k=0;k<cfg.extern_attr.length;k++){
						content += " data-"+cfg.extern_attr[k]+"=\""+data[i][cfg.extern_attr[k]]+"\"";
					}
				}
				content += ">"+data[i][cfg.val_name]+"</option>";
			}
		}
		$(child.dom).html(content);
		if(!cfg.inited && ('onInit' in cfg) && typeof(cfg.onInit) == 'function'){
			cfg.onInit($(child.dom));
		}
		$(child.dom).change();
		cfg.inited = true;
	}
	function findInArr(dom, arr){
		for(i=0;i<arr.length;i++){
			if(arr[i].dom == dom){
				return arr[i];
			}
		}
		return false;
	}
})(jQuery);
