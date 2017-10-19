<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * IOSS Form Helpers
 * 表单辅助函数
 * @author	zhangyk
 */


/**
 * Text Input Field
 * 生成 input控件
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('search_form_input')){
	/**
	 * Text Input Field
	 *  生成下拉框控件
	 * @param string/array $data  域名称 或 关联数组
	 * @param string $value
	 * @param string $label
	 * @param string $extra
	 * @return string
	 */
	function search_form_input($data = '', $value = '', $label='', $extra=''){
		if(!is_array($data)){
			$data = array('name'=>$data);
		}
		if(!isset($data['placeholder']) &&  $label){
			$data['placeholder'] = $label;
		}
		if(!stristr($extra, 'class=')){
			$extra .= ' class="form-control input-medium"';
		}
		$html = '<div class="form-group" ><label class="sr-only">' . $label . '</label>' . form_input($data, $value, $extra) . '</div>';
		return $html;
	}
}

if ( ! function_exists('search_form_dropdown')){
	/**
	 * Drop-down Menu
	 * @param string $name
	 * @param array $options
	 * @param string $selected
	 * @param string $label
	 * @param string $extra
	 * @return string
	 */
	function search_form_dropdown($name = '', $options = array(), $selected = array(), $label = '', $extra='class="form-control input-medium"'){
		$options = parse_options($options);
		$select = form_dropdown($name,$options,$selected, $extra);
		return "<div class=\"form-group\"><label class=\"sr-only\">$label</label>$select</div>";
	}
}

if ( ! function_exists('search_form_checkbox')){
	/**
	 * 生成复选框控件
	 * @param string/array $data
	 * @param string $value
	 * @param string $label
	 * @param boolen $checked
	 * @param string $extra
	 * @return string
	 */
	function search_form_checkbox($data = '', $value = '', $label='', $checked = FALSE, $extra = ''){
		return  '<div class="form-group"><div class="checkbox btn btn-white btn-xs"><div class="checkbox"><label>'
		. form_checkbox($data,$value,$checked,$extra)
		. $label .  '</label></div></div></div>';
	}
}

if ( ! function_exists('search_form_radio')){
	/**
	 * 生成单选框控件
	 * @param string $name
	 * @param array $options
	 * @param string $selected
	 * @return string
	 */
	function search_form_radio($name = '', $options =array(), $selected='', $label = ''){
		$options = parse_options($options, $cfg);
		$html =  '<div class="form-group"><label class="sr-only">' . $label . '</label><div class="btn btn-white btn-xs"><div class="checkbox">'; 
		foreach ($options as $k=>$v){
			$html .= '<label>' . form_radio($name, $k, ($selected === (string)$k)) . $v .'</label>';
		}
		$html .= '</div></div>';
		return $html;
	}
}

/**
 * 编辑页面表单样式
 */
function edit_form_group_open($name = '', $label = ''){
	return  '<div class="form-group' . (form_error($name)?' has-error':'') . '">'
	. '<label class="col-xs-3 control-label">'. $label . '</label><div class="col-xs-4">';
}

function edit_form_group_close($name = ''){
	return form_error($name, '<p class="help-block">', '</p>') . '</div></div>';
}


/**
 * 编辑表单的输入框组
 * @param string/array $data
 * @param string $value
 * @param string $label
 * @param string/array $addons
 * @param string $extra
 * @return string 生成HTML
 */
function edit_form_input_group($data = '', $value = '',$label='', $addons= array(), $extra=''){
	$name = is_array($data)?$data['name']:$data;
	if(!stristr($extra, 'class=')){
		$extra .= ' class="form-control"';
	}
	if(is_array($addons)){
		$addon_front = $addons?$addons[0]:'';
		$addon_back = count($addons)>1?$addons[1]:'';
	}else{
		$addon_front = $addons;
		$addon_back = '';
	}
	$html = edit_form_group_open($name,$label)
	. '<div class="input-group">' . $addon_front
	. form_input($data, $value,$extra)
	 . $addon_back . '</div>'
	. edit_form_group_close($name);
	return $html;
}

/**
 * 编辑表单的文本框控件
 * @param string $data string/array $data  域名称 或 关联数组
 * @param string $value
 * @param string $label
 * @param string $extra 扩展属性
 * @return string 生成HTML
 */
