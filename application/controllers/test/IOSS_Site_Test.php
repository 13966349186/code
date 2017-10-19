<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Types 测试
 */
class IOSS_Site_Test extends CI_Controller {

	function __construct(){
		parent::__construct();
		if(ENVIRONMENT == 'production' ){
			exit('enviroment is production');
		}
		$this->load->library('unit_test');
	}
	
	function index(){
		 $db = IOSS_DB::getInstance();
		 //创建测试数据
		 $t_id = 1;
		 $t_row = array(
		 		'code'=>'fifa1',
		 		'name'=>'fifa1.com',
		 		'domain'=>'www.fifa1.com',
		  		'state'=>1
		 );
		 $t_row_config = array(
		 		'site_id'=>$t_id,
		 		'config_key'=>'aboutUs',
		 		'value'=>'关于我们'
		  );
		 $db->update('site',$t_row, array('id'=>$t_id));
		 $db->update('site_config',$t_row_config, array('site_id'=>$t_id,'config_key'=>$t_row_config['config_key']));
		 $config_count =  $db->from('site_config')->where('site_id',$t_id)->count_all_results();
		 
		 //测试不存在的ID
		 $this->unit->run(IOSS_Site::getSite(99999), NULL, ' IOSS_Game::getGame (id不存在)',  "测试不存在的ID: 99999");
		 //测试存在的ID
		 $site = IOSS_Site::getSite($t_id);
		 $this->unit->run($site->id, $t_id, ' IOSS_Game::getGame  (id存在)',  "测试存在的ID: $t_id");
		 $this->unit->run($site->code, $t_row['code'], ' IOSS_Game::code',  "");
		 $this->unit->run($site->name, $t_row['name'], ' IOSS_Game::name',  "");
		 $this->unit->run($site->domain, $t_row['domain'], ' IOSS_Game::domain',  "");
		 $this->unit->run($site->state, $t_row['state'], ' IOSS_Game::state',  "");
		 //测试getSiteByCode方法
		 $this->unit->run(IOSS_Site::getSiteByCode($t_row['code'])->id, $t_id, ' IOSS_Game::getSiteByCode',  "");
		 //测试getConfig
		 $this->unit->run($v = $site->getConfig($t_row_config['config_key']), $t_row_config['value'], ' IOSS_Game::getConfig  (配置存在)',  '测试存在配置项：' . $t_row_config['config_key'] . ' = ' . $v);
		 $this->unit->run($v = $site->getConfig('sssss aaaa'), NULL, ' IOSS_Game::getConfig   (配置不存在)',  '测试不存在配置项：' . $t_row_config['config_key'] . ' = NULL' );
		 $this->unit->run($v = count($site->getConfig()), $config_count, ' IOSS_Game::getConfig',  "所有配置共： $v 项");
		 	
		 
		 
		echo $this->unit->report();
	}
}