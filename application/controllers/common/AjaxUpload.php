<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 共用模块-临时文件上传的Ajax调用接口
 */
class AjaxUpload extends AdminController {
	function __construct(){
		parent::__construct();
		$this->output->enable_profiler(false);
		$this->temp_path =  $this->config->item('tmp_path')? trim($this->config->item('tmp_path'), '/ ') : 'upload/tmp';
	}
	
	function index(){
		//$this->upload();
	}
	
	/**
	 * 上传文件到临时文件目录
	 * @param string $field
	 */
	function upload($field){
		if(!isset($_FILES) || !is_array($_FILES) || count($_FILES) < 1){
			echo json_encode(Array('error'=>'请选择要上传的文件！'));
			return;
		}
		do{
			$path = $this->temp_path . '/' .  md5(uniqid(mt_rand()));
		}while(file_exists($path));
		if(!mkdir($path, DIR_READ_MODE))	{
			echo json_encode(Array('error'=>'临时目录创建失败！'));
			return;
		}
		//上传文件
		$config['upload_path'] = $path . '/';
		$config['allowed_types'] = 'jpg|png|bmp|swf';
		$config['max_size'] = '2000';
		$config['overwrite'] = FALSE;
		$this->load->library('upload', $config);
		if ($this->upload->do_upload($field)){
			$data = $this->upload->data();
			$message['path'] = $config['upload_path'] . $data['file_name'];
			$message['url'] = site_url($message['path']);
			$message['file_size'] = $data['file_size'];
			$message['is_image'] = $data['is_image'];
			$message['error'] = '';
		}else{
			$message['error'] = $this->upload->display_errors('');
		}
		//清理过期文件
		if(mt_rand(0, 100) < 10){
			$this->removeTempFile();
		}
		echo json_encode($message);
	}
	
	/**
	 * 清理过期的临时文件
	 */
	private function removeTempFile(){
		if(!$paths = @scandir($this->temp_path)){
			return ;
		}
		foreach ($paths as $path){
			if(!$path || $path[0] == '.'){
				continue;  //隐含目录以及 . 和 .. 都不处理
			}
			$path = $this->temp_path . '/' . $path;
			if( is_dir($path) &&   (fileatime($path) < time() - 3600*12)){
				foreach (@scandir($path) as $file){
					$file = $path . '/' . $file;
					if(is_file( $file)){
						@unlink($file);
					}
				}
				@rmdir($path);
			}
		}
	}
}
