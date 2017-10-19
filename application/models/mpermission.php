<?php
/**
 * 后台管理员表操作模型
 * @author lifw
 */
class MPermission extends CI_Model{

	private $table = 'permissions';

    function __construct() {
        parent::__construct();
    }

    function getPermissionByRoleId($role_id){
    	$this->db->select("*");
    	$this->db->from($this->table);
    	$this->db->where('role_id', $role_id);
    	return $this->db->get();
    }
    function setRolePermission($role_id, $data){
    	$this->db->delete($this->table, Array('role_id'=>$role_id));
    	foreach ($data as $item){
    		$item['role_id'] = $role_id;
    		$this->db->insert($this->table, $item);
    	}
    }
    function clearRolePermission($role_id){
    	$this->db->delete($this->table, Array('role_id'=>$role_id));
    }
}
