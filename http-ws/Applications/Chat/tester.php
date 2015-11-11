<?php


$c = new MongoClient('mongodb://119.254.100.239:27017'); //链接到 192.168.1.5:27017//27017端口是默认的。

$s = $c->joymedia->live_list;
		$where = array("activity_id"=> "A2015110200169");
		$set_array = array(	
								"zan_count"=> + 15,  // 自加模式 无需关心原来的数值
							);
		$res = $s->update($where,array('$inc' => $set_array));  // 用 $inc  进行连接

exit;

$insert_array = array(	"user_id" => "",
						"activity_id" => "",
						"zan_count"=> "",
						"dou_count"=> "",
						"danmu_count"=> "",
						"live_status"=> "1",
						"live_name"=> "",
						"live_avatar"=> "",
						"start_time"=> "",
						"end_time"=> ""
					);
$s->insert($insert_array);  

exit;

$s = $c->joymedia->dashan;


for($i=0; $i<3; $i++) {  
    //$s->insert( array( "content" => $i ) );  
}  

//$r = $s->find();
$r = $s->find();
$array = iterator_to_array($r);
$o = count($array);
$rand = rand(1, 999);
$key = $rand % $o;
var_dump($array);


function arrayToObject($e){
    if( gettype($e)!='array' ) return;
    foreach($e as $k=>$v){
        if( gettype($v)=='array' || getType($v)=='object' )
            $e[$k]=(object)arrayToObject($v);
    }
    return (object)$e;
}
 
function objectToArray($e){
    $e=(array)$e;
    foreach($e as $k=>$v){
        if( gettype($v)=='resource' ) return;
        if( gettype($v)=='object' || gettype($v)=='array' )
            $e[$k]=(array)objectToArray($v);
    }
    return $e;
}


