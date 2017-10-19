<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** 用户登录认证类 */
class UserIdentity{
	/** 登录用户信息 */
	private $user = null;
	private $CI= null;
	/** 用户信息保存在Session中的key */
	private $session_key = 'curr_login_user';
	
	public function __construct($param = array('checkLogin'=>TRUE)) {
		$this->CI = & get_instance();
		$this->user = $this->CI->session->userdata($this->session_key);
	
		if($param['checkLogin']){
			$this->checkLogin();
		}
	 
	}
	/** 检测登录 */
	function checkLogin(){
		if(!$this->user){
			if(array_key_exists('REQUEST_URI', $_SERVER)){
				$this->CI->session->set_userdata('login_redirect_url', $_SERVER['REQUEST_URI']);
			}
			redirect('/auth');
		}
	}
	/**
	 * 判断密码和指定用户是否匹配
	 * @param Object $user 用户信息
	 * @param string $password 密码
	 */
	function validatePassword($user, $password){
		return ($user != null) && ($user->password == $this->encodePassword($password, $user->salt));
	}
	/** 重新加载登录用户信息 */
	function refreshLoginInfo(){
		if($this->user){
			$this->CI->load->model('MAdmin');
			$info = $this->CI->MAdmin->getOne(Array('id'=>$this->user->id));
			if($info){
				$info->last_login_ip = $this->user->last_login_ip;
				$info->last_login_time = $this->user->last_login_time;
				$this->CI->session->set_userdata($this->session_key,$info);
				$this->user = $info;
			}
		}
	}
	/**
	 * 用户密码加密
	 * @param string $pwd 未加密的原生密码
	 * @return string 加密过后的密码串
	 */
	function encodePassword($pwd, $salt){
		return md5($pwd.$salt);
	}
	/**
	 * 判断用户是否被禁用了
	 * @param Object $user 用户信息
	 */
	function isForbidden($user){
		return $user->forbidden > 0;
	}
	/** 取得当前登录用户 */
	function getUser(){
		return $this->user;
	}
	/**
	 * 验证登录
	 * @param string $user_name 帐号
	 * @param string $pwd 密码
	 */
	function processLogin($user_name, $pwd){
		$this->CI->load->model('MAdmin');
		$info = $this->CI->MAdmin->getOne(Array('account'=>$user_name));
		if($info){
			if(!$this->validatePassword($info, $pwd) || $this->isForbidden($info)){
				return false;
			}
			$this->user = $info;
			$this->CI->session->set_userdata($this->session_key,$info);
			UserPower::initPermisionInfo($info->id);
			$this->CI->MAdmin->recodeLogin($info);
			return true;
		}
		return false;
	}
	/** 退出登录 */
	function logout(){
		$this->CI->session->sess_destroy();
		$this->user = null;
	}
 }
 
 /**
  * 用户/权限操作类
  * @author lifw
  */
class UserPower{
	/** 权限数组信息 */
	private static $_info;
	public $read;	
	public $edit;
	public $add;
	public $delete;
	public $value;
	
	/**
	 * 用户登录时，初始化用户权限
	 * @param int $admin_id 管理员id
	 * @return boolean 数据异常时返回 false
	 */
	public static function initPermisionInfo($admin_id){
		$CI =& get_instance();
		$CI->load->model('MAdmin');
		$CI->load->model('MPermission');
		$CI->config->load('systemmenu');
		$pages = $CI->config->item("menu_page");
		$admin = $CI->MAdmin->getById($admin_id);
		$permission_codes = array();
 
		//检查用户类型
		if(!$admin){
			return false;
		}else{
			$page_permissions = $CI->MPermission->getPermissionByRoleId($admin->role_id)->result();
			foreach ($page_permissions as $permission_item){
				$permission_codes[$permission_item->page_id] = $permission_item->power;
			}
		}
		//初始化用户页面权限
		foreach($pages as $ctrl => $pageItem){
			if($pageItem['group'] == GROUP_EVERYONE){
				self::$_info[strtolower($ctrl)] = self::decodePower(1023);
				continue;
			}
			if($admin->role_id == 1){
				self::$_info[strtolower($ctrl)] = self::decodePower(1023);
			}else{
				$code = array_key_exists($pageItem['id'],$permission_codes)?$permission_codes[$pageItem['id']]:0;
				self::$_info[strtolower($ctrl)] = self::decodePower($code);
			}
		}
		$CI->session->set_userdata('UserPower_info', serialize(self::$_info));
		return true;
	}
 
	/**
	 * 获取当前用户对某一画面的的权限集
	 * @param $ctrl 画面的控制器
	 * @return 权限对象
	 */
	public static function getPermisionInfo($ctrl){
		if(!self::$_info){
			self::load();
		}
		if (array_key_exists(strtolower($ctrl), self::$_info)){
			return self::$_info[strtolower($ctrl)];
		}
		return self::decodePower(0);
	}
 
	/**
	 * 权限位解码
	 * 将权限数字按位运算 解开成权限
	 * @param $num 权限集数字
	 * @return 权限信息
	 */
	static function decodePower($num, $info=null){
		$num = (int)$num;
		if(is_null($info)){
			$info = new UserPower();
		}
		$info->value = $num;
		$info->read = (($num & VIEWPOWER) == VIEWPOWER);
		$info->edit = (($num & EDITPOWER) == EDITPOWER);
		$info->add = (($num & ADDPOWER) == ADDPOWER);
		$info->delete = (($num & DELETEPOWER) == DELETEPOWER);
		return $info;
	}
	/**
	 * 权限位加码
	 * 将权限信息转换成数字，以便保存入库
	 * @param $info	权限信息
	 * @return 权限集数字
	 */
	static function encodePower($info){
		$rtn = 0;
		if($info->read)$rtn=($rtn | (int)VIEWPOWER);
		if($info->edit)$rtn=($rtn | (int)EDITPOWER);
		if($info->add)$rtn=($rtn | (int)ADDPOWER);
		if($info->delete)$rtn=($rtn | (int)DELETEPOWER);
		return $rtn;
	}
 
	private static function load(){
		$CI =& get_instance();
		self::$_info =	@unserialize($CI->session->userdata('UserPower_info'));
	}
 
	private function __construct(){
	}
}
