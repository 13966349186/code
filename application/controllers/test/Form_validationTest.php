<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Currency 测试
 */
class Form_validationTest extends AdminController {
	
	function __construct(){
		parent::__construct();
		$this->_required = array('index'=>0);
		$this->load->library('Currency');
		$this->load->library('Form_validation');
	}
	
	function index(){
		phpinfo();
		
		$this->_validation = array (
				array ('field' => 'input1','label' => '游戏',	'rules' => 'required|integer' ),
				array ('field' => 'input2',	'label' => '标识',	'rules' => 'trim|required|max_length[64]' )
		);
		$this->form_validation->set_rules ($this->_validation);
		var_dump($this->form_validation->post());
		var_dump($this->form_validation->post('input2'));
		var_dump($this->form_validation->post('input3'));
		echo "<html>
				<body>
				<form action='/test/Form_validationTest' method='post'>
				<input name='input1' value='111' />
				<input name='input2' value='222' />
				<input name='input3' value='333' />
				<input type='submit' value='submit' />
				</form>
				</body>
				</html>";
	}
}