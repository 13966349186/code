<?php
/**
 * SitePayment操作模型

 */
class MSitePayment extends MY_Model {

	protected $table = 'site_payment';
	private $filter = array();

    function __construct() {
        parent::__construct();        
    }

	
	/**
	 * 保存 配置信息
	 * @param int $id
	 * @param array $defs
	 * @return boolean
	 */
	public function saveConfig($id, $defs){
		$data = Array();
		foreach ($defs as $def){
			$tmp['account_id'] = $def['account_id'];
			$tmp['site_id'] = $def['site_id'];
			$tmp['sort'] = $def['sort'];
			$data[] = $tmp;
		}
		$this->db->where('site_id',(int)$id);
		$this->db->delete($this->table);
		if($data){
			return $this->db->insert_batch($this->table, $data);
		}else{
			return true;
		}
	}
	
	/**
     * 根据网站ID 获取网站的支付账号列表
     * @param string $siteId
     */
	public function getSitePayMents($siteId, $where=Array()){
		$this->load->model('MPaymentAccount');
		$this->db->select('core_payment_account.*,core_site_payment.id as site_payment_id,core_site_payment.sort as sort,core_payment_method.name as method_name');
		$this->db->from('core_payment_account');
		$this->db->join('core_site_payment', 'core_site_payment.account_id=core_payment_account.id  and core_site_payment.site_id = '.$siteId, 'left');
		$this->db->join('core_payment_method','core_payment_method.id = core_payment_account.method_id','left');
		$this->db->where('core_payment_account.state',MPaymentAccount::STATE_ENABLE);
		if($where){
			$this->db->where($where);
		}
		$this->db->order_by('sort is null,sort');
		return $this->db->get()->result();
	}
	
	
}