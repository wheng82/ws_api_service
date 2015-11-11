<?php
error_reporting(E_ALL^E_NOTICE^E_WARNING);

include_once('tools.php');
include_once ('syslib.php');

/*foreach ($_POST as $key => $value) {
	$data[$key] = $value;
	if ($key != "type" && $key != 'user_id') {
		$letvdata[$key] = $value;
	}
}

if (!isset($value) || is_null($data)) {
	echo error_message(LIVE_ARGV_WRONG);  //10101  参数无法json反解析
}else {
	$l = new LetvApi;
	$res = json_encode($l -> $data['type']($letvdata));
	if ($res['code'] != "0") {
		echo json_encode($res);
	}else {
		$res['type'] = $data['type'];
		echo json_encode($res);
	}
}*/
//function apiconnect($fty,$method,$val='st') {
	
	$val = $_POST;
	$fty = $val['factory'];
	$method = $val['type'];
//apiconnect($factory,$type,$arr);
	$FACTORY = NEW syslib();
	$canshu =  $FACTORY->sysfactory($fty);
	//if($val == 'st'){
		//$result = $canshu->$method();
	//} else {
		$result = $canshu->$method($val);
	//}
	//var_dump($canshu);
	//echo $method;
	if(!is_numeric($result)){
		echo json_encode($result);
	} else {
		$err = array(
			'code'=>$result,'err_msg'=>$GLOBALS['error_list'][$result]
		);
		echo json_encode($err);
	}
//}
/*function found_all_live(){
		$FACTORY = NEW syslib();
		$LIVE = $FACTORY->sysfactory('Live');
		$result = $LIVE->found_all_live();
		if(!empty($result)) {
			echo json_encode($result);exit;
			return json_encode($result);
		} else {
			echo 'cuowu';
		}
	}*/
	
	

