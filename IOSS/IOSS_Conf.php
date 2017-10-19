<?php
Class IOSS_Conf {
	private static $_debugOnPage = 1;//异常是否抛到画面上显示
	private static $_configFileName = "Config.php";//配置文件的名字

	private static $_baseCfg = null;

	/** 保存Cookie时的前辍 */
	static function getCookiePre(){return "";}
	/** Cookie的路径参数 */
	static function getCookiePath(){return "/";}
	/** Cookie的保存时长(秒数) */
	static function getCookieTime(){return 2592000;}
	/** 是否开启调试状态 */
	static function getDebugFlg(){return self::$_debugOnPage;}
	/** 取基础配置 */
	static function getConfig($key){
		if(self::$_baseCfg == null){
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.self::$_configFileName;
			self::$_baseCfg = $IOSS_Config;
		}
		if(array_key_exists($key, self::$_baseCfg)){
			return self::$_baseCfg[$key];
		}
		return null;
	}

	/** 日志存放目录 配置 */
	static function getLogPath(){
		$path = self::getConfig('log_path');
		if($path){
			return $path;
		}else{
			return dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR."application".DIRECTORY_SEPARATOR."logs".DIRECTORY_SEPARATOR;		
		}
	}
}
