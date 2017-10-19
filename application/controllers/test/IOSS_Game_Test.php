<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Types 测试
 */
class IOSS_Game_Test extends CI_Controller {

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
		$t_id = 29;
		$t_row = array(
		 		'code'=>'unit_test',
		 		'name'=>'Test Game',
		  		'description'=>'unit test data',
		  		'sort'=>9,
		  		'state'=>1
		);
		$t_cat_id =96;
		$t_cat = array(
		 		'game_id'=>29,
		 		'parent_id'=>0,
		 		'code'=>'unit_test-cat',
		 		'name'=>'Test Game 目录',
		 		'description'=>'单元测试目录',
		 		'state'=>1
		);
		$db->update('game',$t_row, array('id'=>$t_id));
		$db->update('category',$t_cat, array('id'=>$t_cat_id));
		$game_count = $db->from('game')->count_all_results();

		//测试开始 =====================================================================================================================
		//id 不存在
		$this->unit->run(IOSS_Game::getGame(999999), NULL, ' IOSS_Game::getGame(id)  [id 不存在]',  "测试 getGame 方法id不存在，id = 999999");
		//id 存在
		$game = IOSS_Game::getGame($t_id);
		$this->unit->run($game->id, $t_id, ' IOSS_Game::getGame   [id 存在]',  "测试:getGame 方法，id = $t_id");
		$this->unit->run($game->code, $t_row['code'], ' IOSS_Game::code',  "");
		$this->unit->run($game->name, $t_row['name'], ' IOSS_Game::name',  "");
		$this->unit->run($game->description, $t_row['description'], ' IOSS_Game::description',  "");
		$this->unit->run($game->sort, $t_row['sort'], ' IOSS_Game::sort',  "");
		$this->unit->run($game->state, $t_row['state'], ' IOSS_Game::state',  "");
		
		$game = IOSS_Game::getGame($t_id); //第二次调用
		$this->unit->run($game->id, $t_id, ' IOSS_Game::getGame   [id 存在]',  "第二次调用 IOSS_Game::getGame 函数，id = $t_id");
		
		//返回游戏列表
		$games = IOSS_Game::getGames();
		$this->unit->run(count($games), $game_count, ' IOSS_Game::getGames()',  "测试：返回所有游戏列表, 共有 $game_count 个游戏");
		//
		$game2 = IOSS_Game::getGameByCode($t_row['code']);
		$this->unit->run($game2->id, $t_id, ' IOSS_Game::getGameByCode',  "code 为 {$t_row['code']} 游戏对应id为 {$game2->id}");
		
		$game2 = IOSS_Game::getGameByCode($t_row['code']);
		$this->unit->run($game2->id, $t_id, ' IOSS_Game::getGameByCode',  "第二次调用IOSS_Game::getGameByCode，  code 为 {$t_row['code']} 游戏对应id为 {$game2->id}");
		
		echo $this->unit->report();
	}
}