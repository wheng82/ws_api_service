<?php
error_reporting(E_ALL^E_NOTICE^E_WARNING); 

class redis_model {
	
	public $redism;
	
	//自行添加的redis类  与系统使用的redis模式不同，不相互关联
	public function redis_conn() {
		$rds = new Redis();
		$rds->connect($this->redism['host'],$this->redism['port']);
		$rds->select($this->redism['dbeight']);
		return $rds;
	}
	
	//自行添加的redis类  与系统使用的redis模式不同，不相互关联
	public function redis_chat_conn() {
		$rds = new Redis();
		$rds->connect($this->redism['host'],$this->redism['port']);
		$rds->select($this->redism['zero']);
		return $rds;
	}
	
}
?>