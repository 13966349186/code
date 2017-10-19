<?php
/**
 * 日志记录功能
 * @author zhangyk
 *
 */
class IOSS_Logger{
	const IPN_LOG='IPN';
	const IPN_MSG='IPN_MSG_';
	const MESSAGE_LOG='MESSAGE';
		
	private static $instance;
	private $timezone = 8; //beijing
	private $log_file_path;
	private $log_device;	//Rsyslog远程同步是默认配置了local6，一般不需要改动
	private $level_debug;
	public $display;
	
	private function  __construct(){
		//$this->log_device = defined('LOG_LOCAL6')?LOG_LOCAL6:0;
		$this->log_device = 0;
		$this->log_file_path = IOSS_Conf::getLogPath();
		$this->level_debug = false;
		$this->display = false;
	}
	
	/**
	 * 创建 IOSS_Logger 实例
	 * @return IOSS_Logger
	 */

	public static function getIntance(){
		if(self::$instance == null)
		{
			self::$instance = new IOSS_Logger();
		}
		return self::$instance;
	}

	/**
	 * 错误日志记录方法，按照日期分文件
	 * @param string $message
	 * @return bool
	 */
	public  function errorMessage($message){
		$ident = __FUNCTION__;
		return $this->_message($ident,$message);//by add guangs 2013-05-20
	}
	
	/**
	 * 记录Debug日志，可根据配置关闭
	 * @param unknown_type $message
	 * @throws Exception
	 * @return bool
	 */
	public  function debugMessage($message){
		if(!$this->level_debug){
			return false;
		}
		$ident = __FUNCTION__;
		return $this->_message($ident,$message);//by add guangs 2013-05-20
	}
	

	/**
	 * 通用日志记录方法
	 * @param string $file_name
	 * @param string $message
	 * @return bool
	 */
	public  function message($message,$log_name=self::MESSAGE_LOG){
		$ident = $log_name;
		return $this->_message($ident,$message);//by add guangs 2013-05-20
	}

	/**
	 * 通用日志记录
	 * @param string $message 消息信息
	 * @param string $ident 消息标识,用来区分消息类型
	 * @return bool	日志写入成功返回true，否则返回false
	 * @author guangs
	 */
	 private function _message($ident,$message){
		if(empty($ident)){
			return false;
		}
		$time = time() + $this->timezone * 3600;
		$message = gmdate("Y-m-d H:i:s", $time).' '.$message;
		if($this->display){
			echo $ident . ' // ' . $message . " <br/> \r";
			return true;
		}
		if($this->log_device){
			if(@openlog($ident, 0,$this->log_device)){	//
				if(@syslog(LOG_INFO, $message)){
					@closelog();
					return true;
				}
			}
		}else{
			$file_name = $ident . gmdate('Ymd', $time). '.log';
			$fh = fopen($this->log_file_path . $file_name , 'a');
			if($fh){
				fwrite($fh, $message . "\r\n");
				fclose($fh);
				return true;
			}
		}
		return false;
	 }
}
