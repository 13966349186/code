<?php
/**
 * 用户操作类
 * @author zhangyk
 *
 */
class IOSS_User{
	const TYPE_WEBSITE = 0;
	const STATE_DISABLED = 0;
	const STATE_ENABLED = 1;
	private static  $table = 'core_users';
	private static  $editable_cols = array('email','full_name','phone','country_code','country','city','address', 'zip','reg_ip','state','risk','password');
	
	private $id;
	private $site_id;
	private $password;
	private $email;
	private $full_name;
	private $phone;
	private $country_code;
	private $country;
	private $city;
	private $address;
	private $zip;
	private $reg_ip;
	private $type;
	private $state;
	private $risk;
	

	/**
	 * 新用户注册
	 * @param 用户信息 $info
	 * @return integer 用户ID或者错误代码(小于0为错误代码)
	 */
	public static function register($info){
		$db = IOSS_DB::getInstance();
		$data = array_intersect_key($info, array_flip(self::$editable_cols));
		if(!(isset($info['site_id']) && $info['site_id']>0)){
			return -1;
		}
		if( !(isset($info['email']) && self::isLegalEmail($info['email'])) ){
			return -2;
		}
		if(!( isset($info['password']) && $info['password']  )){
			return -3;
		}
		if( $db->get_where(self::$table, Array('email'=>$info['email'], 'site_id'=>$info['site_id']))->row()){
			return -4;  //Email已存在
		}
		$data['site_id'] = $info['site_id'];
		$data['email'] = $info['email'];
		$data['password'] = password_hash($info['password'], PASSWORD_DEFAULT);
		$data['risk'] = isset($data['risk']) ? (int)$data['risk'] : 0;
		$data['type'] = self::TYPE_WEBSITE;
		$data['state'] = self::STATE_ENABLED;
		$data['create_time'] = time();
		$data['update_time'] = $data['create_time'];
		if($db->insert(self::$table, $data)){
			return $db->insert_id();
		}else{
			return -99;
		}
	}
	
	static function isLegalEmail($email){
		return strlen($email) > 4 && strlen($email) < 255 &&  preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
	}
	
	/**
	 * 通过id获取用户
	 * @param int $id
	 * @return IOSS_User|NULL
	 */
	public static  function getById($id){
		$db = IOSS_DB::getInstance();
		$vo = $db->get_where(self::$table, Array( 'id'=>$id))->row();
		if($vo){
			return  new self($vo);
		}else{
			return null;
		}
	}
	
	/**
	 * 通过email获取用户
	 * @param int $site_id
	 * @param string $email
	 * @return IOSS_User|NULL
	 */
	public static function getByEmail($site_id, $email){
		$db = IOSS_DB::getInstance();
		$vo = $db->get_where(self::$table,  Array('email'=>$email, 'site_id'=>$site_id))->row();
		if($vo){
			return  new self($vo);
		}else {
			return null;
		}
	}


	private function __construct($vo){
		foreach ($vo as $k=>$v){
			if(property_exists($this, $k)){
				if($k == 'id' || $k=='site_id' || $k == 'type' || $k == 'state' || $k == 'risk'){
					$this->{$k} = (int)$v;
				}else{
					$this->{$k} = $v;
				}
			}
		}
	}
	
	public function __get($key){
		if($key != 'password' && property_exists($this, $key)){
			return $this->$key;
		}
		$trace = debug_backtrace();
		trigger_error(	'Undefined property via __get(): ' . $key . 	' in ' . $trace[0]['file'] .	' on line ' . $trace[0]['line'],	E_USER_NOTICE);
		return null;
	}
	
	/**
	 * 验证用户密码
	 * @param string $pass 用户输入的原始密码
	 * @return boolean
	 */
	public function passwordVerify($pass){
		return $this->password && password_verify($pass, $this->password);
	}

	/**
	 * 编辑用户信息
	 * @param array $info 用户信息
	 * @return boolean
	 */
	public function edit($info){
		$db = IOSS_DB::getInstance();
		$data = array_intersect_key($info, array_flip(self::$editable_cols));
		if(isset($data['email']) && !self::isLegalEmail($data['email'])){
			return false;
		}
		if(isset($data['password'])){
			if($data['password']){
				$data['password'] = password_hash($info['password'], PASSWORD_DEFAULT);
			}else{
				unset($data['password']);
			}
		}
		if(isset($data['risk'])) $data['risk'] = (int)$data['risk'];
		if(isset($data['state'])) $data['state'] = (int)$data['state'];
		$data['update_time'] = time();
		$sucess = $db->where('id', $this->id)->update(self::$table, $data) && ($db->affected_rows() >= 1);
		if($sucess){
			foreach ($data as $k=>$v){
				$this->$k = $v;
			}
		}
		return $sucess;
	}
}
