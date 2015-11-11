<?php 


class Error_Report {
	const	LIVE_ARGV_WRONG	= "10001";
	
	public function error_message($const) {
		$data['code'] = $const;
		$data['message'] = "abc";
		return json_encode($data);
	}
}