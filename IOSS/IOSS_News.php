<?php
/**
 * 新闻信息操作类
 * @author lifw
 */
Class IOSS_News {
	const STATE_ENABLE = 1;
	
	/** 读取新闻列表 */
	public static function getList($site_id, $offset=Array()){
		if(!is_array($offset) || !array_key_exists('begin', $offset) || !array_key_exists('pagesize', $offset)){
			$offset=Array('begin'=>0, 'pagesize'=>12);
		}
		$cache = IOSS_GameCache::getInstance();
		$key = __METHOD__.'('.$site_id.','.$offset['begin'].','.$offset['pagesize'].')';
		if(!($datas = $cache->get($key))){
			$datas = Array();
			$db = IOSS_DB::getInstance();
			$db->select('cms_node.*');
			$db->from('cms_node');
			$db->join('cms_category', 'cms_node.category_id=cms_category.id', 'left');
			$db->where('cms_category.site_id', $site_id);
			$db->where('cms_category.code', 'news');
			$db->where('cms_node.status', self::STATE_ENABLE);
			$db->order_by('cms_node.publish_time desc, cms_node.id desc');
			$db->limit($offset['pagesize'], $offset['begin']);
			if($query = $db->get()->result()){
				foreach ($query as $row){
					$datas[$row->id] = $row;
				}
				$cache->set($key,$datas);
			}
		}
		return $datas;
	}
	/** 读取新闻总数量 */
	public static function getListCount($site_id){
		$cache = IOSS_GameCache::getInstance();
		$key = __METHOD__.'('.$site_id.')';
		if(!($datas = $cache->get($key))){
			$db = IOSS_DB::getInstance();
			$db->select('count('.$db->dbprefix('cms_node').'.id) as num');
			$db->from('cms_node');
			$db->join('cms_category', 'cms_node.category_id=cms_category.id', 'left');
			$db->where('cms_category.site_id', $site_id);
			$db->where('cms_category.code', 'news');
			$db->where('cms_node.status', self::STATE_ENABLE);
			$datas = $db->get()->row();
			$cache->set($key, $datas);
		}
		return $datas?$datas->num:0;
	}
	
	private static  function ext($node, $db){
		if(!($sub = $db->get_where('cms_node_'.$node->model_code, Array('node_id'=>$node->id))->row())){
			return null;
		}
		foreach ($sub as $k=>$v){
			$node->{$k} = $v;
		}
		return $node;
	}
	
	/**
	 * @deprecated 已作废函数
	 */
	public static function getNewsById($id){
		return self::getById($id);
	}
	/**
	 * 获取新闻详细
	 * @param int $id
	 * @return object
	 */
	public static function getById($id){
		$cache = IOSS_GameCache::getInstance();
		$key = __METHOD__.'('.$id.')';
		if($datas = $cache->get($key)){
			return $datas;
		}
		$db = IOSS_DB::getInstance();
		$db->select('cms_node.*, cms_model.code as model_code');
		$db->from('cms_node');
		$db->join('cms_category', 'cms_node.category_id=cms_category.id', 'left');
		$db->join('cms_model', 'cms_category.model_id=cms_model.id', 'left');
		$db->where('cms_node.id', $id);
		$db->where('cms_node.status', self::STATE_ENABLE);
		if(!($row = $db->get()->row())){
			return null;
		}
		$row = self::ext($row, $db);
		$cache->set($key, $row);
		return $row;
	}
	/**
	 * 使用code获取新闻详细
	 * @param int $site_id
	 * @param string $cat_code
	 * @param string $node_code
	 * @return object
	 */
	public static function getByCode($site_id, $cat_code,$node_code){
		$cache = IOSS_GameCache::getInstance();
		$key = __METHOD__.'('. implode(' , ' , func_get_args()) .')';
		if($datas = $cache->get($key)){
			return $datas;
		}
		$db = IOSS_DB::getInstance();
		$db->select('cms_node.*, cms_model.code as model_code');
		$db->from('cms_node');
		$db->join('cms_category', 'cms_node.category_id=cms_category.id', 'left');
		$db->join('cms_model', 'cms_category.model_id=cms_model.id', 'left');
		$db->where('cms_category.site_id', $site_id);
		$db->where('cms_category.code', $cat_code);
		$db->where('cms_node.code', $node_code);
		$db->where('cms_node.status', self::STATE_ENABLE);
		if(!($row = $db->get()->row())){
			return null;
		}
		$row = self::ext($row, $db);
		$cache->set($key, $row);
		return $row;
	}
	
	/**
	 * 按指定目录代码，查找数据列表
	 * @param integer $site_id 网站标识
	 * @param string $cat_code 目录代码
	 * @param integer $limit 限制数量
	 * @return array
	 */
	public static function getListByCmsCategory($site_id, $cat_code, $limit=10){
		$cache = IOSS_GameCache::getInstance();
		$key = __METHOD__.'('. implode(',' , func_get_args()) .')';
		if($datas = $cache->get($key)){
			return $datas;
		}
		$db = IOSS_DB::getInstance();
		$db->select('cms_model.*, cms_category.id as category_id');
		$db->from('cms_category');
		$db->join('cms_model', 'cms_category.model_id=cms_model.id', 'left');
		$db->where('cms_category.site_id', $site_id);
		$db->where('cms_category.code', $cat_code);
		if(!($row = $db->get()->row()) || !$row->code){
			return array();
		}
		$db->select('cms_node.*, '.'cms_node_'.$row->code.'.*');
		$db->from('cms_node');
		$db->join('cms_node_'.$row->code, 'cms_node_'.$row->code.'.node_id=cms_node.id', 'left');
		$db->where('cms_node.category_id', $row->category_id);
		$db->where('cms_node.status', self::STATE_ENABLE);
		$db->order_by('publish_time', 'desc');
		if($limit > 0){
			$db->limit($limit);
		}
		if(!($query = $db->get()->result())){
			return array();
		}
		$cache->set($key, $query);
		return $query;
	}
}
