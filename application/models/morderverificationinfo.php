<?php
/**
 * 订单验证表操作模型
 * @author lifw
 */
class MOrderVerificationInfo extends MY_Model {
	/** 电话验证 */
	const KEY_PHONE = 1;
	/** 身份证件验证 */
	const KEY_IDCARD = 2;
	protected $table = 'order_verification_info';
	public function delete($id, $order_id, $update_time){
		return $this->db->delete($this->table, Array('id'=>$id, 'order_id'=>$order_id, 'update_time'=>$update_time)) && ($this->db->affected_rows() >= 1);
	}
	public function getList($limit){
		$this->db->select($this->table.'.*, orders.no');
		$this->db->from($this->table);
		$this->db->join('orders', $this->table.'.order_id=orders.id', 'left');
		if($limit){
			$this->db->limit($limit['limit'],$limit['offset']);
		}
		$this->formfilter->doFilter();
		$this->db->order_by('id', 'desc');
		return $this->db->get()->result();
	}
	public function getCount(){
		$this->db->select('count(*) as num');
		$this->db->from($this->table);
		$this->db->join('orders', $this->table.'.order_id=orders.id', 'left');
		$this->formfilter->doFilter();
		$tmp = $this->db->get()->result();
		return (int)$tmp[0]->num;
	}
}
