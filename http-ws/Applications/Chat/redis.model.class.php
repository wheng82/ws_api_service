<?php 

namespace myRedis;

class myRedis {
	private $redishost = "127.0.0.1";
	private $redisport = "6379";
	private $redisdb = "8";
	private $redis_chatdb = "0";
	
	
	private function rd_login_set($room_id, $client_id, $client_name) {
		$key = "CLIENT_USERLIST-".$client_id;
		$data = array();
		$data['room_id'] = $room_id;
		$data['client_id'] = $client_id;
		$data['client_name'] = $client_name;
		$value = json_encode($data);
		$rds = SELF::redis_conn();
		$rds->set($key,$value);
	}
	
	//用于创建roomid的直播在线列表 live_roomid 的过期时间 解决无响应后的过期问题 mhash存储 或set存储
	private function rd_flash_livestatus() {
		$rds = SELF::redis_conn();
		$key = "LIVE_ROOMID";
		$value = $rds->get($key);
		if (isset($value)) {
			
		}else {
			//init  live_roomid
			$data['room_id'] = array();
		}
	}
	
	//登出的时候删除redis当中的userid与roomid映射关系
	private function rd_logout($client_id) {
		$rds = SELF::redis_conn();
		$key = "CLIENT_USERLIST-".$client_id;
		$status = $rds->delete($key);
		return $status;
	}
	
	//查询redis中用户信息 基于clientid 进行查询
	private function rd_userinfo($client_id) {
		$rds = SELF::redis_conn();
		$key = "CLIENT_USERLIST-".$client_id;
		$value = $rds->get($key);
		return json_decode($value,true);
		
	}
	
	//得到roomid下面所有的用户clientid
	private function redis_allclient($room_id) {
		$rds = $this->redis_chat_conn();
		$key = "ROOM_CLIENT_LIST-$room_id";
		$res = $rds->get($key);
		$client_id_array = array_keys(unserialize($res));
		return $client_id_array;
		
	}
	
	//自行添加的redis类  与系统使用的redis模式不同，不相互关联
	private function redis_conn() {
		$rds = new Redis();
		$rds->connect($this->redishost,$this->redisport);
		$rds->select($this->redisdb);
		return $rds;
	}
	
	//自行添加的redis类  与系统使用的redis模式不同，不相互关联
	private function redis_chat_conn() {
		$rds = new Redis();
		$rds->connect($this->redishost,$this->redisport);
		$rds->select($this->redis_chatdb);
		return $rds;
	}
}