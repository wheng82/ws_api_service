<?php 

error_reporting(E_ALL^E_NOTICE^E_WARNING); 
 
use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Store;

include __DIR__.'/../../Api/syslib.php';

class ws_api extends syslib {
	private $redishost = "127.0.0.1";
	private $redisport = "6379";
	private $redisdb = "8";
	private $redis_chatdb = "0";
	private $howtosend = array(
								'all' => 'sendToAll',
								'self' => 'sendToCurrentClient',
								'someone' => 'sendToClient'
							);	
	//定义gateway方法 根据type不同如何进行返回
	private $sendtowho 	= array(
									'danmu_get' => 'self',
									'danmu_send' => 'all',
									'chat_send' => 'all',
									'dashan_send' => 'all',
									'zan_send' => 'all',
									'letv_create_actid' => 'all',
									'live_start' => 'all'
								);
	private $playbanksys;
	private $financesys;
	public function __construct($client_id,$message) {

			
	}	
	
	//公共方法开始 解决登录 登出  和gateway路由  等chat基本功能
	
	public function ws_login($message_data,$client_id) {
		//注册 room id 与 user id的映射
		$LIVE = SYSLIB::sysfactory('Live');
		$USER = SYSLIB::sysfactory('User');
		//组织需要写入redis的参数
		//$arg = json_decode($message_data,true);
		$room_id = $message_data['room_id'];
		$user_id = $message_data['user_id'];
		// 如果参数有问题直接报错
		if ($room_id == "" or $user_id == "") {
			$return_error = error_ws(MSG_ARGV_ERROR,$message_data);
			Gateway::sendToCurrentClient(json_encode($return_error));
			return;
		}
		$isuser = $USER->mongo_getuserinfo($user_id);
		if($isuser === FALSE){
			$return_error = error_ws(USER_NO_EXIST,$message_data);
			Gateway::sendToCurrentClient(json_encode($return_error));
			return;
		} else {
			
			$LIVE->rd_login_set($room_id, $client_id, $user_id);
			// 存储到当前房间的客户端列表
			$all_clients = SELF::addClientToRoom($room_id, $client_id, $user_id);
			
			// 整理客户端列表以便显示
			//$client_list = SELF::formatClientsData($all_clients);
			
			$_SESSION['user_id'] = $user_id;
			$_SESSION['room_id'] = $room_id;
			// 给客户端请求者的回应  只有成功的回应才能走到这一步
			$return_message = success_ws($message_data);
			Gateway::sendToCurrentClient(json_encode($return_message));
			
			//给所有人的回应
			$uinfo = $USER->mongo_getuserinfo($_SESSION['user_id']);
			$sendtoall['type'] = $message_data['type'];
			$sendtoall['user_id'] = $_SESSION['user_id'];
			//$sendtoall['rtype'] = "roomer";
			//$sendtoall['client_list'] = $client_list;
			$sendtoall['username'] = $uinfo['nickname'];
			$sendtoall['avatar'] = $uinfo['avatar'];
	 		$client_id_array = array_keys($all_clients);
			Gateway::sendToAll(json_encode($sendtoall), $client_id_array);
		}
	}
	
	// 本方法当中，对于返回数据还有待完善
	public function ws_logout($client_id) {
		$LIVE = SYSLIB::sysfactory('Live');
		$get_client_info = $LIVE->rd_userinfo($client_id);
		if (is_array($get_client_info)) {
			//删除自定义用户组当中的在线信息
			$status = $LIVE->rd_logout($client_id);
			$room_id = $get_client_info['room_id'];
			//删除房间中的clinetid 此部分为workermanchat的onclose封装处理
			self::delClientFromRoom($room_id, $client_id);
			//对room当中的其他人进行离线通知
			$client_id_array = $LIVE->redis_allclient($room_id);
			Gateway::sendToAll(json_encode($client_id_array), $client_id_array);
		}
		
	}
	
