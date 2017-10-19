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
 

if ( ! function_exists('l')){
	function l($string){
		$CI = & get_instance();
		$CI->load->library('lang');
		$CI->lang->load('vvwan');
		return $CI->lang->line($string);
	}
}

/* End of file permission_helper.php */
/* Location: SOMEFOLDERS/permission_helper.php */