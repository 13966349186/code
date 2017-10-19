<?php
Class IOSS_DB {
	private static $databases = array();

	
	static function getInstance($db = 'default'){
		if(!array_key_exists($db, self::$databases)){
			$config = IOSS_Conf::getConfig('db') ;
			if(!array_key_exists($db, $config)){
				return NULL;
			}
			$CI = &get_instance();
			self::$databases[$db] = $CI->load->database($config[$db],true);
		}
		return self::$databases [$db];
	}
}