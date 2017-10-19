<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Router extends CI_Router {
	function __construct(){
		parent::__construct();
	}
	function set_directory($dir){
		$this->directory = str_replace(array('..'), '', $dir).'/';
	}
	/**
	 * Validates the supplied segments.  Attempts to determine the path to
	 * the controller.
	 *
	 * @access	private
	 * @param	array
	 * @return	array
	 */
	function _validate_request($segments)
	{
		$agr_count = count($segments);
		if ($agr_count == 0)
		{
			return $segments;
		}
		$method = 'index';
		$foundArr = false;
		$tailArr = '';
		if (file_exists(APPPATH.'controllers/'.implode('/', $segments).EXT)){
			//未指向方法，最终目标是控制器
			$foundArr = $segments;
		}else if ($agr_count > 1){
			$tmpArr = $segments;
			while(count($tmpArr) > 1){
				if(file_exists(APPPATH.'controllers/'.implode('/', array_slice($tmpArr, 0, count($tmpArr)-1)).EXT)){
					//指向了方法
					$method = $tmpArr[count($tmpArr)-1];
					$foundArr = array_slice($tmpArr, 0, count($tmpArr)-1);
					break;
				}
				$tailArr = '/'.$tmpArr[count($tmpArr)-1].$tailArr;
				$tmpArr = array_slice($tmpArr, 0, count($tmpArr)-1);
			}
		}
		if ($foundArr === false && file_exists(APPPATH.'controllers/'.implode('/', $segments).'/'.trim($this->default_controller, '/').EXT)){
			//默认控制器
			$foundArr = explode('/', implode('/', $segments).'/'.trim($this->default_controller, '/'));
		}
		if($foundArr === false){
			show_404(implode('/', $segments));
		}
		$agr_count = count($foundArr);
		$this->set_directory(implode('/', array_slice($foundArr, 0, $agr_count-1)));
		$this->set_class($foundArr[$agr_count-1]);
		$this->set_method($method);
		$foundArr = Array($foundArr[$agr_count-1], $method);
		if($tailArr){
			$foundArr = array_merge($foundArr, explode('/', substr($tailArr, 1)));
		}
		return $foundArr;
	}
}