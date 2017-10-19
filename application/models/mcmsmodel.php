<?php
/**
 * 内容管理-文章类型
 * @author lifw
 */
class MCmsModel extends MY_Model {
	public $table = 'cms_model';
	public $subTable = 'cms_model_dtl';
	public $dtl_pre = 'cms_node_';
	private $filter = array();
	function __construct() {
		parent::__construct();
	}
	public function getById($id){
		$this->db->select("*");
		$this->db->from($this->table);
		$this->db->where('id', $id);
		return $this->db->get()->row();
	}
	public function getDtlById($model_id){
		$this->db->select('*');
		$this->db->from('cms_model_dtl');
		$this->db->where('model_id', $model_id);
		$query = $this->db->get();
		return $query->result();
	}
	public function getList($limit){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->limit($limit['limit'], $limit['offset']);
		$this->db->order_by('id', 'desc');
		$this->formfilter->doFilter();
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
	public function add($obj, $newList){
		$dtlTableName = $this->db->escape_str($this->db->dbprefix.$this->dtl_pre.$obj->code);
		$dtlTableName_new = $this->db->escape_str($this->db->dbprefix.$this->dtl_pre.$obj->code);
		$this->db->trans_start();
		$obj->update_time = time();
		$obj->create_time = $obj->update_time;
		$result = $this->db->insert($this->table,$obj);
		if($result){
			$obj->id = $this->db->insert_id();
		}
		if(!$result || !$obj->id){
			$this->db->_trans_status = FALSE;
		}
		$this->db->query('drop table if exists '.$dtlTableName);
		foreach ($newList as $v){
			$result = $this->db->insert($this->subTable, Array('model_id'=>$obj->id, 'col_name'=>$v->col_name, 'disp_name'=>$v->disp_name, 'data_type'=>$v->data_type, 'data_format'=>$v->data_format, 'disp_on_list'=>$v->disp_on_list));
			if(!$result){
				$this->db->_trans_status = FALSE;
				break;
			}
		}
		$this->db->query($this->_createTableSql($obj, $newList));
		return $this->db->trans_complete();
	}
	public function update($obj, $newList){
		$oldObj = $this->getOne(Array('id'=>$obj->id));
		if(!$oldObj){
			return false;
		}
		$oldDtlTableName = $this->db->escape_str($this->db->dbprefix.$this->dtl_pre.$oldObj->code);
		$newDtlTableName = $this->db->escape_str($this->db->dbprefix.$this->dtl_pre.$obj->code);
		$oldTableExists = $this->tableExists($oldDtlTableName);
		$newTableExists = false;
		if($newDtlTableName == $oldDtlTableName){
			$newTableExists = $oldTableExists;
		}else{
			$newTableExists = $this->tableExists($newDtlTableName);
		}
		//新旧表名不一样（要改表名），但新表已经存在了，则无法更新
		if($newTableExists && $newDtlTableName != $oldDtlTableName){
			return false;
		}
		$this->db->trans_start();
		//更新主表信息
		$this->db->where('id',$obj->id);
		$this->db->where('update_time',$obj->update_time);
		$obj->update_time = time();
		$ret = $this->db->update($this->table, $obj) && ($this->db->affected_rows() >= 1);
		if(!$ret){
			$this->db->_trans_status = FALSE;
		}
		$oldDtlList = $this->getDtlById($obj->id);
		$addFields = Array();
		$delFields = Array();
		$updateFields = Array();
		$remainFields = Array();
		foreach ($newList as $v){
			if(isset($v->id) && (int)$v->id > 0){
				$updateFields[$v->id.''] = $v;
			}else{
				$addFields[] = $v;
			}
		}
		foreach ($oldDtlList as $v){
			if(!array_key_exists($v->id.'', $updateFields)){
				$delFields[$v->id.''] = $v;
			}else{
				$remainFields[$v->id.''] = $v;
			}
		}
		//更新子表记录
		$this->db->where('model_id', $oldObj->id);
		$this->db->delete($this->subTable);
		foreach ($newList as $v){
			$result = $this->db->insert($this->subTable, Array('model_id'=>$obj->id, 'col_name'=>$v->col_name, 'disp_name'=>$v->disp_name, 'data_type'=>$v->data_type, 'data_format'=>$v->data_format, 'disp_on_list'=>$v->disp_on_list));
			if(!$result){
				$this->db->_trans_status = FALSE;
				break;
			}
		}

		if($oldTableExists){
			foreach ($delFields as $v){
				$sql = "alter table `".$oldDtlTableName."` drop `".$v->col_name."`";
				$result = $this->db->query($sql);
				if(!$result){
					$this->db->_trans_status = FALSE;
					break;
				}
			}
			foreach ($updateFields as $v){
				$sql = "alter table `".$oldDtlTableName."` change `".$remainFields[$v->id]->col_name."` ".$this->_createColumnInfo($v);
				$result = $this->db->query($sql);
				if(!$result){
					$this->db->_trans_status = FALSE;
					break;
				}
			}
			foreach ($addFields as $v){
				$sql = "alter table `".$oldDtlTableName."` add ".$this->_createColumnInfo($v);
				$result = $this->db->query($sql);
				if(!$result){
					$this->db->_trans_status = FALSE;
					break;
				}
			}
			if($oldObj->code != $obj->code){
				$newDtlTableName = $this->db->escape_str($this->db->dbprefix.$this->dtl_pre.$obj->code);
				$result = $this->db->query('alter table `'.$oldDtlTableName.'` rename to `'.$newDtlTableName.'`');
				if(!$result){
					$this->db->_trans_status = FALSE;
					break;
				}
			}
		}else{
			$this->db->query($this->_createTableSql($obj, $newList));
		}
		return $this->db->trans_complete();
	}
	private function _createTableSql($obj, $newList){
		$dtlTableName_new = $this->db->escape_str($this->db->dbprefix.$this->dtl_pre.$obj->code);
		$sql = "create table ".$dtlTableName_new."(node_id int not null";
		foreach ($newList as $v){
			$sql .= ','.$this->_createColumnInfo($v);
		}
		$sql .= ",PRIMARY KEY (`node_id`) ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='".$this->db->escape_str($obj->name)."';";
		return $sql;
	}
	private function _createColumnInfo($v){
		$max_length = 0;
		if(array_key_exists('ci', $v->config_arr)){
			foreach ($v->config_arr['ci'] as $cfgItem){
				$cfgItem = trim(strtolower($cfgItem));
				if(strlen($cfgItem) > 10 && substr($cfgItem, 0, 10) == 'max_length'){
					$cfgItem = trim(substr($cfgItem, 10));
					if(strlen($cfgItem) > 2 && substr($cfgItem, 0, 1) == '[' && substr($cfgItem, strlen($cfgItem)-1, 1) == ']'){
						$cfgItem = trim(substr($cfgItem, 1, strlen($cfgItem)-2));
						if($cfgItem.'' === ((int)$cfgItem).''){
							$max_length = (int)$cfgItem;
							break;
						}
					}
				}
			}
		}
		if($v->data_type == '9' || $v->data_type == '10'){
			return "`".$v->col_name."` varchar(".($max_length>0?$max_length:128).") NOT NULL default '' COMMENT '".$this->db->escape_str($v->disp_name)."'";
		}else if($v->data_type == '8'){
			return "`".$v->col_name."` varchar(".($max_length>0?$max_length:32).") NOT NULL default '' COMMENT '".$this->db->escape_str($v->disp_name)."'";
		}else if($v->data_type == '7'){
			return "`".$v->col_name."` varchar(".($max_length>0?$max_length:32).") NOT NULL default '' COMMENT '".$this->db->escape_str($v->disp_name)."'";
		}else if($v->data_type == '6'){
			return "`".$v->col_name."` varchar(".($max_length>0?$max_length:32).") NOT NULL default '' COMMENT '".$this->db->escape_str($v->disp_name)."'";
		}else if($v->data_type == '5'){
			return "`".$v->col_name."` decimal(10,2) NOT NULL default 0 COMMENT '".$this->db->escape_str($v->disp_name)."'";
		}else if($v->data_type == '4'){
			return "`".$v->col_name."` int NOT NULL default 0 COMMENT '".$this->db->escape_str($v->disp_name)."'";
		}else if($v->data_type == '3'){
			return "`".$v->col_name."` int NOT NULL default 0 COMMENT '".$this->db->escape_str($v->disp_name)."'";
		}else if($v->data_type == '2'){
			return "`".$v->col_name."` int NOT NULL default 0 COMMENT '".$this->db->escape_str($v->disp_name)."'";
		}else if($v->data_type == '1' || $v->data_type == '11'){
			return "`".$v->col_name."` text NOT NULL default '' COMMENT '".$this->db->escape_str($v->disp_name)."'";
		}else{
			return "`".$v->col_name."` varchar(".($max_length>0?$max_length:32).") NOT NULL default '' COMMENT '".$this->db->escape_str($v->disp_name)."'";
		}
	}
	public function dtlTableExists($code){
		$dtlTableName = $this->db->escape_str($this->db->dbprefix.$this->dtl_pre.$code);
		return $this->tableExists($dtlTableName);
	}
	public function dataExists($model_code, $model_id=''){
		$dtlTableName = $this->db->escape_str($this->db->dbprefix.$this->dtl_pre.$model_code);
		if($this->tableExists($dtlTableName)){
			if($this->db->query('select * from '.$dtlTableName.' limit 0,1')->result()){
				return true;
			}
		}
		if($model_id.'' === ((int)$model_id).''){
			$sql = "select * from ".$this->db->dbprefix."cms_node as n";
			$sql .= " left join ".$this->db->dbprefix."cms_category as c on n.category_id=c.id";
			$sql .= " where c.model_id=".$model_id." limit 0,1";
			if($this->db->query($sql)->result()){
				return true;
			}
		}
		return false;
	}
	public function delete($obj, $update_time){
		$this->db->trans_start();
		$this->db->where('id', $obj->id);
		$this->db->where('update_time', $obj->update_time);
		if(!$this->db->delete($this->table) && ($this->db->affected_rows() >= 1)){
			$this->db->_trans_status = FALSE;
		}
		$this->db->query('drop table if exists '.mysql_real_escape_string($this->db->dbprefix.$this->dtl_pre.$obj->code));
		$this->db->where('model_id', $obj->id);
		if(!$this->db->delete($this->subTable)){
			$this->db->_trans_status = FALSE;
		}
		return $this->db->trans_complete();
	}
    function get_cmsmodel($code){
        $this->db->select("*");
        $this->db->where('cms_model.code',$code);
        $this->db->order_by('cms_model_dtl.disp_on_list', 'Asc');
		$this->db->from($this->table);
        $this->db->join('cms_model_dtl','cms_model_dtl.model_id =cms_model.id','inner');
        $list= $this->db->get()->result();
       	return $list;
    }
    function get_cmscategory($cat_id){
        $this->db->select("*");
        $this->db->where('cms_category.id',$cat_id);
		$this->db->from('cms_category');
        $this->db->join('cms_model','cms_model.id =cms_category.model_id','inner');
        $list= $this->db->get()->result();
       	return $list;
    }
}
