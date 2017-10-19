<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Types 测试
 */
class IOSS_Type_Test extends CI_Controller {

	function __construct(){
		parent::__construct();
		if(ENVIRONMENT == 'production' ){
			exit('enviroment is production');
		}
		$this->load->library('unit_test');
		$this->unit->use_strict(true);
	}
	
	function index(){
		//创建测试数据
		$db = IOSS_DB::getInstance();
		$t_id_1 = 52;
		$t_row_1 = array(
				'game_id'=>29,
				'code'=>'test-type',
				'name'=>'测试用类型',
				'model'=>'gold',
				'note'=>'这是一条测试数据',
				'state'=>1
		);
		$db->update('types',$t_row_1, array('id'=>$t_id_1));
		
		//开始测试
		$type1 = IOSS_Type::getType($t_id_1);

		$this->unit->run($type1->id, $t_id_1, ' IOSS_Game::getType',  "id = {$type1->id}");
		$this->unit->run($type1->game_id, $t_row_1['game_id'], ' IOSS_Game::game_id',  "{$type1->game_id}");
		$this->unit->run($type1->code, $t_row_1['code'], ' IOSS_Game::code',  "{$type1->code}");
		$this->unit->run($type1->name, $t_row_1['name'], ' IOSS_Game::name',  "{$type1->name}");
		$this->unit->run($type1->note, $t_row_1['note'], ' IOSS_Game::note',  "{$type1->note}");
		$this->unit->run($type1->state, $t_row_1['state'], ' IOSS_Game::state',  "{$type1->state}");
		$this->unit->run($type1->model, $t_row_1['model'], ' IOSS_Game::state',  "{$type1->model}");
		
		$order_attr = $type1->getAttributesDef();
		$this->unit->run($count = count($order_attr), 2, ' IOSS_Game::getAttributesDef()',  "实际结果 $count");
		 $order_object_attr = $type1->getOrderAttributesDef();
		 $this->unit->run($count = count($order_object_attr), 2, ' IOSS_Game::getOrderAttributesDef()',  "实际结果 $count");
		
		$list = IOSS_Type::getTypes($t_row_1['game_id']);
		$this->unit->run(array_key_exists($t_id_1, $list), TRUE , ' IOSS_Game::getTypes(game_id)',  "条件[game_id ={$t_row_1['game_id']}]， 列表中指定元素存在");
		
		//测试不存在的ID
		$type_null = IOSS_Type::getType(999999999);
		$this->unit->run($type_null, NULL, ' IOSS_Game::getType(9999999999999)',  "测试不存在的ID");
		echo $this->unit->report();
	}
}