<?php
/**
 * 后台商品子表-金币表操作模型
 * @author lifw
 */
class MProductItem extends MY_Model {
	public $table = 'product_item';
    function __construct() {
        parent::__construct();
        $this->load->model('MProduct');
    }
    
    function createVo(){
    	$vo = $this->MProduct->createVo();
    	$query = $this->db->query('SHOW COLUMNS FROM ' . $this->db->dbprefix . $this->table);
    	foreach($query->result() as $c){
    		$vo->{$c->Field} = '';
    	}
    	return $vo;
    }
    
    function getAll($where=null){
    	$this->db->select("{$this->table}.*, {$this->MProduct->table}.*");
    	$this->db->from($this->table);
    	$this->db->join('product', "{$this->table}.product_id={$this->MProduct->table}.id", 'left');
    	if($where != null){
    		$this->db->where($where);
    	}
    	return $this->db->get()->result();
    }
    
    function getOne($where=null){
        $this->db->select("{$this->table}.*, {$this->MProduct->table}.*");
    	$this->db->from($this->table);
    	$this->db->join('product', "{$this->table}.product_id={$this->MProduct->table}.id", 'left');
    	if($where != null){
    		$this->db->where($where);
    	}
    	return $this->db->get()->row();
    }
    
    /**
     * 拆分VO对象
     * @param object $vo
     * @return array
     */
    private function  splitVo($vo){
    	$product = $this->MProduct->createVo();
    	foreach ($product as $k=>$v){
    		$product->{$k} = $vo->{$k};
    		unset($vo->{$k});
    	}
    	return array($product, $vo);
    }
    
    
    /**
     * 添加商品信息
     * @param Object $vo 要入库的商品信息（商品表和子表的信息在一起）
     */
    function add($vo){
    	list($product, $ext) = $this->splitVo($vo);
    	$this->db->trans_start();
    	$this->MProduct->add($product);
    	$ext->product_id = $product->id;
    	$this->db->insert($this->table,$ext);
    	return $this->db->trans_complete();
    }
    
    /**
     * 编辑商品信息
     * @param Object $vo 
     */
    function update($vo){
    	list($product, $ext) = $this->splitVo($vo);
    	unset($ext->product_id);
    	$this->db->trans_start();
		$this->MProduct->update($product);
		$this->db->where('product_id',$product->id);
		$this->db->update($this->table,$ext);
    	return $this->db->trans_complete();
    }

	public function delete($id, $update_time){
    	$this->db->trans_start();
    	$this->db->delete($this->MProduct->table, Array('id'=>$id));
    	$this->db->delete($this->table, Array('product_id'=>$id));
    	return $this->db->trans_complete();
	}
	

}
