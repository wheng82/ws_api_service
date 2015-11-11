<?php

error_reporting(E_ALL^E_NOTICE^E_WARNING);

include_once(__DIR__.'/../Model/apimodel.php');

class userapi extends apimodel{

	public function __construct(){
		//$this->letvapi = APIMODEL::apifactory('letv');
		//$this->redisdb = APIMODEL::apifactory('redis');
		//$this->mongodb = APIMODEL::apifactory('mongo');
	}
	
	
	public function mongo_getuserinfo($uid) {
		$MONGO = APIMODEL::apifactory('mongo');
		//$c = new MongoClient('mongodb://119.254.100.239:27017');
		$MONGO->connent();
		//echo $this->mongodb->getmongoid($uid);
		$query = array( "uid" => $uid);
		$rr = $MONGO->findm('user',$query);
		if(empty($rr)){
			return FALSE;	
		} else {
			$array['avatar'] = $rr[0]['avatar'];
			$array['nickname'] = $rr[0]['nickname'];
			return $array;
		}
	}

}

?>