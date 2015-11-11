<?php
error_reporting(E_ALL^E_NOTICE^E_WARNING); 

class letv_model {
	//参数
	public $letvm;
	//生成签名
	public function secretkey($arr) {
		ksort($arr);  //
		foreach ($arr as $key => $value) {
			$str.=$key.$value;  //
		}
		$skey = md5($str.$this->letvm['letvkey']);
		return $skey;
	}
	//post数据
	public function curl_post($post_data) {
		$o="";
		foreach ($post_data as $k=>$v)
		{
		    $o.= "$k=".urlencode($v)."&";
		}
		$post_data=substr($o,0,-1);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL,$this->letvm['url']);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$result['res'] = curl_exec($ch);
		$result['httpcode'] = curl_getinfo($ch,CURLINFO_HTTP_CODE); 
		curl_close($ch);
		$line = $this->letvm['url']."?".$post_data;
		
		return $result;
		
	}
	//get提交参数	
	private function curl_get($post_data) {
		$o="";
		foreach ($post_data as $k=>$v)
		{
		    $o.= "$k=".urlencode($v)."&";
		}
		$post_data=substr($o,0,-1);
		$line = $this->url."?".$post_data;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $line);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);		
		//执行并获取HTML文档内容
		$result = curl_exec($ch);
		//释放curl句柄
		curl_close($ch);
		$check = $this->is_not_json($result);
		if ($check == true) {
			$data['code'] = "500";
			$data['message'] = "post array have something wrong";
			return json_encode($data);
		}else {
			$resarr = json_decode($result);
			$data['code'] = "0";
			$data['result'] = $resarr;
			$res = json_encode($data);
			return $res;
		}
	}
	//数据处理
	public function is_not_json($str){ 
	    return is_null(json_decode($str));
	}


}

?>