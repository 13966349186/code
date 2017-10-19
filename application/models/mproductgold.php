<?php
/**
 * 后台商品子表-金币表操作模型
 * @author lifw
 */
class MProductGold extends MY_Model {
	protected $table = 'product_gold';
    function __construct() {
        parent::__construct();
    }
    /**
     * 添加商品信息
     * @param Object $info 要入库的商品信息（商品表和子表的信息在一起）
     */
    function add($vo, $product){
    	$this->load->model('MProduct');
    	//将两个对象信息以事务入库
    	$this->db->trans_start();
    	if($this->MProduct->add($product)){
	    	$vo->product_id = $product->id;
	    	$this->db->insert($this->table, $vo);
    	}
    	return $this->db->trans_complete();
    }
    /**
     * 编辑商品信息
     * @param Object $vo 子表信息
     * @param Object $product 商品表信息
     */
    function update($vo, $product){
    	//将信息对象分成商品表、商品子表两个要入库的对象
    	$this->load->model('MProduct');
    	//将两个对象信息以事务入库
    	$this->db->trans_start();
		if($this->MProduct->update($product)){
			$this->db->where('product_id',$vo->product_id);
    		$this->db->update($this->table, $vo);
		}
    	return $this->db->trans_complete();
    }
    
	public function getDtlByProductId($product_id){
		$this->db->select($this->table.'.*, product.*, category.name as category_name, game.name as game_name, types.name as type_name, types.model');
		$this->db->from($this->table);
		$this->db->join('product', $this->table.'.product_id=product.id', 'left');
		$this->db->join('types', 'product.type_id=types.id', 'left');
		$this->db->join('category', 'product.category_id=category.id', 'left');
		$this->db->join('game', 'category.game_id=game.id', 'left');
		$this->db->where('product.id', $product_id);
		$query = $this->db->get();
		return $query->row();
	}
	public function getList($limit=null){
		$this->db->select($this->table.'.*, product.*, category.name as category_name, game.name as game_name, types.name as type_name, types.model');
		$this->db->from($this->table);
		$this->db->join('product', $this->table.'.product_id=product.id', 'left');
		$this->db->join('types', 'product.type_id=types.id', 'left');
		$this->db->join('category', 'product.category_id=category.id', 'left');
		$this->db->join('game', 'category.game_id=game.id', 'left');
		if($limit){
			$this->db->limit($limit['limit'],$limit['offset']);
		}
		$this->formfilter->doFilter();
		$this->db->order_by('product.sort', 'asc');
		$query = $this->db->get();
		return $query->result();
	}
	public function getCount(){
		$this->db->select('count(*) as num');
		$this->db->from($this->table);
		$this->db->join('product', $this->table.'.product_id=product.id', 'left');
		$this->db->join('category', 'product.category_id=category.id', 'left');
		$this->db->join('game', 'category.game_id=game.id', 'left');
		$this->formfilter->doFilter();
		$tmp = $this->db->get()->result();
		return (int)$tmp[0]->num;
	}
	public function getFullAll($where){
		$this->db->select($this->table.".*, product.*");
		$this->db->from($this->table);
		$this->db->join('product', $this->table.'.product_id=product.id', 'left');
		if($where != null){
			$this->db->where($where);
		}
		return $this->db->get()->result();
	}
	public function delete($id, $update_time){
    	$this->db->trans_start();
    	$this->db->delete('product', Array('id'=>$id));
    	$this->db->delete($this->table, Array('product_id'=>$id));
    	return $this->db->trans_complete();
	}
	
	/**
     * 批量更新金币价格
     * @param Array(object) $list 要修改的金币价格
     */
    function GoldPriceBatch($list){
    	$this->load->model('MProduct');
    	//以事务入库
    	$this->db->trans_start();
    	foreach ($list as $vo){
    		$this->MProduct->update($vo);
    	}
    	return $this->db->trans_complete();
    }
}
