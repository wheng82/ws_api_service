<?php

error_reporting(E_ALL^E_NOTICE^E_WARNING);

include_once(__DIR__.'/../Model/apimodel.php');

class liveapi extends apimodel{

	public function __construct(){
		//$this->letvapi = APIMODEL::apifactory('letv');
		//$this->redisdb = APIMODEL::apifactory('redis');
		//$this->mongodb = APIMODEL::apifactory('mongo');
	}
	//test ok
	public function letv_create($post_data) {
		unset($post_data['factory']);
		unset($post_data['type']);
		unset($post_data['user_id']);
		$LETV = APIMODEL::apifactory('letv');
		$t = time() + 28800;
		$t = date("YmdHis",$t);
		$post_data['method'] 			= APIMODEL::apifactory('letv')->letvm['create'];
		$post_data['ver'] 				= APIMODEL::apifactory('letv')->letvm['ver'];
		$post_data['userid'] 			= APIMODEL::apifactory('letv')->letvm['userid'];
		$post_data['timestamp'] 		= time()."000";
		//$post_data['activityName'] 	= "test";
		$post_data['startTime'] 		= date("YmdHis");
		$post_data['endTime'] 			= $t;
		//$post_data['coverImgUrl'] 	= "";  //活动封面地址
		$post_data['description'] 		= "这是一条测试视频";
		$post_data['liveNum'] 			= "1";  //机位数量
		//$post_data['codeRateTypes'] 	= "10";  //10 流畅；13 标清；16 高清；19 超清；22 720P；25 1080P
		//$post_data['needRecord'] 		= "1";  //是否支持全程录制
		$post_data['needTimeShift'] 	= "0";  //是否支持时移
		$post_data['activityCategory'] 	= "999";  //活动分类
		$post_data['sign'] 				= APIMODEL::apifactory('letv')->secretkey($post_data);
		$result = $LETV->curl_post($post_data);
		$check = $LETV->is_not_json($result['res']);
		if ($check == true) {
			return LIVE_LETV_CONNECT_WRONG;
		}else{
			$resarr = json_decode($result['res'],true);
			if (isset($resarr['activityId'])) {
				return array('code'=>0,'activityId'=>$resarr['activityId']);
			}else {
				return LIVE_LETV_ARGV_WRONG;
			}
			
		}
	}

	//test ok
	public function letv_stop($post_data) {
		$LETV = APIMODEL::apifactory('letv');
		$post_data['method'] 			= $LETV->letvm['stop'];
		$post_data['ver'] 				= $LETV->letvm['ver'];
		$post_data['userid'] 			= $LETV->letvm['userid'];
		$post_data['timestamp'] 		= time()."000";
		//$post_data['activityId'] 		= "A2015101900111";
		$post_data['sign'] 				= $LETV->secretkey($post_data);
		$resault =  $LETV->curl_post($post_data);
		if ($resault['httpcode'] == 200) {
			return true;
		}
		if ($resault['httpcode'] == 400) {
			return false;
		}
	}
	
	
	public function letv_modify($post_data) {
		$LETV = APIMODEL::apifactory('letv');
		$post_data['method'] 			= $LETV->letvm['modify'];
		$post_data['ver'] 				= $LETV->letvm['ver'];
		$post_data['userid'] 			= $LETV->letvm['userid'];
		$post_data['timestamp'] 		= time()."000";
		//$post_data['activityId'] 		= "A2015101900111";
		$post_data['sign'] 				= $LETV->secretkey($post_data);
		$res =  $LETV->curl_post($post_data);
		
	}

	// why?
	public function letv_search($post_data) {
		$LETV = APIMODEL::apifactory('letv');
		$post_data['method'] 			= $LETV->letvm['search'];
		$post_data['ver'] 				= $LETV->letvm['ver'];
		$post_data['userid'] 			= $LETV->letvm['userid'];
		$post_data['timestamp'] 		= time()."000";
		//$post_data['activityId'] 		= "A2015101900111";
		//$post_data['activityName'] 	= "test";
		//$post_data['activityStatus'] 	= "3";
		$post_data['sign'] 				= $LETV->secretkey($post_data);
		$res =  $LETV->curl_get($post_data);
	}
	
	
	public function letv_getUrl($post_data) {
		$LETV = APIMODEL::apifactory('letv');
		$post_data['method'] 			= $LETV->letvm['getUrl'];
		$post_data['ver'] 				= $LETV->letvm['ver'];
		$post_data['userid'] 			= $LETV->letvm['userid'];
		$post_data['timestamp'] 		= time()."000";
		//$post_data['activityId'] 		= "A2015101900185";
		$post_data['sign'] 				= $LETV->secretkey($post_data);
		return $LETV->curl_get($post_data);
	}
	
	public function letv_getPushUrl($post_data) {
		$LETV = APIMODEL::apifactory('letv');
		$post_data['method'] 			= $LETV->letvm['getPushUrl'];
		$post_data['ver'] 				= $LETV->letvm['ver'];
		$post_data['userid'] 			= $LETV->letvm['userid'];
		$post_data['timestamp'] 		= time()."000";
		//$post_data['activityId'] 		= "A2015101900130";
		$post_data['sign'] 				= $LETV->secretkey($post_data);
		return $LETV->curl_get($post_data);
	}	
	
