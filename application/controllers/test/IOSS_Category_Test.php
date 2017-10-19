<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Category 测试
 */
class IOSS_Category_Test extends CI_Controller {

	function __construct(){
		parent::__construct();
		if(ENVIRONMENT == 'production' ){
			exit('enviroment is production');
		}
		$this->load->library('unit_test');
		$this->unit->use_strict(true);
	}
	
	function index(){
		 $db = IOSS_DB::getInstance();
		 //创建测试数据
		 $game_id  =29;
		 $t_id_1 = 96;
		 $t_row_1 = array(
		 		'game_id'=>$game_id,
		 		'parent_id'=>0,
		  		'code'=>'test-code-1',
		  		'name'=>'测试目录-1',
		  		'description'=>'测试父目录的描述',
		 		'state'=>1
		 );
		 $t_id_2 = 97;
		 $t_row_2 = array(
		 		'game_id'=>$game_id,
		 		'parent_id'=>$t_id_1,
		 		'code'=>'test-code-1-1',
		 		'name'=>'测试目录-1-1',
		 		'description'=>'测试子目录1的描述',
		 		'state'=>1
		 );
		 $t_id_3 = 98;
		 $t_row_3 = array(
		 		'game_id'=>$game_id,
		 		'parent_id'=>$t_id_1,
		 		'code'=>'test-code-1-2',
		 		'name'=>'测试目录-1-2',
		 		'description'=>'测试子目录2的描述',
		 		'state'=>1
		 );
		 $db->update('category',$t_row_1, array('id'=>$t_id_1));
		 $db->update('category',$t_row_2, array('id'=>$t_id_2));
		 $db->update('category',$t_row_3, array('id'=>$t_id_3));
		 $count1 = $db->from('category')->where('game_id',$game_id)->where('parent_id',0)->count_all_results();
		 $count2 = $db->from('category')->where('game_id',$game_id)->count_all_results();
		 $count3 = $db->from('category')->where('game_id',$game_id)->where('parent_id',$t_id_1)->count_all_results();
		 //测试
		 $c1 = IOSS_Category::getCategory($t_id_1);
		 $c2 = IOSS_Category::getCategory($t_id_2);
		 	
		 $this->unit->run($c1->id, $t_id_1, ' IOSS_Game::getCategory',  "");
		 $this->unit->run($c1->game_id, $t_row_1['game_id'], ' IOSS_Game::game_id',  "");
		 $this->unit->run($c1->parent_id, $t_row_1['parent_id'], ' IOSS_Game::parrent_id',  "");
		 $this->unit->run($c1->code, $t_row_1['code'], ' IOSS_Game::code',  "");
		 $this->unit->run($c1->name, $t_row_1['name'], ' IOSS_Game::name',  "");
		 $this->unit->run($c1->description, $t_row_1['description'], ' IOSS_Game::description',  "");
		 $this->unit->run($c1->state, $t_row_1['state'], ' IOSS_Game::state',  "");
		 //
		 $this->unit->run(IOSS_Category::getCategoryByCode($game_id, $t_row_1['code'])->id, $t_id_1, ' IOSS_Game::getCategoryByCode',  "");
		 //
		 $this->unit->run(count(IOSS_Category::getCategories($game_id)), $count1, ' IOSS_Game::getCategories',  "预期结果 $count1");
		 $this->unit->run(count(IOSS_Category::getCategories($game_id ,true)), $count2, ' IOSS_Game::getCategories',  "预期结果 $count2");
		 
		 $this->unit->run($c2->getParent()->id, $t_id_1, ' IOSS_Game::getParent',  "");
		 $this->unit->run(count($c1->getChildren()), $count3, ' IOSS_Game::getChildren',  "预期结果 $count3");
		 $this->unit->run(count($c2->getChildren()), 0, ' IOSS_Game::getChildren',  "预期结果 0");
		 	
		 echo $this->unit->report();
	}
}