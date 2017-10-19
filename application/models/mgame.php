<?php
/**
 * 后台游戏表操作模型
 * @author heyi
 */
class MGame extends MY_Model {

	const STATE_ENABLE = 1;
	const STATE_DISABLE = 0;
	protected $table = 'game';
	private $filter = array();

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
    
	public function getList($limit, $sort=false){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->limit($limit['limit'],$limit['offset']);
		$this->formfilter->doFilter();
		if($sort){
			$this->db->order_by('sort', 'asc');
		}else{
			$this->db->order_by('id', 'desc');
		}
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
	
	/**
     * 
     * 根据游戏ID查询游戏信息
     * @param $id 游戏ID
     */
	public function getById($id){
		$query = $this->db->where('id',$id)->get($this->table);
		return $query->row();
	}
	
}
