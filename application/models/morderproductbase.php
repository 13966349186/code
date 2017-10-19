<?php
/**
 * 后台订单商品(基本)表操作模型
 * @author lifw
 */
class MOrderProductBase extends MY_Model {
	protected $table = 'order_product_base';
    function __construct() {
        parent::__construct();
    }
    public function getListByOrderId($order_id){
		$this->db->select($this->table.'.*, order_product.*, game.code as game_code, game.name as game_name, category.name as category_name, types.name as type_name, types.model');
		$this->db->from($this->table);
		$this->db->join('category', 'category.id=order_product.category_id', 'left');
		$this->db->join('game', 'category.game_id=game.id', 'left');
		$this->db->join('types', 'types.id=order_product.type', 'left');
		$this->db->join('order_product', $this->table.'.order_product_id=order_product.id', 'left');
		$this->db->where('order_product.order_id', $order_id);
		return $this->db->get()->result();
    }
	public function getDtlById($order_product_id){
		$this->db->select($this->table.'.*, order_product.*, game.code as game_code, game.name as game_name, category.name as category_name, types.name as type_name, types.model');
		$this->db->from($this->table);
		$this->db->join('order_product', $this->table.'.order_product_id=order_product.id', 'left');
		$this->db->join('category', 'order_product.category_id=category.id', 'left');
		$this->db->join('types', 'types.id=order_product.type', 'left');
		$this->db->join('game', 'category.game_id=game.id', 'left');
		$this->db->where($this->table.'.order_product_id', $order_product_id);
		return $this->db->get()->row();
	}
    /**
     * 删除购物车中的一条记录
     * @param object $info 订单商品信息
     */
    public function deleteCart($info){
    	$this->db->trans_start();
    	if(!$this->db->delete('order_product', Array('id'=>$info->id, 'update_time'=>$info->update_time)) || ($this->db->affected_rows() < 1)){
    		$this->db->_trans_status = FALSE;
    	}
    	if($this->db->_trans_status){
    		$this->db->delete($this->table, Array('order_product_id'=>$info->id));
    	}
    	return $this->db->trans_complete();
    }
    public function addCart($item){
    	$this->db->trans_start();
    	$order_product = new stdClass();
    	$order_product->order_id = $item->order_id;
    	$order_product->product_id = $item->id;
    	$order_product->category_id = $item->category_id;
    	$order_product->type = $item->type_id;
    	$order_product->name = $item->name;
    	$order_product->description = '';
    	$order_product->price = $item->price;
    	$order_product->num = $item->num;
    	$order_product->delivery_state = MOrder::DELEVERY_STATE_NOT_DELEVERED;
    	$order_product->delivery_time = 0;
    	$order_product->update_time = time();
    	$this->load->model('MOrderProduct');
    	if(!$this->MOrderProduct->add($order_product)){
    		$this->db->_trans_status = FALSE;
    	}
    	$myItem = (Object)Array('order_product_id'=>$order_product->id);
    	if($this->db->_trans_status){
	    	$this->db->insert($this->table,$myItem);
    	}
    	return $this->db->trans_complete();
    }
}
