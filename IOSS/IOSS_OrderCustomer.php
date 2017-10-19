<?php
class IOSS_OrderCustomer{
	public $user_id = 0;
	public $user_full_name = '';
	public $user_email = '';
	public $user_phone = '';
	public $user_ip = '';
	public $user_agent = '';
	public $user_state = 0;
	public $refer_url = '';
	public function __construct($arr = array()){
		foreach ($arr as $k=>$v){
			if(property_exists($this, $k)){
				$this->$k = $v;
			}
		}
	}
}