	//分发任务，执行相应的function对程序进行响应，最后由gateway决定返回给什么样的用户
	public function gateway($client_id,$message_data,$room_id) {
		$return_message = $this->$message_data['type']($message_data,$client_id);
		//Gateway::sendToAll(json_encode($return_message), $client_id_array);
		// debug
		echo "OUTPUT:".json_encode($return_message)."\n";
	}
	
	//公共方法结束 下面是私有方法，纯粹响应onmessage请求
	
	private function live_start($message_data) {
		// user_id  live_id
		
	}
	
	// 获取一个时间段的某个视频的弹幕 两种情况 如果既不是直播也不是回放 那么这里get不到任何值
	private function danmu_get($message_data) {
		
	}
	
	// 写弹幕日志 如果是直播或回放写入到某个视频的某个时间段内  如果是非直播也非回放，则只是进行send给房间中的人
	// danmu_content  user_id  live_id live_timestamp
	private function danmu_send($message_data) {
		$LIVE = SYSLIB::sysfactory('Live');
		$USER = SYSLIB::sysfactory('User');
		//$rds = SYSLIB::sysfactory('Live')->rd_danmu_in($message_data);
		$fault = 0;
		foreach ($message_data as $key => $value) {
			if (empty($value)) {
				$return_error = error_ws(MSG_DANMU_VALUENULL,$message_data);
				Gateway::sendToCurrentClient(json_encode($return_error));
			}
		}
			//给发送端的返回
			$return_message = success_ws($message_data);
			Gateway::sendToCurrentClient(json_encode($return_message));
			
			//给所有人的请求
			$uinfo = $USER->mongo_getuserinfo($_SESSION['user_id']);
			
			$sendtoall['username'] = $uinfo['nickname'];
			$sendtoall['avatar'] = $uinfo['avatar'];
			$sendtoall['type'] = $message_data['type'];
			$sendtoall['user_id'] = $_SESSION['user_id'];
			$sendtoall['danmu_content'] = $message_data['danmu_content'];
			//$sendtoall['rtype'] = "roomer";
			$client_id_array = $LIVE->redis_allclient($_SESSION['room_id']);
			Gateway::sendToAll(json_encode($sendtoall,JSON_UNESCAPED_UNICODE),$client_id_array);
	}
	
	// 认为应该把直播回访与房间无人时候的聊天分离开分别处理  聊天应该就是对全屋人的请求做一个响应不做任何数据记录
	private function chat_send() {
		
	}
	
	
	// 搭讪  从mongodb取一个随机的留言，发送给客户端的所有人
	private function dashan_send($message_data) {
		$LIVE = SYSLIB::sysfactory('Live');
		$USER = SYSLIB::sysfactory('User');
		//$MONGO = SYSLIB::sysfactory('Mongo');
		foreach ($message_data as $key => $value) {
			if (empty($value)) {
				$return_error = error_ws(MSG_DANMU_VALUENULL,$message_data);
				Gateway::sendToCurrentClient(json_encode($return_error));
			}
		}
		
			//给发送端的返回
			$return_message = success_ws($message_data);
			Gateway::sendToCurrentClient(json_encode($return_message));
			
			//给所有人的请求
			$uinfo = $USER->mongo_getuserinfo($_SESSION['user_id']);
			
			$sendtoall['username'] = $uinfo['nickname'];
			$sendtoall['avatar'] = $uinfo['avatar'];
			$sendtoall['type'] = $message_data['type'];
			$sendtoall['user_id'] = $_SESSION['user_id'];
			//$sendtoall['rtype'] = "roomer";
			$sendtoall['dashan_content'] = $LIVE->mongo_dashan_get();
			$client_id_array = $LIVE->redis_allclient($_SESSION['room_id']);
			Gateway::sendToAll(json_encode($sendtoall,JSON_UNESCAPED_UNICODE),$client_id_array);
	}
	