	//将注册用户的资料写入到redis当中 ，方便用户在客户端关闭连接的时候进行注销用户 主要改自原本chat类的session模式
	//建立redis中的  userid与roomid 进行映
	public function rd_login_set($room_id, $client_id, $client_name) {
		$key = "CLIENT_USERLIST-".$client_id;
		$data = array();
		$data['room_id'] = $room_id;
		$data['client_id'] = $client_id;
		$data['client_name'] = $client_name;
		$value = json_encode($data);
		$rds = APIMODEL::apifactory('redis')->redis_conn();
		$rds->set($key,$value);
	}
	//登出的时候删除redis当中的userid与roomid映射关系
	public function rd_logout($client_id) {
		$rds = APIMODEL::apifactory('redis')->redis_conn();
		$key = "CLIENT_USERLIST-".$client_id;
		$status = $rds->delete($key);
		return $status;
	}
	
	//查询redis中用户信息 基于clientid 进行查询
	public function rd_userinfo($client_id) {
		$rds = APIMODEL::apifactory('redis')->redis_conn();
		$key = "CLIENT_USERLIST-".$client_id;
		$value = $rds->get($key);
		return json_decode($value,true);
	}
	// 根据参数进行弹幕的redis录入
	public function rd_danmu_in($i) {
		$rds = APIMODEL::apifactory('redis')->redis_chat_conn();
		$key = "LIVE_DANMU-".$i['live_id'];
		$value = json_encode($i);
		$rds->set($key,$i);
	}
	
	//得到roomid下面所有的用户clientid
	public function redis_allclient($room_id) {
		$rds = APIMODEL::apifactory('redis')->redis_chat_conn();
		$key = "ROOM_CLIENT_LIST-$room_id";
		$res = $rds->get($key);
		$client_id_array = array_keys(unserialize($res));
		return $client_id_array;
	}
	
	public function mongo_dashan_get() {
		//$c = new MongoClient('mongodb://119.254.100.239:27017');
		$MONGO = APIMODEL::apifactory('mongo');
		$MONGO->connent();
		//echo $this->mongodb->getmongoid($uid);
		$array = $MONGO->findm('dashan');
		$mo = count($array);
		$return_str = $array[rand(1, 999) % $mo];
		return $return_str['content'];
	}
	
	//连接mongo创建直播
	public function mongo_live_create($d) {
		//链接到 192.168.1.5:27017//27017端口是默认的。
		//$s = $c->joymedia->live_list;	
		$MONGO = APIMODEL::apifactory('mongo');
		$insert_array = array(	"user_id" => $d['user_id'],
								"activity_id" => $d['activity_id'],
								"zan_count"=> +0,   // 创见数据类型的时候写0 也不会被认为是int类型 必须写成＋0 经过一步运算 这里才能变成真正的数字类型存到monggo中
								"dou_count"=> +0,
								"danmu_count"=> +0,
								"live_status"=> "1",
								"live_name"=> $d['activityName'],
								"live_avatar"=> $d['coverImgUrl'],
								"start_time"=> time(),
								"end_time"=> time() + 7200
							);
		$MONGO->connent();
		$res = $MONGO->insertm('live_list',$insert_array);
		//$res = $s->insert($insert_array);
		return $res;
	}
	
	public function mongo_live_stop($d) {
		$MONGO = APIMODEL::apifactory('mongo');
		$where = array("activity_id"=> $d['activityId']);
		$set_array = array(	
								"live_status"=> "0",
								"end_time"=> time()
							);
		$MONGO->connent();
		$res = $MONGO->updatem('live_list',$where,array('$set' => $set_array));
		//$res = $s->update($where,array('$set' => $set_array));
		return $res;
	}
	
	public function mongo_live_zan_count($d) {
		$MONGO = APIMODEL::apifactory('mongo');
		$where = array("activity_id"=> $d['live_id']);
		$set_array = array(	
								"zan_count"=> + $d['zan_count'],  // 自加模式 无需关心原来的数值
							);
		$MONGO->connent();
		$res = $MONGO->updatem('live_list',$where,array('$inc' => $set_array));
		return $res;
	}
	//***查询所有正在直播对房间***//
	public function found_all_live($val){
		$MONGO = APIMODEL::apifactory('mongo');
		$where = array('live_status'=>'1','end_time'=>array('$gt'=>time()));
		$canshu =array('user_id'=>true,'activity_id'=>1,'live_name'=>1,'live_avatar'=>1);
		$MONGO->connent();
		$result = $MONGO->findm('live_list',$where,array(),$canshu);
		foreach($result as $key => $val)
		{
			unset($result[$key]['_id']);
			$result[$key]['online'] = 111;
			$result[$key]['toponline'] =2412;
			//$rediskey[] = $result[$key]['live_name']; 		
		}
		return $result;
	}
	//****查询推荐列表****/
	public function found_all_tui_live($val) {
		$MONGO = APIMODEL::apifactory('mongo');
		$MONGO->connent();
		$result = $MONGO->findm('recommend_user');
		return $result;
	}
	//用于创建roomid的直播在线列表 live_roomid 的过期时间 解决无响应后的过期问题 mhash存储 或set存储
	//未写完－－－－－－－－－－－－－－－－－－－－－－－－－－－－－－
	public function rd_flash_livestatus() {
		$rds = APIMODEL::apifactory('redis')->redis_conn();
		$key = "LIVE_ROOMID";
		$value = $rds->get($key);
		if (isset($value)) {
			
		}else {
			$data['room_id'] = array();
		}
	}
}