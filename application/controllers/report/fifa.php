<?php
class Fifa extends AdminController{
	private $fifa_games = Array('fifa14', 'fifa15', 'fifa16','fifa17');
	function __construct(){
		parent::__construct();
		$this->load->library('Currency');
		$this->load->library('Sites');
		$this->load->library('Games');
		$this->load->model('MOrder');
		$this->load->model('RFIFA');
		$this->assign('fifa_games', $this->fifa_games);
	}
	public function index($game_code=null, $category_id=null, $category_name=null){
		if(!$game_code){
			$game_code = $this->fifa_games[0];
		}
		if(!($game = $this->MGame->getOne(Array('code'=>$game_code)))){
			show_error('游戏不存在！');
		}
		$this->assign('game', $game);
		$this->RFIFA->game_id = $game->id;
		$this->RFIFA->category_id = $category_id;
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
		$lst = $this->RFIFA->getList();
		$col = 'category_id';
		if($category_id){
			$col = 'type_id';
		}
		if($lst){
			$tmp = Array();
			foreach ($lst as $v){
				$tmp[$v->{$col}] = $v;
			}
			$lst = $tmp;
			foreach ($this->currency as $k=>$v){
				if($query = $this->RFIFA->getAmountSum($k)){
					foreach ($query as $row){
						if(array_key_exists($row->{$col}, $lst)){
							$lst[$row->{$col}]->{$k} = $row->amount;
						}
					}
				}
			}
		}
		$this->assign('params', $this->input->get()?'?'.http_build_query($this->input->get()):'');
		$this->assign('game', $game);
		$this->assign('category_id', $category_id);
		$this->assign('category_name', $category_name);
		$this->assign('lst', $lst);
		$this->layout();
	}
}