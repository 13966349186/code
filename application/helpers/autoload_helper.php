<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --------------------------------
 *  Edit description here......
 * --------------------------------
 * @license		MIT License
 * @package
 * @category    
 * @author		Niap(niap.pr@gmail.com)
 * @link		git://github.com/Niap/niap_cms.git
 * @version		1.0.0
 */
if(!function_exists('__autoload')){
	function __autoload($className){
		global $_AutoLoadDirInfo;
		$LimitNum = 200;
		if(!isset($_AutoLoadDirInfo) || !$_AutoLoadDirInfo){
			//遍历IOSS目录下的所有目录（包括IOSS目录本身）保存到数组中
			$_AutoLoadDirInfo = Array(FCPATH.'IOSS'.DIRECTORY_SEPARATOR);
			$searchList = Array(FCPATH.'IOSS'.DIRECTORY_SEPARATOR);
			while($searchList && count($_AutoLoadDirInfo) < $LimitNum){
				$tmpList = Array();
				foreach ($searchList as $d){
					if($df = @opendir($d)){
						while (($file = @readdir($df)) !== false && count($_AutoLoadDirInfo) < $LimitNum){
							if($file != '.' && $file != '..' && is_dir($d.$file) && !in_array($d.$file.DIRECTORY_SEPARATOR, $_AutoLoadDirInfo)){
								$tmpList[] = $d.$file.DIRECTORY_SEPARATOR;
								$_AutoLoadDirInfo[] = $d.$file.DIRECTORY_SEPARATOR;
							}
						}
					}
				}
				$searchList = $tmpList;
			}
		}
		if(strpos($className,'IOSS_') === 0){
			foreach ($_AutoLoadDirInfo as $d){
				if(is_file($d.$className.".php")){
					require_once $d.$className.".php";
				}
			}
		}
	}
}
