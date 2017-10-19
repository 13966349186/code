<?php
class MY_Session{

	public function __construct(){
		session_start();
		if(!isset($_SESSION['flashdata'])){
			$_SESSION['flashdata'] = array();
		}
	}
	public function open(){
		session_start();
	}
	public function close(){
		session_write_close();
	}

	public function userdata($key = null){
		if($key == null){
			return $_SESSION;
		}
		if(!isset($_SESSION[$key])){
			return '';
		}
		return $_SESSION[$key];
	}

	public function set_userdata($key,$data){
		$_SESSION[$key] = $data;
	}
	
	public function unset_userdata($key,$data){
		unset($_SESSION[$key]);
	}

	public function sess_destroy(){
		session_destroy();
		unset($_SESSION);
	}
	public function all_userdata(){
		return $_SESSION;
	}

	public function set_flashdata($key,$data){
		$_SESSION['flashdata'][$key] = serialize($data);
	}

	public function flashdata($key){
		if(array_key_exists($key,$_SESSION['flashdata'])){
			$data = unserialize($_SESSION['flashdata'][$key]);
			unset($_SESSION['flashdata'][$key]);
			return $data;
		}else{
			return false;
		}
	}
}
