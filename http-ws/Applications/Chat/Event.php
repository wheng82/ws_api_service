<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose 
 */
error_reporting(E_ALL^E_NOTICE^E_WARNING); 
 
use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Store;

include_once('ws_api.php');
include_once(dirname(__FILE__).'/../../Api/Live/live_api.php');
//include_once('/opt/workerman/http-ws/Api/tools.php');
include_once(dirname(__FILE__)."/../../Api/tools.php");

class Event
{
   
   public $sent_arr = array(
   							'all' => 'sendToAll',
   							'self' => 'sendToCurrentClient',
   							'someone' => 'sendToClient'
   						);
   public $debug = 1;
   						
   						
   /**
    * 当链接创建的时候 
    * @param int $client_id
    * @param string $message
    */
  
  public static function onConnect($client_id,$message) {
  		/*$message_data = json_decode($message, true);
  		*/
  }
   
   /**
    * 有消息时
    * @param int $client_id
    * @param string $message
    */
   public static function onMessage($client_id, $message)
   {  
   
   		//debug
   		echo "client_id:$client_id room_id:".$new_message['room_id']."\nINPUT:".$message."\n";
        
        // 对数据进行基本验证，如果参数都不正确直接不给予正确返回  踢出用户方法 Gateway::closeClient($client_id);
        $message_data = json_decode($message, true);
        if(!$message_data)
        {
        	$return_error = error_ws(MSG_ARGV_ERROR,$message_data);
        	Gateway::sendToCurrentClient(json_encode($return_error));
            return ;
        }
        // 兼容网页客户端
        if (!is_null($message_data['json'])) {
        	$message_data = json_decode($message_data['json'],true);	
        }

        //如果是登录操作，先进行登录检查，否则进行登录验证 验证给定的room_id下面有没有userid 如果有则继续下面步骤，没有就认为请求非法踢用户
        $ws = new ws_api();
        if ($message_data['type'] == 'ws_login'){
        	$ws->ws_login($message_data,$client_id);
        	return ;
        }
        
        
         //  .......... 踢出用户方法 Gateway::closeClient($client_id); 在执行业务逻辑前先判断用户有没有执行登录操作 没有就报错踢出 
        if (!isset($_SESSION['user_id'])){
        	$return_error = error_ws(MSG_NO_LOGIN,$message_data);
        	Gateway::sendToCurrentClient(json_encode($return_error));
            return ;
        }
       
        
        //登录验证之后，对给定的请求进行响应，如果type不存在则报错  存在则根据方法处理参数给相应的用户返回数据
        if (method_exists($ws,$message_data['type'])) {
        	$ws->gateway($client_id,$message_data,$room_id);
        }else {
        	//对于方法名称不正确的请求，给予错误返回。
        	$return_error = error_ws(MSG_TYPE_ERROR,$message_data);
        	Gateway::sendToCurrentClient(json_encode($return_error));
        }
        
        
   }
   
   /**
    * 当客户端断开连接时
    * @param integer $client_id 客户端id
    */
   public static function onClose($client_id)
   {
       // debug
       echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";
       
       $ws = new ws_api();
       $ws->ws_logout($client_id);
       
       /*
       // 从房间的客户端列表中删除
       // 此处需要重写 让roomID被 clientid查询出来
       var_dump($_SESSION);
       if(isset($_SESSION['room_id']))
       {
           $room_id = $_SESSION['room_id'];
           self::delClientFromRoom($room_id, $client_id);
           // 广播 xxx 退出了
           if($all_clients = self::getClientListFromRoom($room_id))
           {
               $client_list = self::formatClientsData($all_clients);
               $new_message = array('type'=>'logout', 'from_client_id'=>$client_id, 'from_client_name'=>$_SESSION['client_name'], 'client_list'=>$client_list, 'time'=>date('Y-m-d H:i:s'));
               $client_id_array = array_keys($all_clients);
               Gateway::sendToAll(json_encode($new_message), $client_id_array);
           }
       }
       */
   }
   
  
  
   
   /**
    * 获得客户端列表
    * @todo 保存有限个
    */
   public static function getClientListFromRoom($room_id)
   {
       $key = "ROOM_CLIENT_LIST-$room_id";
       $store = Store::instance('room');
       $ret = $store->get($key);
       if(false === $ret)
       {
           if(get_class($store) == 'Memcached')
           {
               if($store->getResultCode() == \Memcached::RES_NOTFOUND)
               {
                   return array();
               }
               else 
               {
                   throw new \Exception("getClientListFromRoom($room_id)->Store::instance('room')->get($key) fail " . $store->getResultMessage());
               }
           }
           return array();
       }
       return $ret;
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
               if(false === $client_list)
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
   
      
   private function check_user_in_room() {
   		
   }
   
   private function initapi(){
   		include_once('ws_api.php');
   		$wsapi = new ws_api();
   }
}
