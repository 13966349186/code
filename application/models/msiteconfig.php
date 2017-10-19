<?php
/**
 * 后台网站参数表操作模型
 * @author lifw
 */
class MSiteConfig extends MY_Model {

	protected $table = 'site_config';
	private $filter = array();

    function __construct() {
        parent::__construct();
    }

    /**
     * 根据网站标识取配置参数
     * @param int $Id
     */
	public function getConfig($siteId){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('site_id',(int)$siteId);
		$query = $this->db->get();
		return $query->result();
	}
	public function getConfigByKey($siteId, $key){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('site_id',(int)$siteId);
		$this->db->where('config_key', $key);
		$query = $this->db->get();
		return $query->row();
	}
	public function saveConfig($siteId, $defs){
		$data = Array();
		foreach ($defs as $def){
			$tmp['site_id'] = (int)$siteId;
			$tmp['config_key'] = $def->config_key;
			$tmp['value'] = $def->value;
			$data[] = $tmp;
		}
		$this->db->where('site_id',(int)$siteId);
		$this->db->delete($this->table);
		if($data){
			return $this->db->insert_batch($this->table, $data);
		}else{
			return true;
		}
	}
    public function updateConfig($siteId, $defs){
	     $data = Array();
         $index=array();
		foreach ($defs as $def){
			$tmp['site_id'] = (int)$siteId;
			$index[]=$tmp['config_key'] = $def->config_key;
			$tmp['value'] = $def->value;
            $data[]=$tmp;
		};
		$this->db->where('site_id',(int)$siteId);
		//$this->db->delete($this->table);

		if($data){
			return $this->db->update_batch($this->table,$data);
        }else{
			return true;
		}
	}
    public function update($id,$data){
     foreach($data as $k=>$v){
	     	$this->db->set('value', $v);
     		$this->db->where('config_key', $k);
			$this->db->where('site_id',$id);
     		$this->db->update($this->table);
     }
    return true ;
   }	 
}