	// 用户点赞 如果是
	private function zan_send($message_data,$client_id) {
		$LIVE = SYSLIB::sysfactory('Live');
		foreach ($message_data as $key => $value) {
			if (empty($value)) {
				$return_error = error_ws(MSG_DANMU_VALUENULL,$message_data);
				Gateway::sendToCurrentClient(json_encode($return_error));
			}
		}
		
		$res = $LIVE->mongo_live_zan_count($message_data);
		if ($res == TRUE){
			//给发送端的返回
			$return_message = success_ws($message_data);
			Gateway::sendToCurrentClient(json_encode($return_message));
			//给所有人的请求
			$sendtoall['type'] = $message_data['type'];
			$sendtoall['user_id'] = $_SESSION['user_id'];
			//$sendtoall['rtype'] = "roomer";
			$sendtoall['zan_count'] = $message_data['zan_count'];
			$client_id_array = $LIVE->redis_allclient($_SESSION['room_id']);
			$ckey = array_search($client_id, $client_id_array);
			unset($client_id_array[$ckey]);
			//var_dump($return_message);
			Gateway::sendToAll(json_encode($sendtoall,JSON_UNESCAPED_UNICODE),$client_id_array);
		}else{
			$return_error = error_ws(MSG_MONGO_ERR,$message);
			Gateway::sendToCurrentClient(json_encode($return_error));
			return;
		}
	}
	
	
	// 用户点赞 如果是
	private function dashang_send($message_data) {
		$LIVE = SYSLIB::sysfactory('Live');
		$USER = SYSLIB::sysfactory('User');
		foreach ($message_data as $key => $value) {
			if (empty($value)) {
				$return_error = error_ws(MSG_DANMU_VALUENULL,$message_data);
				Gateway::sendToCurrentClient(json_encode($return_error));
			}
		}
		
		$return_message = success_ws($message_data);
		$return_message['balance'] = '52.00';
		Gateway::sendToCurrentClient(json_encode($return_message));
				
		//给所有人的请求
		$meddileuser = $USER->mongo_getuserinfo($_SESSION['user_id']);
		$sendtoall['type'] = $message_data['type'];
		$sendtoall['user_id'] = $_SESSION['user_id'];
		$sendtoall['username'] = $meddileuser['nickname'];
		$sendtoall['avatar'] = $meddileuser['avatar'];
		//$sendtoall['rtype'] = "roomer";
		$sendtoall['dashang_count'] = $message_data['dashang_count'];
		$client_id_array = $LIVE->redis_allclient($_SESSION['room_id']);
		Gateway::sendToAll(json_encode($sendtoall,JSON_UNESCAPED_UNICODE),$client_id_array);
	}
	
