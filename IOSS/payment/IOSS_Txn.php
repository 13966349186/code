<?php
class Txn{
	const table = 'paypal_txn';
	
	private  function __construct($vo = null){
		if($vo){
			foreach ($vo as $k=>$v){
				$this->{$k} = $v;
			}
		}
	}
	
	public static function getByTxnId($txn_id){
		$db = IOSS_DB::getInstance();
		if($vo = $db->get_where(self::table, Array('txn_id'=>$txn_id))->row()){
			return new self($vo);
		}
		return null;
	}
}