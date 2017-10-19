<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
 * 核心模块-缓存清理
 */
 class cache extends AdminController {
	public $IOSS_GameCache=null;  //memcached 实例
	public $isConnect=null;  //memcached 是否能够连接上
	
 	function __construct(){
 		parent::__construct();
 		$this->IOSS_GameCache = IOSS_GameCache::getInstance();
 		$this->isConnect=$this->IOSS_GameCache->isConnect;
 	}
 	 	
 	/**
 	 *  清除 缓存
 	 * 
 	 */
 	function clearCache(){ 		
 		if(!$this->p->add){
			//权限不足
			show_error(l('user_has_nopower'));
		}
 		if($this->isConnect){
 			$clearStatus=$this->IOSS_GameCache->flush(); 		
	 		if($clearStatus){
	 			$infoMsg='缓存清理成功！';
	 		}else{
	 			$infoMsg='警告 : 缓存清理失败！';
	 		}
 		}else{
 			$infoMsg='警告 : Memcached 连接不上！';
 		}
 		$this->assign('infoMsg',$infoMsg);
 		$this->layout('info');
 	}
  	
	
 	 	
 	function index(){ 		
 		if(!$this->p->read){
			//权限不足
			show_error(l('user_has_nopower'));
		}
		if($this->isConnect){
			$onlineStatus=TRUE;
			$cacheVersion=$this->IOSS_GameCache->getVersion();
			$cacheStates=$this->IOSS_GameCache->getStates();
		}else{
			$onlineStatus=FALSE;
			$cacheVersion='';
			$cacheStates=array();
		}
				
 		$this->assign('onlineStatus',$onlineStatus);
 		$this->assign('cacheVersion',$cacheVersion);
 		$this->assign('cacheStates',$cacheStates);
 		$this->layout();
 	}

 }
