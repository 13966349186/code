<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(E_ALL);
/**
 * IOSS_Product 测试
 */
class IOSS_Product_Test extends CI_Controller {

	function __construct(){
		parent::__construct();
		if(ENVIRONMENT == 'production' ){
			exit('enviroment is production');
		}
		$this->load->library('unit_test');
		$this->unit->use_strict(TRUE);
		$this->unit->set_test_items(array('test_name', 'result'));
		//$this->output->enable_profiler(true);
	}
	
	function index(){
		$db = IOSS_DB::getInstance();
		//创建测试数据
		$t_row = array(
				'id'=>899,
				'category_id'=>82,
				'type_id'=>45,
				'name'=>'Test Product',
				'description'=>'unit test data',
				'price'=>'119.41',
				'sort'=>'5',
				'state'=>1
		);
		$db->update('product',$t_row, array('id'=>$t_row['id']));
		
		//测试开始 =======================================================================================
		$this->unit->run(IOSS_Product::getProduct(-1), NULL, '测试 IOSS_Product::getProduct(id) ',  "当id不存在时，返回 NULL");
		
		$porduct = IOSS_Product::getProduct($t_row['id']);
		$this->unit->run(get_class($porduct), 'IOSS_ProductGold', ' 测试 IOSS_Product::getProduct(id) ',  "当id存在时，返回 IOSS_ProductGold 对象");
		$this->unit->run($porduct->id, $t_row['id'] . '', '测试属性值 id',  "id =" .  $t_row['id']);
		$this->unit->run($porduct->category_id, $t_row['category_id'] . '', '测试属性值 category_id',  "");
		$this->unit->run($porduct->type_id, $t_row['type_id'] . '',  '测试属性值 type_id',  "");
		$this->unit->run($porduct->name, $t_row['name'],  '测试属性值 name',  "");
		$this->unit->run($porduct->description, $t_row['description'],  '测试属性值 description',  "");
		$this->unit->run($porduct->sort, $t_row['sort'],  '测试属性值 sort',  "");
		$this->unit->run($porduct->state, $t_row['state'] . '',  '测试属性值 state',  "");
		//金币商品
		$this->unit->run($porduct->gold_num, 800 . '', '测试属性值 gold_num',  "gold_num = {$porduct->gold_num}");
		
		//测试 IOSS_Product::getProducts
		$products = IOSS_Product::getProducts($t_row['category_id']);
		$count = count($products);
		$this->unit->run($count, 77, "测试方法 getProducts  - category_id={$t_row['category_id']}",  "预期商品数量为  $count = 77");
		
		//测试 IOSS_Product::getList
		$list = IOSS_Product::getList($t_row['category_id']);
		$count = count($list);
		$this->unit->run($count, 77, "测试方法 getList - category_id={$t_row['category_id']}",  "预期商品数量为  $count = 77");
		$this->unit->run(get_class($list[0]), "IOSS_Product", "测试方法 getList - category_id={$t_row['category_id']}",  "对象类型为 IOSS_Product");
		
		$list2 = IOSS_Product::getList(0);
		$count2 = count($list2);
		$this->unit->run($count2, 0, "测试方法 getList - category_id=0",  "预期商品数量为  $count2 = 0");
		
		$this->unit->result();
		echo $this->unit->report();
	}
	
	function testProductGold(){
		$category_id = 82;
		$db = IOSS_DB::getInstance();
		$golds = IOSS_ProductGold::getProducts($category_id);
		var_dump($golds);

	}
	
	function testProductItem(){
		$db = IOSS_DB::getInstance();
		//创建测试数据
		$t_row = array(	'id'=>1021,'category_id'=>96,	'type_id'=>52,'name'=>'Test Item Product',	'description'=>'description test',	'price'=>'19.41',	'sort'=>'2',	'state'=>1);
		$t_item= array('product_id'=>1021, 'stock'=>200, 'image'=>'/xxxx/a.jpg');
		$db->update('product',$t_row, array('id'=>$t_row['id']));
		$db->update('product_item',$t_item, array('product_id'=>$t_item['product_id']));
		
		$porduct = IOSS_Product::getProduct($t_row['id']);
		$this->unit->run(get_class($porduct), "IOSS_ProductItem", "测试方法 getProduct({$t_row['id']})",  "对象类型为 IOSS_ProductItem");
		
		echo $this->unit->report();
	}
}