function edit_form_input($data = '', $value = '',$label='',  $extra = ''){
	$name = is_array($data)?$data['name']:$data;
	if(!stristr($extra, 'class=')){
		$extra .= ' class="form-control"';
	}
	$html = edit_form_group_open($name,$label) 
	. form_input($data, $value, $extra)
	. edit_form_group_close($name);
	return $html;
}

/**
 * 下拉框控件
 * @param string $name
 * @param array $options
 * @param string $selected
 * @param string $label
 * @param string $extra
 * @return string 生成HTML
 */
function edit_form_dropdown($name = '', $options = array(), $selected = array(), $label='', $extra = ''){
	if(!stristr($extra, 'class=')){
		$extra .= ' class="form-control"';
	}
	return edit_form_group_open($name,$label)
	. form_dropdown($name , $options , $selected, $extra)
	. edit_form_group_close($name);
}

/**
 * 单选框组控件
 * @param string $name
 * @param array $options
 * @param string $selected
 * @param string $label
 * @param string $extra
 */
function edit_form_radio_list($name = '', $options = array(), $selected = '', $label='', $extra = ''){
	$html = '<div class="radio-list">';
	foreach ($options as $k=>$v){
		$html .= '<label class="radio-inline"><span>';
		$html .= form_radio($name,$k,  $k == $selected, $extra);
		$html .= '</span>' . htmlspecialchars($v) . '</label>';
	}
	$html .= '</div>';
	return edit_form_group_open($name,$label) . $html . edit_form_group_close($name);
}

/**
 * 复选框控件
 * @param string $data
 * @param string $value
 * @param boolen $checked
 * @param string $label
 * @param string $extra
 * @return string 生成的HTML
 */
function edit_form_checkbox($data = '', $value = '', $checked = FALSE, $label='', $extra = ''){
	$name = is_array($data)?$data['name']:$data;
	$html = '<label class="checkbox-inline" style="padding-left:0px;"><span>';
	$html .= form_checkbox($data,$value,$checked,$extra);
	$html .= '</span>' . htmlspecialchars($label) . '</label>';
	return edit_form_group_open($name,$label) . $html . edit_form_group_close($name);
}

/**
 * 编辑表单的textarea控件
 * @param string $name
 * @param string $value
 * @param string $label
 * @param string $extra
 * @return  string 生成的HTML
 */
function edit_form_textarea($name = '', $value = '',$label='',  $extra = 'class="form-control"'){
	if(!is_array($name)){
		$date['name'] = $name;
		$date['value'] = $value;
		$date['rows'] = '3';		
	}else{
		$date = $name;
	}
	$html = edit_form_group_open($name,$label);
	$html .= form_textarea($date , $value , $extra);
	$html .= edit_form_group_close($name);
	return $html;
}

/**
 * 编辑表单的ajaxuploader控件
 * @param string $name
 * @param string $value
 * @param string $label
 * @return string 生成的HTML
 */
function edit_form_uploader($name = '', $value = '', $label=''){
	$value = htmlspecialchars($value);
	return  edit_form_group_open($name,$label) 	 . "<div id=\"$name\" class=\"ajax-uploader\"><input type=\"hidden\"  value=\"$value\"  name=\"$name\"></div>" .  edit_form_group_close($name);
}
function edit_form_uploader_mult($name = '', $value = '', $label=''){
	$value =is_string($value)?explode('|', $value):$value;
	$html = '';
	foreach ($value as $v){
		$v = htmlspecialchars($v);
		$html .= "<input type=\"hidden\"  value=\"$v\"  name=\"{$name}[]\">";
	}
	return  edit_form_group_open($name,$label) 	. "<div id=\"$name\" class=\"ajax-uploader mult\">$html</div>" . edit_form_group_close($name);
}

/**
 * 设置文件上传控件的值
 * @param string $field
 * @param string $default
 * @return string|Ambigous <string, unknown, mixed>
 */
function set_uploader($field = '', $default = ''){
	if ( ! isset($_POST[$field]))
	{
		return $default;
	}
	return form_prep($_POST[$field], $field);
}

/**
 * 生成编辑页面的文本显示项
 * @param string $label 标签名
 * @param string $value 显示内容
 * @return string
 */
