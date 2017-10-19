<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 共用模块-Ajax调用
 */
class Ajax extends AdminController {
	function __construct(){
		parent::__construct();
	}
	/**
	 * 根据游戏ID取游戏下的目录
	 * @param integer $game_id 游戏标识
	 */
	function GetCategorys($game_id){
		$this->load->model('MCategory');
		$rtn = Array();
		$where = Array('game_id'=>$game_id);
		
		if($lst = $this->MCategory->getAll($where, 'name')){
			foreach ($lst as $v){
				$index[$v->id] = $v->parent_id;
				$names[$v->id] = $v->name;
			}
			foreach ($index as $k=>$v){
				$temp = array($names[$k]);
				while (array_key_exists($v, $index)){
					$temp[] = $names[$v];
					$v = $index[$v];
				}
				$path[] = implode(' / ',array_reverse($temp));
				$rtn[] = Array('id'=>$k, 'name'=>implode(' / ',array_reverse($temp)));
			}
		}
		array_multisort($path, $rtn, SORT_STRING);
		echo json_encode($rtn);
	}
	
	/**
	 * 根据游戏ID取游戏下的商品类型
	 * @param integer $game_id 游戏标识
	 */
	function GetTypes($game_id){
		$this->load->model('MType');
		$rtn = Array();
		if($types = $this->MType->getAll(Array('game_id'=>$game_id), 'name')){
			foreach ($types as $v){
				$rtn[] = Array('id'=>$v->id, 'name'=>$v->name);
			}
		}
		die(json_encode($rtn));
	}
	
	/**
	 * 获取目录树
	 * @param int $game_id
	 */
	function getCategoryTree($game_id){
		$this->load->model('MCategory');
		$this->load->library('games');
		$categories = $this->MCategory->getAll(array('game_id'=>$game_id), 'name');
		$t['0'] = array('text'=>$this->games[$game_id],'id'=>'0','pid'=>NULL, 'type'=>'game');
		foreach ($categories as $c){
			$t[$c->id] = array('text'=>$c->name,'id'=>$c->id,'pid'=>$c->parent_id);
		}
		foreach ($t as $k => $node){
			if( !is_null($node['pid']) ) {
				$t[$node['pid']]['children'][] =& $t[$k];
			}
		}
		echo json_encode($t['0']);
	}
}
