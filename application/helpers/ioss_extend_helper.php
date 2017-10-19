<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 根据数据模型找到对应的控制器地址
 * @param string $url
 * @param string $model
 * @param string $game_code
 * @param string $type_code
 * @return string
 */
function ioss_extend_route($url, $model, $game_code=null, $type_code=null){
	if (defined('ENVIRONMENT') AND is_file(APPPATH.'config/'.ENVIRONMENT.'/extend.php'))
	{
		include(APPPATH.'config/'.ENVIRONMENT.'/ioss_extend.php');
	}
	elseif (is_file(APPPATH.'config/ioss_extend.php'))
	{
		include(APPPATH.'config/ioss_extend.php');
	}
	$goto = '';
	$url = trim($url, '/');
	if($game_code && $type_code){
		$goto = isset($extend_game[$game_code][$type_code][$url])?$extend_game[$game_code][$type_code][$url]:'';
	}
	if(!$goto){
		$goto = isset($extend_model[$model][$url])?$extend_model[$model][$url]:'';
	}
	return $goto;
}