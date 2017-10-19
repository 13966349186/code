<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Types 测试
 */
class IOSS_Currency_Test extends CI_Controller {

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
		 $t_row = array(
		 		'code'=>'RMB',
		 		'name'=>'人民币',
		  		'format'=>'¥ {num}',
		  		'exchange_rate'=>'6.2018'
		 );
		 $db->update('currency',$t_row, array('code'=>$t_row['code']));
		 $count = $db->from('currency')->count_all_results();

		 //测试
		 $this->unit->run(count(IOSS_Currency::getCurrency()), $count, ' IIOSS_Currency::getCurrency(code)  [code 为空]',  "测试 getCurrency 方法: code为空，应该返回所有货币配置，数量为 $count");
		 $this->unit->run(IOSS_Currency::getCurrency('XXX'), NULL, ' IIOSS_Currency::getCurrency(code)  [code 不存在]',  "测试 getCurrency 方法: code不存在，code = XXX");
		 $currency = IOSS_Currency::getCurrency($t_row['code']);
		 $this->unit->run($currency->code, $t_row['code'], ' IIOSS_Currency::getCurrency(code)  [code 存在]',  "测试 getCurrency 方法:  code = {$t_row['code']}");
		 $this->unit->run($currency->name, $t_row['name'], ' IIOSS_Currency::name',  "");
		 $this->unit->run($currency->format, $t_row['format'], ' IIOSS_Currency::format',  "");
		 $this->unit->run($currency->exchange_rate, $t_row['exchange_rate'], ' IIOSS_Currency::exchange_rate',  "");
		 
		 $this->unit->run($currency->format(999.12345678) === '¥ 999.12', TRUE, ' IIOSS_Currency::format',  "测试 format 方法，四舍五入保留两位小数");
		 $this->unit->run($currency->format(999.98765432) === '¥ 999.99', TRUE, ' IIOSS_Currency::format',  "测试 format 方法，四舍五入保留两位小数");
		 
		 $this->unit->run($currency->exchange(999.12345678) - 6196.36 <0.00001 , TRUE, ' IIOSS_Currency::format',  "测试 exchange 方法，四舍五入保留两位小数");
		 $this->unit->run($currency->exchange(999.1243) - 6196.37 <0.00001 , TRUE, ' IIOSS_Currency::format',  "测试 exchange 方法，四舍五入保留两位小数");
		 	
		 echo $this->unit->report();
	}
}