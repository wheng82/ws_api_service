<?php
error_reporting(E_ALL^E_NOTICE^E_WARNING); 

include_once(__DIR__.'/../Model/apimodel.php');

class concernapi extends apimodel {
		
		private $mongodb;
		
		public function __construct() {
			//$this->mongodb = APIMODEL::apifactory('mongo');
		}
		//**添加关注*****/
		public function create($userid,$concernid) {
			$MONGO = APIMODEL::apifactory('mongo');
			$jg = $this->check($userid,$concernid);
			if($jg == TRUE){
				$insert_array = array(	"guanzhu" => $userid,
										"beiguanzhu" => $concernid
									);
				$MONGO->connent();
				$res = $MONGO->insertm('relation',$insert_array);
				return $res;
			} else {
				return TRUE;//直接成功
			}
		}
		//*****查询是否关注过******/
		public function check($userid,$concernid) {
			$MONGO = APIMODEL::apifactory('mongo');
			$MONGO->connent();
			$query = array( "guanzhu" => $userid,"beiguanzhu" => $concernid);
			$rr = $MONGO->findm('relation',$query);
			if(empty($rr)){
				return TRUE;//需要插入
			} else {
				return FALSE;//直接成功
			}
		}
		//***查询关注数量****/
		public function get_concern_num($userid){
			$MONGO = APIMODEL::apifactory('mongo');
			$query = array('guanzhu'=>$userid);
			$MONGO->connent();
			$num = $MONGO->findcount('relation',$query);
			return $num;
		}
		//****查询关注列表*****/
		public function get_concern_list($userid) {
			$MONGO = APIMODEL::apifactory('mongo');
			$query = array('guanzhu'=>$userid);
			$MONGO->connent();
			$result= $MONGO->findm('relation',$query);
			return $result;
		}
		//*****查询粉丝数量*******/
		public function get_fconcern_num($userid){
			$MONGO = APIMODEL::apifactory('mongo');
			$query = array('beiguanzhu'=>$userid);
			$MONGO->connent();
			$num = $MONGO->findcount('relation',$query);
			return $num;
			
		}
		//*****查询粉丝列表*******/
		public function get_fconcern_list($userid) {
			$MONGO = APIMODEL::apifactory('mongo');
			$query = array('beiguanzhu'=>$userid);
			$MONGO->connent();
			$result= $MONGO->findm('relation',$query);
			return $result;
		}
}

?>