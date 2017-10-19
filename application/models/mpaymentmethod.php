<?php
/**
 * 后台支付方式表操作模型
 * @author heyi
 */
class MPaymentMethod extends MY_Model {

	const STATE_ENABLE = 1;
	const STATE_DISABLE = 0;
	protected $table = 'payment_method';
	private $filter = array();

    function __construct() {
        parent::__construct();
    }

	public function getById($id){
		$query = $this->db->where('id',$id)->get($this->table);
		return $query->row();
	}
	public function getByCode($code){
		$query = $this->db->where('code',$code)->get($this->table);
		return $query->row();
	}
	
}
