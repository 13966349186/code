<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tmp extends CI_Controller {
	function __construct(){
		parent::__construct();
	}
	public function index()
	{
		$tables = Array();
		$lst = $this->db->query('SHOW TABLES FROM '.$this->db->database)->result();
		foreach ($lst as $v){
			$tables[$v->{'Tables_in_'.$this->db->database}] = Array();
		}
		ksort($tables);
		$str = '';
		$line = '<br>';
		foreach($tables as $k=>&$v){
			$str .= $k.'{'.$line;
			$lst = $this->db->query('SHOW FULL COLUMNS FROM `'.$k.'`')->result();
			foreach ($lst as $vv){
				$vv = (Array)$vv;
				//ksort($vv);
				$v[$vv['Field']] = $vv;
			}
			//ksort($v);
			foreach ($v as $kk => $col){
				$str .= "\t".$kk;
				$str .= "\t".$col['Type'];
				$str .= "\t".($col['Null']=='YES'?'null':'not null');
				$str .= "\t".$col['Key'];
				$str .= "\t default ".($col['Default'] === null ? 'null': (is_string($col['Default'])?'\''.$col['Default'].'\'':$col['Default']));
				$str .= "\t".$col['Extra'];
				$str .= $line;
			}
			$str .= '}'.$line;
			$indexs = Array();
			if($lst = $this->db->query('show index from  `'.$k.'`')->result()){
				foreach ($lst as $idx_v){
					if(!array_key_exists($idx_v->Key_name, $indexs)){
						$indexs[$idx_v->Key_name] = Array();
					}
					$indexs[$idx_v->Key_name][$idx_v->Seq_in_index] = $idx_v;
				}
			}
			ksort($indexs);
			foreach ($indexs as $idx_k=>$idx_v){
				ksort($idx_v);
				$sub_str = '';
				foreach ($idx_v as $idx_sub_k => $idx_sub_v){
					if($sub_str){
						$sub_str .= ',';
					}
					$sub_str .= $idx_sub_v->Column_name;
				}
				if($idx_sub_v->Non_unique == '1'){
					$str .= "\t index ".$idx_k.' on '.$k.'('.$sub_str.')';
				}else{
					$str .= "\t union index ".$idx_k.' on '.$k.'('.$sub_str.')';
				}
				$str .= $line;
			}
			
		}
		echo $str;
	}
}
