<?php
/**
 * 后台管理员表操作模型
 * @author lifw
 */
class MAdmin extends MY_Model {

	const IS_FORBIDDEN = 1;
	const NOT_FORBIDDEN = 0;
	
	protected $table = 'admins';
	private $filter = array();

    function __construct() {
        parent::__construct();
    }
    
    public function getState($state = NULL){
    	$f = Array(self::NOT_FORBIDDEN=>'启用', self::IS_FORBIDDEN=>'禁用');
    	if($state === NULL){
    		return $f;
    	}
    	return element($state, $f, '');
    }

    /**
     * 根据管理员ID查询管理员信息
     * @param $id 管理员ID
     */
	public function getById($id){
		$query = $this->db->where('id',$id)->get($this->table);
		return $query->row();
	}
	/**
	 * 根据管理员名查询管理员信息
	 * @param $name	管理员名（完全匹配）
	 */
	public function getByName($name){
		$query = $this->db->where('account',$name)->get($this->table);
		return $query->row();
	}

	public function getList($limit){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->limit($limit['limit'],$limit['offset']);
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
	/**
	 * 管理员列表页总数量查询（为分页查询总页数）
	 * @param $name	管理员名（模糊匹配）
	 */
	/**
	 * 根据角色标识 取用户
	 * @param $role_id 角色标识
	 */
	public function getUserListByRoleId($role_id){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('role_id', $role_id);
		return $this->db->get();
	}

	public function addAdmin($vo){
		if(isset($vo->password) && strlen($vo->password) > 0){
			$vo->salt = md5(time().rand(1, 10000));
			$vo->password = $this->useridentity->encodePassword($vo->password, $vo->salt);
		}else{
			unset($vo->password);
			unset($vo->salt);
		}
		return $this->add($vo);
	}
	public function updateAdmin($vo){
		if(isset($vo->password) && strlen($vo->password) > 0){
			$vo->salt = md5(time().rand(1, 10000));
			$vo->password = $this->useridentity->encodePassword($vo->password, $vo->salt);
		}else{
			unset($vo->password);
			unset($vo->salt);
		}
		return $this->update($vo);
	}
	/**
	 * 批量修改禁用状态
	 * @param $idTimeList ID=>update_time数组 {'id'=> 'update_time'...}
	 * @param $flg 禁用的值(1：禁用   0：启用)
	 */
	function changeForbidden($idTimeList, $flg){
		if(!is_array($idTimeList) || count($idTimeList) < 1){
			return false;
		}
		$currTime = time();
		$this->db->trans_start();
		foreach ($idTimeList as $id => $update_time){
			if(!$this->update((Object)Array('id'=>$id, 'update_time'=>$update_time, 'forbidden'=>$flg))){
				$this->db->_trans_status = FALSE;
				break;
			}
		}
		return $this->db->trans_complete();
	}
	/**
	 * 批量删除用户
	 * @param $idTimeList ID=>update_time数组 {'id'=> 'update_time'...}
	 */
	function deleteUser($idTimeList){
		if(!is_array($idTimeList) || count($idTimeList) < 1){
			return false;
		}
		$currTime = time();
		$this->db->trans_start();
		foreach ($idTimeList as $id => $update_time){
			if(!$this->db->delete($this->table, Array('id'=>$id, 'update_time'=>$update_time)) || ($this->db->affected_rows() < 1)){
				$this->db->_trans_status = FALSE;
				break;
			}
		}
		return $this->db->trans_complete();
	}
	/**
	 * 修改密码
	 * @param $user	用户信息
	 * @param $oldPwd	旧密码
	 * @param $newPwd	新密码
	 */
	function changePwd($user, $oldPwd, $newPwd){
		$oldPwd = $this->useridentity->encodePassword($oldPwd, $user->salt);
		$newSalt = md5(time().rand(1, 10000));
		$newPwd = $this->useridentity->encodePassword($newPwd, $newSalt);
		$this->db->set('password', $newPwd);
		$this->db->set('salt', $newSalt);
		$this->db->where('id', $user->id);
		$this->db->where('password', $oldPwd);
		$rtn = $this->db->update($this->table);
		return ($rtn === true) && ($this->db->affected_rows() == 1);
	}
	/**
	 * 登录成功后的动作
	 * @param $user 用户信息
	 */
	function recodeLogin(&$user){
		//记录IP和登录时间
		$this->db->set('last_login_time', time());
		$this->db->set('last_login_ip', $this->input->ip_address());
		$this->db->where('id', $user->id);
		$this->db->update($this->table);
	}
}
