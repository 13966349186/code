<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if(!function_exists('cms_form_input')){
	/**
	 * CMS模块，创建表单控件
	 * @param int $type 			控件类型
	 * @param string $name	控件名称
	 * @param array $config	控件配置
	 * @param string $value		控件默认值
	 */
	function cms_form_input($type, $field, $label, $config, $default=''){
		$html = '';
		if(!is_array($config)){
			$config = cms_parse_config($type, $config);
		}
		switch($type){
			case 1:  	//富文本编辑器
				$html = edit_form_textarea($field, set_value($field, $default), $label, 'id="'.$field.'"');
				$html .= '<script type="text/javascript" charset="utf-8"> $("#'.$field.'").parent(".col-xs-4").removeClass("col-xs-4").addClass("col-xs-7"); var editor_'.$field.' = new baidu.editor.ui.Editor(); editor_'.$field.'.render("'.$field.'"); </script>';
				break;
			case 2:		//时间
				$default = set_value($field,$default?$default:'');
				$html = edit_form_input($field, is_numeric($default)?date('Y-m-d H:i:s', $default):$default, $label, 'class="form-control datetime-picker" data-date-format="yyyy-mm-dd hh:ii:ss" ');
				break;
			case 3:		//日期
				$default = set_value($field,$default?$default:'');
				$html = edit_form_input($field, is_numeric($default)?date('Y-m-d', $default):$default, $label, 'class="form-control date-picker" data-date-format="yyyy-mm-dd" ');
				break;
			case 6:		//下拉框
				$html = edit_form_dropdown($field, array_key_exists('options', $config)?$config['options']:Array(), set_value($field, $default), $label);
				break;
			case 7:		//复选框
				$html = edit_form_checkbox($field, '1', set_checkbox($field, '1', $default == '1'), $label);
				break;
			case 8:		//单选框
				$html = edit_form_radio_list($field, (array_key_exists('options', $config)?$config['options']:Array()), set_value($field, $default), $label);
				break;
			case 9:		//文件
			case 10:		//图片
				$html = edit_form_uploader($field, set_uploader($field, $default), $label);
				break;
			case 11:			//多行文字
				$html = edit_form_textarea($field, set_value($field, $default), $label);
				break;
			case 4: 		//整数
			case 5:		//浮点数
			case 0: 		//文本
			default:
				$html = edit_form_input($field, set_value($field, $default), $label);
		}
		return $html;
	}
}
if(!function_exists('cms_list_display')){
	/**
	 * CMS模块，列表页显示
	 * @param int $type 			控件类型
	 * @param string $name	控件名称
	 * @param array $config	控件配置
	 * @param string $value		控件默认值
	 */
	function cms_list_display($type, $name, $config, $value){
		if(!is_array($config)){
			$config = cms_parse_config($type, $config);
		}
		switch($type){
			case 1:
				$rtn = $value;
				break;
			case 2:
				$rtn = date('Y-m-d H:i:s',$value);
				break;
			case 3:
				$rtn = date('Y-m-d',$value);
				break;
			case 6:
			case 7:
			case 8:
				$rtn = (array_key_exists('options', $config) && array_key_exists($value, $config['options']))?$config['options'][$value]:'';
				break;
			case 10:
				if(strlen($value) >= 4 && strtolower(substr($value, strlen($value)-4)) == '.swf'){
					$rtn = '<script language="javascript" type="text/javascript">';
					$rtn .= '	var style="style=\"width:100px;height:50px;\"";';
					$rtn .= '	var idxSwfUrl = "'.image_url($value).'";';
					$rtn .= '	if (window.navigator.appName == "Microsoft Internet Explorer") {';
					$rtn .= '		document.write("<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" "+style+">");';
					$rtn .= '		document.write("	<param name=\"movie\" value=\""+idxSwfUrl+"\" /> ");';
					$rtn .= '		document.write("	<param name=\"quality\" value=\"high\" /> ");';
					$rtn .= '		document.write("	<param name=\"wmode\" value=\"transparent\" /> ");';
					$rtn .= '		document.write("	<param name=\"allowScriptAccess\" value=\"always\" /> ");';
					$rtn .= '		document.write("	<param name=\"swfversion\" value=\"9.0.45.0\" />");';
					$rtn .= '		document.write("</object>");';
					$rtn .= '	} else {';
					$rtn .= '		document.write("<embed "+style+" wmode=\"transparent\" allowScriptAccess=\"always\" quality=\"high\" src=\""+idxSwfUrl+"\">");';
					$rtn .= '	}';
					$rtn .= '</script>';
				}else{
					$rtn = '<img src="' . image_url($value) . '" style="max-width:100px;max-height:50px;">';
				}
				break;
			default:
				$rtn = htmlspecialchars($value);
		}
		return $rtn;
	}
}
if(!function_exists('cms_parse_config')){
	/**
	 * 配置项解析
	 * @param integer $type 数据类型
	 * @param string $cfg_str 配置串
	 * @return Array 解析结果
	 */
	function cms_parse_config($type, $cfg_str){
		//$lines = explode("\n", str_replace("\r", "", $cfg_str));
		//为了方便数据库中直接编辑，字符串\r\n和\n会被当作换行符处理
		$lines = explode("\n", str_replace("\\n", "\n", str_replace("\r", "", str_replace("\\r", "", $cfg_str))));
		$configs = array();
		foreach($lines as $line){
			$line = trim($line);
			$c = explode(':',$line,2);
			if($c && count($c)==2 ){
				if($c[0] == 'options' || $c[0] =='upload'){
					$option = explode('=', $c[1],2);
					$configs[$c[0]][trim($option[0])] = trim($option[1]);
				}elseif($c[0] == 'default'){
					$configs[$c[0]] = $c[1];
				}elseif($c[0] == 'ci'){
					$key = $c[1];
					if(($idx = strpos($c[1], '[')) > 0 && substr($c[1], strlen($c[1])-1) == ']'){
						$key = substr($c[1], 0, $idx);
					}
					$configs[$c[0]][$key] = trim($c[1]);
				}else{
					$configs[$c[0]] = $c[1];
				}
			}
		}
		if(!array_key_exists('ci', $configs)){
			$configs['ci'] = array();
		}
		//设置默认配置
		switch($type){
			case 1:  	//富文本编辑器
				break;
			case 2:	//时间
			case 3:	//日期
				if(!array_key_exists('strtotime', $configs['ci'])){ //日期时间转为时间戳
					$configs['ci']['strtotime'] = 'strtotime';
				}
				break;
			case 6:		//下拉框
				if(array_key_exists('max_length', $configs['ci'])){
					$configs['ci']['max_length'] = 'max_length[32]';
				}
				break;
			case 7:		//复选框
				if(array_key_exists('max_length', $configs['ci'])){
					$configs['ci']['max_length'] = 'max_length[32]';
				}
				break;
			case 8:		//单选框
				if(array_key_exists('max_length', $configs['ci'])){
					$configs['ci']['max_length'] = 'max_length[32]';
				}
				break;
			case 9:		//文件
				if(!array_key_exists('max_size', $configs['ci'])){
					$configs['ci']['max_size']='max_size[10240]';
				}
				if(!array_key_exists('allowed_types', $configs['ci'])){
					$configs['ci']['allowed_types']='allowed_types[jpg,gif,png,txt,rar,zip,doc,docx,xls,xlsx,ppt,pptx,js,css,html,htm,swf]';
				}
				if(array_key_exists('max_length', $configs['ci'])){
					$configs['ci']['max_length'] = 'max_length[128]';
				}
				break;
			case 10:			//图片
				if(!array_key_exists('upload', $configs)){
					$configs['upload'] = Array();
				}
				$configs['upload']['encrypt_name']=true;
				if(!array_key_exists('allowed_types', $configs['ci'])){
					$configs['ci']['allowed_types']='allowed_types[jpg,gif,png]';
				}
				if(!array_key_exists('max_length', $configs['ci'])){
					$configs['ci']['max_length'] = 'max_length[128]';
				}
				break;
			case 4: 		//整数
				if(!in_array('integer', $configs['ci'])){
					$configs['ci']['integer'] = 'integer';
				}
				break;
			case 5:		//浮点数
				if(!in_array('numeric', $configs['ci'])){
					$configs['ci']['numeric'] = 'numeric';
				}
				break;
			case 0: 		//文本
				if(!array_key_exists('max_length', $configs['ci'])){
					$configs['ci']['max_length'] = 'max_length[32]';
				}
		}
		return $configs;
	}
}



