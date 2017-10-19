<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 首页模块-修改密码
 */
class LoginInfo extends AdminController {
	public $_validation = null;

	function __construct(){
		parent::__construct();
		$this->load->model('MNotice');
	}
	
	function index(){
		$this->load->helper('formfilter');
		$this->load->library('FormFilter');
		$limit = $this->pagination($this->MNotice->getCount());
		$list = $this->MNotice->getList($limit);
		$currTime = time();
		foreach ($list as $v){
			$t = ($v->show_time - ($v->show_time + 28800)%86400)/86400;
			$curr = ($currTime - ($currTime + 28800)%86400)/86400;
			$span = $currTime - $v->show_time;
			if($span < 0){
				$span = 0;
			}
			if($span >= 365*24*3600){
				$v->spanStr = ((int)($span/(365*24*3600))).'年前';
			}else if($span >= 30*24*3600){
				$v->spanStr = ((int)($span/(30*24*3600))).'个月前';
			}else if($span >= 7*24*3600){
				$v->spanStr = ((int)($span/(7*24*3600))).'周前';
			}else if($span >= 24*3600){
				$v->spanStr = ((int)($span/(24*3600))).'天前';
			}else if($span >= 3600){
				$v->spanStr = ((int)($span/(3600))).'小时前';
			}else if($span >= 60){
				$v->spanStr = ((int)($span/(60))).'分钟前';
			}else{
				$v->spanStr = $span.'秒前';
			}
			//计算颜色 红到黑渐变
			$color = $span / 3600;
			if($color > 255){
				$color = 255;
			}
			$colorStr = dechex(255 - $color);
			if(strlen($colorStr) < 2){
				$colorStr = '0'.$colorStr;
			}
			$v->colorStr = $colorStr.'0000';
		}
		$this->assign('list', $list);
		$this->layout();
	}
	

}
