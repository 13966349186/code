<?php
/**
 * 后台支付账号表操作模型
 * @author heyi
 */
class MPaymentAccount extends MY_Model {

	const STATE_ENABLE = 1;
	const STATE_DISABLE = 0;
	public $states = array(self::STATE_DISABLE=>'禁用', self::STATE_ENABLE=>'启用');
	protected $table = 'payment_account';
	private $filter = array();

    function __construct() {
        parent::__construct();
    }
    
    public function getState($key = NULL){
    	if($key === NULL){
    		return $this->states;
    	}
    	return element($key, $this->states, '');
    }

	public function getById($id){
		$query = $this->db->where('id',$id)->get($this->table);
		return $query->row();
	}
	
	public function delete($id,$update_time = NULL){
		
		$this->db->where('id', $id);
		if($update_time !== NULL){
			$this->db->where('update_time', $update_time);
		}
		return $ret = $this->db->delete($this->table);
	}
	
	public function getByAccount($account){
		$query = $this->db->where('account',$account)->get($this->table);
		return $query->row();
	}
	
	public function getList($limit){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->limit($limit['limit'],$limit['offset']);
		$this->formfilter->doFilter();
		$this->db->order_by($this->table.'.id', 'desc');
		$query = $this->db->get();
		return $query->result();
	}
	public function getCount(){
		$this->db->select('count(*) as num');
		$this->db->from($this->table);
		$this->formfilter->doFilter();
		$tmp = $this->db->get()->result();
		return (int)$tmp[0]->num;
	}
}
