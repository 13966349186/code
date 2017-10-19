<?php
/**
 * 后台网站平台表操作模型
 * @author heyi
 */
class MSite extends MY_Model {

	protected  $table = 'site';
	const STATE_ENABLE = 1;
	const STATE_DISABLE = 0;

    function __construct() {
        parent::__construct();
    }
    
    function getState($state=NULL){
    	$define = array(self::STATE_DISABLE=>'禁用',self::STATE_ENABLE=>'启用');
    	if($state === NULL){
    		return $define;
    	}else{
    		return key_exists($state, $define)?$define[$state]:'';
    	}
    }

    /**
     * 
     * 根据网站平台ID查询网站平台信息
     * @param $id 网站平台ID
     */
	public function getById($id){
		$query = $this->db->where('id',$id)->get($this->table);
		return $query->row();
	}
	
	/**
	 *
	 * 根据网站域名查询网站平台信息
	 * @param $string 网站平台域名
	 */
	public function getByDomain($domain){
		$query = $this->db->where('domain',$domain)->get($this->table);
		return $query->row();
	}
	
	public function getList($limit=null){
		$this->db->select("*");
		$this->db->from($this->table);
		if($limit != null)
			$this->db->limit($limit['limit'],$limit['offset']);
		$this->formfilter->doFilter();
		return $this->db->get()->result();
	}
	public function getCount(){
		$this->db->select("count(*) as count");
		$this->db->from($this->table);
	//	$this->formfilter->doFilter();
		$row = $this->db->get()->row();
		return $row->count;
	}  
}
