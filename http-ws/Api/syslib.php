<?php

class syslib {
	
	public function __construct(){
		
		
	}
	function ClassFactory($config_file){
        //读取配置文件内容
        $handle = fopen($config_file, r);
        $content = fread($handle, filesize($config_file));
        fclose($handle);
        //去除注释
        $content=preg_replace("<\/\/.*?\s>","",$content);
        //转成数组
        return json_decode($content,true);

    }

	public function sysfactory($model){
		//include_once(__DIR__.'/tools.php');
		//echo __DIR__.'/../tools.php';exit;
		$mtools = $this->ClassFactory(__DIR__.'/apitools.json');
		include_once (__DIR__."/".$mtools['sys'][$model]);
		//echo __DIR__."/".$sys[$model];exit;
		$npname = $model.'api';
		//$ncname = $model.'m';
		$obj = NEW 	$npname;
		//$obj->$ncname = $letv;
		//print_r($obj->$ncname);exit;
		return $obj;	
	}
	
}
?>