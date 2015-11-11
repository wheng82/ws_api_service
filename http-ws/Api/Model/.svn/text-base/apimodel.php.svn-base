<?php
//error_reporting(E_ALL^E_NOTICE^E_WARNING); 


//print_r($levt);exit;
class apimodel {
	public function __construct(){
		//include_once(__DIR__.'/mtools.php');
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
	public function apifactory($model){
		//include_once(__DIR__.'/mtools.php');
		//echo __DIR__.'/../tools.php';exit;
		$mtools = $this->ClassFactory(__DIR__.'/../mtools.json');
		include_once (__DIR__."/".$mtools['factory'][$model]);
		//echo __DIR__."/".$factory[$model];
		$npname = $model.'_model';
		$ncname = $model.'m';
		$obj = NEW 	$npname;
		$obj->$ncname = $mtools[$model];
		//print_r($obj->$ncname);exit;
		return $obj;	
	}
	
}
//$c = new apimodel();
//$b = $c->factory('letv');
//$b->dd();
?>