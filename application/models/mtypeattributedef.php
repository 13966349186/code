<?php
/**
 * 类型属性定义表操作模型
 * @author lifw
 */
class MTypeAttributeDef extends MY_Model {
	protected $table = 'type_attribute_def';
	private $filter = array();
    function __construct() {
        parent::__construct();
    }
    function getListByModel($model){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('model', $model);
		$this->db->order_by('sort', 'asc');
		$query = $this->db->get();
		return $query->result();
    }
}
