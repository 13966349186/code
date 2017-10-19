<?php
class MRole extends MY_Model{
	protected $table = 'roles';
	
    function __construct() {
        parent::__construct();
    }
	
	public function getById($id){
		$query = $this->db->where('id',$id)->get($this->table);
		return $query->row();
	}

	public function getAll(){
		return $this->db->where('del',UNDELETED)->get($this->table)->result();
	}

	public function getList($limit){
		$result = $this->getQuery($limit)->result();
		$this->load->model('MAdmin');
		foreach ($result as &$val) {
			$val->user_num = $this->MAdmin->getUserListByRoleId($val->id)->num_rows();
		}
		return $result;
	}
	
	public function getQuery($limit=null){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where($this->table.'.del', UNDELETED);
		if($limit != null)
			$this->db->limit($limit['limit'],$limit['offset']);
		$this->formfilter->doFilter();
		$rtn = $this->db->get();
		return $rtn;
	}
	/**
	 * 管理员列表页总数量查询（为分页查询总页数）
	 * @param $name	管理员名（模糊匹配）
	 */
	public function getCount(){
		$query = $this->getQuery();
		return $query->num_rows();
	}
	/**
	 * 修改角色信息
	 * @param $roleInfoArr	要修改的信息数组
	 * @param $id			要修改的角色信息对应的标识
	 * @param $updated		要修改的角色信息对应的更新时间
	 * @param $powerArr		权限信息
	 */
	function update($roleInfoArr, $id, $updated, $powerArr){
		$this->db->where('id', $id);
		$this->db->where('update_time', $updated);
		$this->db->set($roleInfoArr);
		$this->db->set('update_time', 'unix_timestamp(sysdate())', false);
		$rtn = $this->db->update($this->table);
		if($rtn !== true || $this->db->affected_rows() < 1){
			return false;
		}
		$this->load->model('MPermission');
		$this->MPermission->setRolePermission($id, $powerArr);
		return true;
	}
	/**
	 * 新建角色
	 * @param $roleInfoArr	要添加的信息数组
	 * @param $powerArr		权限信息
	 */
	function add($roleInfoArr, $powerArr){
		$this->db->set((Array)$roleInfoArr);
		$this->db->set('update_time', 'unix_timestamp(sysdate())', false);
		$this->db->set('create_time', 'unix_timestamp(sysdate())', false);
		$rtn = $this->db->insert($this->table);
		if($rtn === true){
			$this->load->model('MPermission');
			$this->MPermission->setRolePermission($this->db->insert_id(), $powerArr);
		}
		return $rtn;
	}
	/**
	 * 删除角色
	 * @param $role_id	角色标识
	 * @param $updated	更新时间
	 */
	function delete($role_id, $updated){
		$rtn = $this->db->delete($this->table, Array('id'=>$role_id, 'update_time'=>$updated));
		if($rtn !== true || $this->db->affected_rows() < 1){
			return false;
		}
		$this->load->model('MPermission');
		$this->MPermission->clearRolePermission($role_id);
		return $rtn;
	}
}
