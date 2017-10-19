<?php
/**
 * Paypal表操作模型
 * @author lifw
 */
class MPaypalTxn extends MY_Model {
	protected $table = 'paypal_txn';
	
	/** Paypal状态-付款中 */
	const PP_Pending = 'Pending';
	/** Paypal状态-已付款 */
	const PP_Completed = 'Completed';
	/** Paypal状态-退款 */
	const PP_Refunded = 'Refunded';
	/** Paypal状态-拒绝 */
	const PP_Denied = 'Denied';
	/** Paypal状态-失败 */
	const PP_Failed = 'Failed';
	/** Paypal状态-过期 */
	const PP_Expired = 'Expired';
	/** Paypal状态-投诉 */
	const PP_Reversed = 'Reversed';
	/** Paypal状态-取消投诉 */
	const PP_Canceled_Reversal = 'Canceled_Reversal';
	/** Paypal状态列表 */
	public static $PP_States = Array(self::PP_Pending=>'Pending', self::PP_Completed=>'Completed', self::PP_Refunded=>'Refunded'
		,self::PP_Denied=>'Denied', self::PP_Failed=>'Failed', self::PP_Expired=>'Expired'
		, self::PP_Reversed=>'Reversed', self::PP_Canceled_Reversal=>'Canceled_Reversal');
		
	public function getState($state = ''){
		$rtn = Array(self::PP_Pending=>'Pending', self::PP_Completed=>'Completed', self::PP_Refunded=>'Refunded'
		,self::PP_Denied=>'Denied', self::PP_Failed=>'Failed', self::PP_Expired=>'Expired'
		, self::PP_Reversed=>'Reversed', self::PP_Canceled_Reversal=>'Canceled_Reversal');
		
		if($state === ''){
			return $rtn;
		}
		return $rtn[$state];
	}
	public function getById($id){
		$this->db->select($this->table.'.*');
		$this->db->from($this->table);
		$this->db->where($this->table.'.id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function getByTxnId($txn_id){
		$this->db->select($this->table.'.*');
		$this->db->from($this->table);
		$this->db->where($this->table.'.txn_id', $txn_id);
		$query = $this->db->get();
		return $query->row();
	}	
	
	public function getList($limit=null){
		$this->db->select($this->table.'.*');
		$this->db->from($this->table);
		$this->db->join('order_payment', $this->table.'.order_payment_id=order_payment.id', 'left');
		if($limit){
			$this->db->limit($limit['limit'],$limit['offset']);
		}
		$this->formfilter->doFilter();
		$this->db->order_by($this->table.'.id','desc');
		$query = $this->db->get();
		return $query->result();
	}
	public function getCount(){
		$this->db->select('count(*) as num');
		$this->db->from($this->table);
		$this->db->join('order_payment', $this->table.'.order_payment_id=order_payment.id', 'left');
		$this->formfilter->doFilter();
		$tmp = $this->db->get()->result();
		return (int)$tmp[0]->num;
	}
}
