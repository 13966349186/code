<?php
/**
 * Paypal争议通告表操作模型
 * @author lifw
 */
class MPaypalIssues extends MY_Model {
	protected $table = 'paypal_issues';
	
	/** 保存争议通告信息 */
	public function save($info){
		
		if(is_array($info)){
			$info = (Object) $info;
		}
		
		if(!parent::save($info)){
			return false;
		}
		
	}
}
