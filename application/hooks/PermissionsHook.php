<?php
/**
 * 检查登录用户是否有当前页面的访问权限
 * @author zhangyk
 *
 */
class PermissionsHook{
	public function check(){
		$CI = &get_instance();
		if(is_a($CI, 'AdminController') && array_key_exists($CI->_thisMethod, $CI->_required)){
			$power_value = $CI->_required[$CI->_thisMethod];
			if(($CI->p->value & $power_value) != $power_value){
				show_error(l('user_has_nopower'));
			}
		}
	}
}