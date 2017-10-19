<?php
/**
 * 后台商品表操作模型
 * @author lifw
 */
class MProduct extends MY_Model {

	/** 推荐 */
	const STATE_RECOMEND = 2;
	/** 启用 */
	const STATE_ENABLE = 1;
	/** 禁用 */
	const STATE_DISABLE = 0;
	public  $table = 'product';
	private $filter = array();
	public $states = Array(self::STATE_RECOMEND=>'推荐', self::STATE_ENABLE=>'启用', self::STATE_DISABLE=>'禁用');

	function __construct() {
        parent::__construct();
    }
	public function getList($limit, $sort=null){
		$this->db->select($this->table.'.*, category.name as category_name, category.game_id as game_id');
		$this->db->from($this->table);
		$this->db->join('category', $this->table.'.category_id=category.id', 'left');
		$this->db->limit($limit['limit'],$limit['offset']);
		$this->formfilter->doFilter();
		if($sort){
			$this->db->order_by($this->table.'.sort', 'asc');
		}else{
			$this->db->order_by('id', 'desc');
		}
		$query = $this->db->get();
		return $query->result();
	}
	public function getCount(){
		$this->db->select('count(*) as num');
		$this->db->from($this->table);
		$this->db->join('category', $this->table.'.category_id=category.id', 'left');
		$this->formfilter->doFilter();
		$tmp = $this->db->get()->result();
		return (int)$tmp[0]->num;
	}
	
	public function getCountByCategoryId($category_id){
		$this->db->select('count(id) as num');
		$this->db->from($this->table);
		$this->db->where('category_id', $category_id);
		$tmp = $this->db->get()->result();
		return (int)$tmp[0]->num;
	}
	/**
	 * 批量修改禁用状态
	 * @param $idTimeArr ID和update_time键值对数组  {'id'=> 'update_time'...}
	 * @param $flg 状态值
	 */
	function changeForbidden($idTimeArr, $flg){
		if(!is_array($idTimeArr) || count($idTimeArr) < 1){
			return false;
		}
		$this->db->trans_start();
		foreach ($idTimeArr as $id => $update_time){
			if(!$this->update((Object)Array('id'=>$id, 'update_time'=>$update_time, 'state'=>$flg))){
				break;
			}
		}
		return $this->db->trans_complete();
	}
}
