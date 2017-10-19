<?php
class Sales extends AdminController{
	function __construct(){
		parent::__construct();
		$this->load->library('Currency');
		$this->load->model('RSales');
	}
	public function index(){
		//类型限制范围及显示名称定义（同时作为SQL的字段名使用）
		$all = Array('site_id'=>'网站', 'game_id'=>'游戏', 'category_id'=>'目录', 'type'=>'类型', 'product_id'=>'商品','r_day'=>'日报', 'r_week'=>'周报', 'r_month'=>'月报');
		$time_keys = Array('r_day'=>'日报', 'r_week'=>'周报', 'r_month'=>'月报');
		//复合参数格式为：ID类型-名称   如：1site_id-fifa
		//参数列表中最后一个参数有可能不是复合参数类型，只由类型组成
		$args = func_get_args();
		//结构定义（限制下一级显示项）
		$struct = Array(
			'game_id'=>Array(
				'site_id'=>Array()
				,'category_id'=>Array(
					'type'=>Array(
						'product_id'=>Array()
					)
				)
				,'type'=>Array(
					'category_id'=>Array(
						'product_id'=>Array()
					)
				)
			)
			,'site_id'=>Array(
				'game_id'=>Array(
					'category_id'=>Array(
						'type'=>Array(
							'product_id'=>Array()
						)
					)
					,'type'=>Array(
						'category_id'=>Array(
							'product_id'=>Array()
						)
					)
				)
			)
		);
		$where = Array();//条件项（类型作为键，ID作为值）
		$names = Array();//条件项的名称（类型作为键，显示名称作为值，显示名称是表格链接中显示的名称，从复合参数中取得）
		$group = '';//表格第一列的类型（也是SQL中group by的项）
		foreach ($args as $v){
			$t = $v = base64_decode(urldecode($v));//参数转义解析
			$name = '';
			if($idx=strpos($v, '-')){//参数为三项值时，切分参数
				$name = substr($v, $idx+1);//减号后面的为名称
				$t = preg_replace('|[0-9]|','',substr($v, 0, $idx));//减号前面部分，去掉数字为类型
			}
			if(!array_key_exists($t, $struct) && !array_key_exists($t, $time_keys)){
				break;//必须按照结构定义取参数，否则停止取参
			}
			if(!((int)$v) || array_key_exists($t, $time_keys)){
				$group = $t;//点击标签时，最后一个参数不带ID和名称，至此where条件结束，group分组为最后一个参数
				break;
			}
			$where[$t] = (int)$v;//点击表格中的链接产生的参数解析结果
			$names[$t] = $name;
			$struct = $struct[$t];//选定了一个条件，结构跳往下一级
		}
		if(!$group){
			$group = key($struct);//点击菜单链接或者表格中链接时，group无值，取结构的第一项
		}
		$struct += Array('r_day'=>Array(), 'r_week'=>Array(), 'r_month'=>Array());
		$this->RSales->all = $all;
		$this->RSales->where = $where;
		$this->RSales->group = $group;
		$this->load->library('FormFilter', Array('method'=>'get'));
		$this->load->helper('formfilter');
		if(!$this->formfilter->getFilterValue('create_begin') || !$this->formfilter->getFilterValue('create_end')){
			$this->formfilter->setFilterValue('create_begin',date('Y-m-d'));
			$this->formfilter->setFilterValue('create_end',date('Y-m-d'));
		}
		if($begintime = filterValue('create_begin')){
			$this->formfilter->addFilter('create_begin', 'where',array('orders.create_time >= ',strtotime($begintime.' 00:00:00')));
		}
		if($endtime = filterValue('create_end')){
			$this->formfilter->addFilter('create_end', 'where',array('orders.create_time <= ',strtotime($endtime.' 23:59:59')));
		}
		$lst = $this->RSales->getList();
		$this->assign('where', $where);
		$this->assign('struct', $struct);
		$this->assign('names', $names);
		$this->assign('all', $all);
		$this->assign('time_keys', $time_keys);
		$this->assign('group', $group);
		$this->assign('lst', $lst);
		$this->layout();
	}
}