	// activityName  codeRateTypes  coverImgUrl   needRecord
	private function live_create_actid($arg) {
		$LIVE = SYSLIB::sysfactory('Live');
		$post['activityName'] = $arg['activityName'];
		$post['codeRateTypes'] = $arg['codeRateTypes'];
		$post['coverImgUrl'] = $arg['coverImgUrl'];
		$post['needRecord'] = $arg['needRecord'];
		foreach ($post as $key => $value) {
			if ($value == "") {
				$return_error = error_ws(MSG_ARGV_ERROR,$message);
				Gateway::sendToCurrentClient(json_encode($return_error));
				return;
			}
		}
		$newcanshu = $LIVE->letv_create($post);
		$active_id = $newcanshu['activityId'];
		//得到了乐视接口返回的数据后进行判断，如果是成功了，就要向redis里面刷新当前直播的列表
		if (is_numeric($active_id)) {
			//调用失败处理
			$return_error = error_ws($active_id,$message);
			Gateway::sendToCurrentClient(json_encode($return_error));
		}else {
			$arg['activity_id'] = $active_id;
			$res = $LIVE->mongo_live_create($arg);
			if ($res == true){
				//给发送端的返回
				$return_message = success_ws($arg);
				$return_message['activityId'] = $active_id;
				Gateway::sendToCurrentClient(json_encode($return_message));
				
				//给所有人的请求
				$sendtoall['type'] = $arg['type'];
				$sendtoall['user_id'] = $_SESSION['user_id'];
				//$sendtoall['rtype'] = "roomer";
				$sendtoall['activityId'] = $active_id;
				$client_id_array = $LIVE->redis_allclient($_SESSION['room_id']);
				Gateway::sendToAll(json_encode($sendtoall,JSON_UNESCAPED_UNICODE),$client_id_array);
			}else{
				$return_error = error_ws(MSG_MONGO_ERR,$message);
				Gateway::sendToCurrentClient(json_encode($return_error));
				return;
			}
			
		}
	}
	//activityId
	private function live_stop_actid($arg) {
		$LIVE = SYSLIB::sysfactory('Live');
		//$arg = json_decode($message,true);
		$post['activityId'] = $arg['activityId'];
		$jsonreturn = $LIVE->letv_stop($post);
		//得到了乐视接口返回的数据后进行判断，如果是成功了，就要向redis里面刷新当前直播的列表
		if ($jsonreturn == false) {
			$return_error = error_ws(LIVE_LETV_STOP_ERR,$message);
			Gateway::sendToCurrentClient(json_encode($return_error));
		}else {
			$res = $LIVE->mongo_live_stop($arg);
			if ($res == TRUE){
				//给发送端的返回
				$return_message = success_ws($arg);
				Gateway::sendToCurrentClient(json_encode($return_message));
				
				//给所有人的请求
				$sendtoall['type'] = $arg['type'];
				$sendtoall['user_id'] = $_SESSION['user_id'];
				$sendtoall['activityId'] = $arg['activityId'];
				$client_id_array = $LIVE->redis_allclient($_SESSION['room_id']);
				Gateway::sendToAll(json_encode($sendtoall,JSON_UNESCAPED_UNICODE),$client_id_array);
			}else{
				$return_error = error_ws(MSG_MONGO_ERR,$message);
				Gateway::sendToCurrentClient(json_encode($return_error));
				return;
			}
			
		}
		return $jsonreturn;
	}
	
	public function concern_create($arg){
		$concernid = $arg['concern_id'];
		$user_id = $_SESSION['user_id'];
		$result = SYSLIB::sysfactory('Concren')->create($user_id,$concernid);
		if($result == true){
			$return_message = success_ws($arg);
			Gateway::sendToCurrentClient(json_encode($return_message));
		} else {
			$return_error = error_ws(CONCERN_CREATE_ERR,$message);
			Gateway::sendToCurrentClient(json_encode($return_error));
			return;
		}
	} 
	
	
	// ＝＝＝＝＝＝分界线，从这里往下都是工具函数，不涉及聊天返回给谁的问题，只被gateway当中的方法调用＝＝＝＝＝＝＝＝
	
	
	
	

		
	
	
		
	
	
	//tichu-----------
	
	
	
		
		
		
	// ＝＝＝＝＝＝分界线＝＝＝＝＝＝＝＝
	//这下面的方法不要修改，这些是移植自workerman－chat的原始通讯方法 修改会影响server端正常响应
	
