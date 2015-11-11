<?php 

function secretkey($arr,$skey) {
	$key = "405b0ad53452a60698d992dbbd2a045d68fc91a2";  // bylhahaha  sha1
	ksort($arr);
	$value = json_encode($arr);
	$mytime = substr(time(),0,-3);
	$str = $key.$value.$mytime;
	$mykey = sha1(md5($str));
	if ($mykey == $skey) {
		return 1;
	}
	return 0;
}


function error_message($const) {
	$data['code'] = $const;
	echo json_encode($data);
	exit;
}


function success_ws($msg) {
	$r['code'] = '0';
	//$r['type'] = $msg['type'];
	//$r['rtype'] = "requester";
	$r['type'] = "response";
	$r['msg_id'] = $msg['msg_id'];
	return $r;
}

function error_ws($const,$msg) {
	$data['msg_id'] = $msg['msg_id'];
	//$data['rtype'] = 'requester';
	//$data['type'] = $msg['type'];
	$data['type'] = "response";
	$data['code'] = $const;
	$data['err_msg'] = $GLOBALS['error_list'][$const];
	return $data;
}
//直播系统报错
const	LIVE_ARGV_WRONG	= 10101;
const	LIVE_LETV_CONNECT_WRONG = 10102;
const	LIVE_LETV_ARGV_WRONG = 10103;
const   LIVE_LETV_STOP_ERR = 10104;
//消息系统报错
const	MSG_ARGV_ERROR = 11001;
const	MSG_DANMU_VALUENULL = 11002;
const 	MSG_MONGO_ERR = 11003;
const   MSG_NO_LOGIN = 11004;
const 	MSG_TYPE_ERROR = 11005;
//关系系统报错
const 	CONCERN_CREATE_ERR = 12001;
//用户系统报错
const	USER_NO_EXIST = 19001;

$GLOBALS['error_list'] = array(
						LIVE_ARGV_WRONG => "传递参数不全或参数不是json格式",
						LIVE_LETV_CONNECT_WRONG => "直播api接口连接错误",
						LIVE_LETV_ARGV_WRONG => "直播参数填写不正确或json格式传送错误",
						LIVE_LETV_STOP_ERR  => "视频停止失败或视频不存在",
						MSG_ARGV_ERROR => "type信息填写不正确，或参数无法正常json解析",
						MSG_DANMU_VALUENULL => "弹幕参数填写有误",
						MSG_MONGO_ERR => "MONGO连接或操作出错",
						MSG_NO_LOGIN => "请先完成登录操作再进行其他请求",
						MSG_TYPE_ERROR => "消息调用方法不存在",
						CONCERN_CREATE_ERR => "关注失败(数据库写入失败)",
						USER_NO_EXIST => '用户不存在'
					);
					




