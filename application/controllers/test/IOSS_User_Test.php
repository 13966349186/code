<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Types 测试
 */
class IOSS_User_Test extends CI_Controller {

	function __construct(){
		parent::__construct();
		if(ENVIRONMENT == 'production' ){
			exit('enviroment is production');
		}
		$this->load->library('unit_test');
		$this->unit->use_strict(true);
	}
	
	function index(){
		$id = 1;
		$info['site_id']=1;
		$info['email']='test2@test.com';
		$info['password']='xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
		$info['full_name']='full name';
		$info['phone']='1234567890';
		$info['country_code']='CN';
		$info['country']='China';
		$info['city']='hefei';
		$info['address']='tian zhi lu 44';
		$info['zip']='230000';
		$info['reg_ip']='127.0.0.1';
		$info['type']=IOSS_User::TYPE_WEBSITE;
		$info['state']=2;
		$info['risk']=3;
		//***************************************** 正常测试  ******************************************************************//
		$user = IOSS_User::getById($id);
		$this->unit->run($user->id, $id, ' IOSS_User::getById',  "通过id查找用户");
		$user2 = IOSS_User::getByEmail($info['site_id'], $info['email']);
		$this->unit->run($user2->id, $id, ' IOSS_User::getByEmail',  "通过email查找用户");
		
		//编辑用户信息
		$sucess = $user->edit($info);
		$this->unit->run($sucess, true, ' IOSS_User::edit($info)',  "编辑用户信息成功！");
		//用户信息
		$this->unit->run($user->site_id, $info['site_id'], ' IOSS_User::id',  "");
		$this->unit->run($user->email, $info['email'], ' IOSS_User::id',  "");
		$this->unit->run($user->full_name, $info['full_name'], ' IOSS_User::id',  "");
		$this->unit->run($user->phone, $info['phone'], ' IOSS_User::id',  "");
		$this->unit->run($user->country_code, $info['country_code'], ' IOSS_User::id',  "");
		$this->unit->run($user->country, $info['country'], ' IOSS_User::country',  "");
		$this->unit->run($user->city, $info['city'], ' IOSS_User::city',  "");
		$this->unit->run($user->address, $info['address'], ' IOSS_User::address',  "");
		$this->unit->run($user->zip, $info['zip'], ' IOSS_User::zip',  "");
		$this->unit->run($user->reg_ip, $info['reg_ip'], ' IOSS_User::reg_ip',  "");
		$this->unit->run($user->type, $info['type'], ' IOSS_User::type',  "");
		$this->unit->run($user->state, $info['state'], ' IOSS_User::state',  "");
		$this->unit->run($user->risk, $info['risk'], ' IOSS_User::risk',  "");
		
		$this->unit->run($user->passwordVerify($info['password']), TRUE, ' IOSS_User::passwordVerify',  "验证正确的用户密码");
		$this->unit->run($user->passwordVerify('xxjksdfjksa fkdjf'), FALSE, ' IOSS_User::passwordVerify',  "验证错误的用户密码");

		//***************************************** 异常测试  ******************************************************************//
		//非法的email
		$sucess = $user->edit(array('email'=>'aaa'));
		$this->unit->run($sucess, FALSE, ' IOSS_User::edit($info)',  "编辑用户信息失败！");
		
		echo $this->unit->report();
	}
	
	/**
	 * 测试用户注册
	 */
	function testReg(){
		$info['site_id']=1;
		$info['email']='abcd@c.co';
		$info['password']='b';
		$info['full_name']='full name';
		$info['phone']='1234567890';
		$info['country_code']='CN';
		$info['country']='China';
		$info['city']='hefei';
		$info['address']='tian zhi lu';
		$info['zip']='230000';
		$info['reg_ip']='127.0.0.1';
		$info['type']='1';
		$info['state']='2';
		$info['risk']='3';
		$uid = IOSS_User::register($info);
		var_dump($uid);
		$user = IOSS_User::getById($uid);
		var_dump($user);
	}
}