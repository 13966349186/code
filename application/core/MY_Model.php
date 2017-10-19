<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model{
	/**
	 * @var CI_DB_active_record $db
	 */
	public $db;
	protected  $time_zone = '+8:00';
	
	public function __construct(){
		parent::__construct();
		$CI =& get_instance();
		$this->db = $CI->db;
	}
	
	public function createVo(){
		var_dump($this->table);
		$query = $this->db->query('SHOW COLUMNS FROM ' . $this->db->dbprefix . $this->table);
	
		$vo = new stdClass();
		foreach($query->result() as $c){
			$vo->{$c->Field} = '';
		}
	
		return $vo;
	}

	public function getOne($where=null){
		$this->db->select("*");
		$this->db->from($this->table);
		if($where != null){
			$this->db->where($where);
		}
		return $this->db->get()->row();
	}

	public function getAll($where=null,$order_by=null){
		$this->db->select("*");
		$this->db->from($this->table);
		if($where != null){
			$this->db->where($where);
		}
		if($order_by != null){
			$this->db->order_by($order_by);
		}
		return $this->db->get()->result();
	}

	public function update($vo){
		$this->db->where('id',$vo->id);
		if(property_exists($vo, 'update_time')){
			$this->db->where('update_time',$vo->update_time);
			$vo->update_time = time();
		}
		$success =  $this->db->update($this->table,$vo) && ($this->db->affected_rows() >= 1);
		if($success === FALSE){
			$this->db->_trans_status = FALSE;
		}
		return $success;
	}

	public function add($vo){
		if(property_exists($vo,'update_time'))
			$vo->update_time = time();
		if(property_exists($vo,'create_time'))
			$vo->create_time = time();
		$result = $this->db->insert($this->table,$vo);
		if($result && (!property_exists($vo, 'id') || !$vo->id)){
			$vo->id = $this->db->insert_id();
		}
		return $result;
	}
	public function save($vo){
		if(!empty($vo->id)){
			return $this->update($vo) ;
		}else{
			return $this->add($vo);
		}
	}
	public function tableExists($tableName){
		$sql = "select table_name from information_schema.tables where table_name='".$tableName."' and table_schema='".$this->db->database."';";
		$ret = $this->db->query($sql)->result();
		if($ret){
			return true;
		}
		return false;
	}
}
