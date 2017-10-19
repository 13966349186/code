<?php
/**
 * 类型属性表操作模型
 * @author lifw
 */
class MTypeAttribute extends MY_Model {
	protected $table = 'type_attributes';
	private $filter = array();
    function __construct() {
        parent::__construct();
    }
    function update($type, $obj){
    	$arr = (Array)$obj;
		$this->db->trans_start();
    	$this->db->where('type_id', $type->id);
    	$this->db->delete($this->table);
    	foreach ($arr as $k=>$v) {
    		$tmpObj = new stdClass();
    		$tmpObj->type_id = $type->id;
    		$tmpObj->code = $k;
    		$tmpObj->value = $v;
    		if(!$this->add($tmpObj)){
    			$this->db->_trans_status = FALSE;
    			break;
    		}
    	}
    	return $this->db->trans_complete();
    }
}