	/**
	 * 添加到客户端列表中
	 * @param int $client_id
	 * @param string $client_name
	 */
	private static function addClientToRoom($room_id, $client_id, $client_name)
	{
	    $key = "ROOM_CLIENT_LIST-$room_id";
	    $store = Store::instance('room');
	    // 获取所有所有房间的实际在线客户端列表，以便将存储中不在线用户删除
	    $all_online_client_id = Gateway::getOnlineStatus();
	    
	    // 存储驱动是memcached
	    if(get_class($store) == 'Memcached')
	    {
	        $cas = 0;
	        $try_count = 3;
	        while($try_count--)
	        {
	            $client_list = $store->get($key, null, $cas);
	            if(false == $client_list)
	            {
	                if($store->getResultCode() == \Memcached::RES_NOTFOUND)
	                {
	                    $client_list = array();
	                }
	                else
	                {
	                    throw new \Exception("Memcached->get($key) return false and memcache errcode:" .$store->getResultCode(). " errmsg:" . $store->getResultMessage());
	                }
	            }
	            if(!isset($client_list[$client_id]))
	            {
	                // 将存储中不在线用户删除
	                if($all_online_client_id && $client_list)
	                {
	                    $all_online_client_id = array_flip($all_online_client_id);
	                    $client_list = array_intersect_key($client_list, $all_online_client_id);
	                }
	                // 添加在线客户端
	                $client_list[$client_id] = $client_name;
	                // 原子添加
	                if($store->getResultCode() == \Memcached::RES_NOTFOUND)
	                {
	                    $store->add($key, $client_list);
	                }
	                // 置换
	                else
	                {
	                    $store->cas($cas, $key, $client_list);
	                }
	                if($store->getResultCode() == \Memcached::RES_SUCCESS)
	                {
	                    return $client_list;
	                }
	            }
	            else 
	            {
	                return $client_list;
	            }
	        }
	        throw new \Exception("addClientToRoom($room_id, $client_id, $client_name)->cas($cas, $key, \$client_list) fail .".$store->getResultMessage());
	    }
	    // 存储驱动是memcache或者file
	    else
	    {
	        $handler = fopen(__FILE__, 'r');
	        flock($handler,  LOCK_EX);
	        $client_list = $store->get($key);
	        if(!isset($client_list[$client_id]))
	        {
	            // 将存储中不在线用户删除
	            if($all_online_client_id && $client_list)
	            {
	                $all_online_client_id = array_flip($all_online_client_id);
	                $client_list = array_intersect_key($client_list, $all_online_client_id);
	            }
	            // 添加在线客户端
	            $client_list[$client_id] = $client_name;
	            $ret = $store->set($key, $client_list);
	            flock($handler, LOCK_UN);
	            return $client_list;
	        }
	        flock($handler, LOCK_UN);
	    }
	    return $client_list;
	}
	
	
	/**
	 * 从客户端列表中删除一个客户端
	 * @param int $client_id
	 */
	public static function delClientFromRoom($room_id, $client_id)
	{
	    $key = "ROOM_CLIENT_LIST-$room_id";
	    $store = Store::instance('room');
	    // 存储驱动是memcached
	    if(get_class($store) == 'Memcached')
	    {
	        $cas = 0;
	        $try_count = 3;
	        while($try_count--)
	        {
	            $client_list = $store->get($key, null, $cas);
	            if(false == $client_list)
	            {
	                if($store->getResultCode() == \Memcached::RES_NOTFOUND)
	                {
	                    return array();
	                }
	                else
	                {
	                     throw new \Exception("Memcached->get($key) return false and memcache errcode:" .$store->getResultCode(). " errmsg:" . $store->getResultMessage());
	                }
	            }
	            if(isset($client_list[$client_id]))
	            {
	                unset($client_list[$client_id]);
	                if($store->cas($cas, $key, $client_list))
	                {
	                    return $client_list;
	                }
	            }
	            else 
	            {
	                return true;
	            }
	        }
	        throw new \Exception("delClientFromRoom($room_id, $client_id)->Store::instance('room')->cas($cas, $key, \$client_list) fail" . $store->getResultMessage());
	    }
	    // 存储驱动是memcache或者file
	    else
	    {
	        $handler = fopen(__FILE__, 'r');
	        flock($handler,  LOCK_EX);
	        $client_list = $store->get($key);
	        if(isset($client_list[$client_id]))
	        {
	            unset($client_list[$client_id]);
	            $ret = $store->set($key, $client_list);
	            flock($handler, LOCK_UN);
	            return $client_list;
	        }
	        flock($handler, LOCK_UN);
	    }
	    return $client_list;
	}
	
	
	/**
	 * 格式化客户端列表数据
	 * @param array $all_clients
	 */
	private static function formatClientsData($all_clients)
	{
	    $client_list = array();
	    if($all_clients)
	    {
	        foreach($all_clients as $tmp_client_id=>$tmp_name)
	        {
	            $client_list[] = array('client_id'=>$tmp_client_id, 'user_id'=>$tmp_name);
	        }
	    }
	    return $client_list;
	}

	
}