function edit_form_static($label, $value, $parse_html=true){
	$html = '<div class="form-group">';
	$html .= '	<label class="col-xs-3 control-label">'.$label.'</label>';
	$html .= '	<div class="col-xs-4">';
	$html .= '		<p class="form-control-static"><strong>'.($parse_html?htmlspecialchars($value):$value).'</strong></p>';
	$html .= '	</div>';
	$html .= '</div>';
	return $html;
}


if ( ! function_exists('parse_options')){
	/**
	 * 将键值对数组，对象数组的列表，转换成单一的键值对数组，以便生成下拉框的options
	 * @param Array $options 对象数组，或者混合了键值对数组
	 * @param 键值对数组 $cfg 选项(数组元素是对象时，转换成值，指定属性名)
	 * <br> 键名含意如下：
	 * <br>	key: 值是字符串，生成下拉框的option的value值用到的属性名(和val要同时使用)
	 * <br> val: 值是字符串，生成下拉框的option的html显示内容用到的属性名(和key要同时使用)
	 * <br> 如果配置了key和val，则表示参数$options数组中的元素是对象，这两个配置项的值，表示的是$options数组中对象的属性名
	 * <br> 如果没有配置key和val，则表示参数$options数组，是键值对数组，键和值都是字符串，直接用来生成下拉框的option列表
	 * <br> 没有配置key和val时，元素是对象，则键默认取id(key,code)属性依次试着取，值则是取name属性
	 */
	function parse_options($options, $key_name="id", $value_name="name"){
		$rtn = Array();
		foreach ($options as $k=>$v){
			if(is_object($v)){
				$rtn[$v->{$key_name}] = $v->{$value_name};
			}elseif( is_array($v)){
				$rtn[$v[$key_name]] = $v[$value_name];
			}else{
				$rtn[$k]=$v;
			}
		}
		return $rtn;
	}
}

/**
 * 生成编辑页面Form标题
 * @param string $caption 标题
 * @param string $icon 图标
 * @return string
 */
function edit_form_caption($caption, $icon='<i class="fa  fa-cogs"></i>'){
	$html = '<div class="portlet-title">';
	$html .= '	<div class="caption">';
	$html .= '		'.$icon.' '.$caption;
	$html .= '	</div>';
	$html .= '</div>';
	return $html;
}

if ( ! function_exists('parse_js')){
	function parse_js($str){
		$str = str_replace('\\', '\\\\', $str);
		$str = str_replace('"', '\\"', $str);
		$str = str_replace("'", "\\'", $str);
		$str = str_replace("\t", "\\t", $str);
		$str = str_replace("\r", "\\r", $str);
		$str = str_replace("\n", "\\n", $str);
		$str = str_replace("\f", "\\f", $str);
		$str = str_replace("\b", "\\b", $str);
		return $str;
	}
}

if ( ! function_exists('float_compare')){
	/**
	 * 浮点数比较
	 * @param string $valueA 比较A值
	 * @param string $valueB 比较B值
	 * @return integer $valueA > $valueB返回1，$valueA < $valueB返回-1，$valueA==$valueB返回0
	 */
	function float_compare($valueA, $valueB){
		$valueA -= $valueB;
		if($valueA < -0.00001){
			return -1;
		}else if($valueA > 0.00001){
			return 1;
		}
		return 0;
	}
}

if ( ! function_exists('is_https'))
{
	/**
	 * Is HTTPS?
	 *
	 * Determines if the application is accessed via an encrypted
	 * (HTTPS) connection.
	 *
	 * @return	bool
	 */
	function is_https()
	{
		if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
		{
			return TRUE;
		}
		elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
		{
			return TRUE;
		}
		elseif ( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
		{
			return TRUE;
		}

		return FALSE;
	}
}

if ( ! function_exists('image_url')){
	/**
	 * 生成图片引用url
	 * @param string $uri 图片路径
	 * @return string 图片地址
	 */
	function image_url($uri = ''){
		$CI = &get_instance();
		$config_key = is_https()?'image_url_ssl':'image_url';
		$base_url = $CI->config->item($config_key)?:'/';
		return $base_url . ltrim($uri,'/');
	}
}

