<?php
/**
 * cms模块 node表操作模型
 * @author zhangyk
 * 
 */
class MCmsNode extends MY_Model {
	protected $table = 'cms_node';
	/**
	 * 根据目录ID构造node对应的model
	 * @param int $category_id 目录ID
	 */
	function __construct() {
		parent::__construct();
	}
	function getListWithModel($limit, $model_code){
		$subTable = $this->table.'_'.$model_code;
		$this->db->select($this->table.'.*');
		$this->db->select($subTable.'.*');
		$this->db->from($this->table);
		$this->db->join($subTable, $subTable.'.node_id='.$this->table.'.id', 'left');
		$this->db->limit($limit['limit'], $limit['offset']);
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
	public function createSubVo($model_code){
		$subTable = $this->table.'_'.$model_code;
		$query = $this->db->query('SHOW COLUMNS FROM ' . $this->db->dbprefix . $subTable);
		$vo = new stdClass();
		foreach($query->result() as $c){
			$vo->{$c->Field} = '';
		}
		return $vo;
	}
	public function getDtlByIdAndCode($id, $model_code){
		$subTable = $this->table.'_'.$model_code;
		$this->db->select($this->table.'.*');
		$this->db->select($subTable.'.*');
		$this->db->from($this->table);
		$this->db->join($subTable, $subTable.'.node_id='.$this->table.'.id', 'left');
		$this->db->where('id', $id);
		return $this->db->get()->row();
	}
	/**
	 * 添加新闻
	 * @param Object $info 数据对象
	 * @param Object $model 类型信息对象
	 * @param Array $model_list 类型信息定义列表
	 */
    function addNode($info, $model, $model_list){
	   	$subTable = $this->table.'_'.$model->code;
	   	//将信息分成两个表的对象
	   	$newObj = (Object)((Array)$info);
	   	$selfObj = $this->createSubVo($model->code);
	   	$fields = (Array)$selfObj;
	   	foreach ($fields as $k=>$v){
	   		if(property_exists($newObj, $k)){
			   	$selfObj->{$k} = $newObj->{$k};
			   	unset($newObj->{$k});
	   		}
	   	}
	   	//将两个对象信息以事务入库
	   	$this->db->trans_start();
	   	if(!$this->add($newObj)){
	   		$this->db->_trans_status = FALSE;
	   	}
	   	$selfObj->node_id = $newObj->id;
	   	if(!$this->db->insert($subTable,$selfObj)){
	   		$this->db->_trans_status = FALSE;
	   	}
	   	$info->id = $newObj->id;
	   	return $this->db->trans_complete();
    }
	/**
	 * 编辑新闻
	 * @param Object $info 数据对象
	 * @param Object $model 类型信息对象
	 * @param Array $model_list 类型信息定义列表
	 */
    function editNode($info, $model, $model_list){
	   	$subTable = $this->table.'_'.$model->code;
	   	//将信息分成两个表的对象
	   	$newObj = (Object)((Array)$info);
	   	$selfObj = $this->createSubVo($model->code);
	   	$fields = (Array)$selfObj;
	   	foreach ($fields as $k=>$v){
	   		if(property_exists($newObj, $k)){
			   	$selfObj->{$k} = $newObj->{$k};
			   	unset($newObj->{$k});
	   		}
	   	}
	   	//将两个对象信息以事务入库
	   	$this->db->trans_start();
	   	if(!$this->update($newObj)){
	   		$this->db->_trans_status = FALSE;
	   	}
	   	$selfObj->node_id = $newObj->id;
	   	$this->db->where('node_id',$newObj->id);
	   	if(!$this->db->update($subTable,$selfObj)){
	   		$this->db->_trans_status = FALSE;
	   	}
	   	$info->id = $newObj->id;
	   	return $this->db->trans_complete();
    }
    /**
     * 删除新闻
     * @param integer $id 新闻ID
     * @param integer update_time 更新时间
     * @param string $model_code 子表代码
     */
    function deleteNode($id, $update_time, $model_code){
    	$subTable = $this->table.'_'.$model_code;
	   	//将两个对象信息以事务入库
	   	$this->db->trans_start();
	   	$this->db->where('id',$id);
	   	$this->db->where('update_time',$update_time);
	   	if(!$this->db->delete($this->table) || ($this->db->affected_rows() < 1)){
	   		$this->db->_trans_status = FALSE;
	   	}
	   	$this->db->where('node_id',$id);
	   	if(!$this->db->delete($subTable) || ($this->db->affected_rows() < 1)){
	   		$this->db->_trans_status = FALSE;
	   	}
	   	return $this->db->trans_complete();
	}
}


