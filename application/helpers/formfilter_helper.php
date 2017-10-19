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
/**
 * 列表过滤器填充表单的函数
*/
if ( ! function_exists('filterValue')){
	function filterValue($key){
		$CI = &get_instance();
		return trim($CI->formfilter->getFilterValue($key));
	}
}
/* End of file formPlus_helper.php */
/* Location: SOMEFOLDERS/formPlus_helper.php */
