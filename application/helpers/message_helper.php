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
* Plural
*
* Takes a singular word and makes it plural (improved by stensi)
*
* @access	public
* @param	string
* @param	bool
* @return	str
*/
if ( ! function_exists('error')){
	function error($message){
		$CI = &get_instance();
		$msg = $CI->session->flashdata('flash_messages')?:array();
		$msg[] = array('type'=>'danger', 'text' =>$message);
		$CI->session->set_flashdata('flash_messages',$msg);
	}
}

if ( ! function_exists('success')){
	function success($message){
		$CI = &get_instance();
		$msg = $CI->session->flashdata('flash_messages')?:array();
		$msg[] = array('type'=>'success', 'text' =>$message);
		$CI->session->set_flashdata('flash_messages',$msg);
	}
}

if ( ! function_exists('successAndRedirect')){
	function successAndRedirect($message,$url=null){
		$CI = &get_instance();
		$msg = $CI->session->flashdata('flash_messages')?:array();
		$msg[] = array('type'=>'success', 'text' =>$message);
		$CI->session->set_flashdata('flash_messages',$msg);
		if($url == null)
			$url = $CI->_thisModule.$CI->_thisController.'/';
		redirect($url);
	}
}

if ( ! function_exists('errorAndRedirect')){
	function errorAndRedirect($message,$url=null){
		$CI = &get_instance();
		$msg = $CI->session->flashdata('flash_messages')?:array();
		$msg[] = array('type'=>'danger', 'text' =>$message);
		$CI->session->set_flashdata('flash_messages',$msg);
		if($url == null)
			$url = $CI->_thisModule.$CI->_thisController.'/';
		redirect($url);
	}
}

if ( ! function_exists('model_error')){
	/** 模态框错误页面 */
	function model_error($error=''){
		error($error);
		$CI = &get_instance();
		$CI->layout('modal_error.tpl', null, true);
		echo $CI->output->get_output();
		exit;
	}
}

if ( ! function_exists('model_success')){
	/** 模态框成功页面 */
	function model_success($info=''){
		success($info);
		$CI = &get_instance();
		$CI->layout('modal_success.tpl', null, true);
		echo $CI->output->get_output();
		exit;
	}
}

/* End of file message_helper.php */
/* Location: SOMEFOLDERS/message_helper.php */
