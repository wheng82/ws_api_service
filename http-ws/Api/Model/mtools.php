<?php
/*新增apimodel列表*/

/*说明：此参数的键值跟以下的参数配置数组名必须一致*/
$factory['alipay']			='apimodel/alipay_model.php';  //支付宝模块
$factory['letv']			='apimodel/letv_model.php';		//乐视模块
$factory['mongo']			='dbmodel/mongo_model.php';		//mongodb模块
$factory['mysql']			='dbmodel/mysql_model.php';		//mysql模块
$factory['redis']			='dbmodel/redis_model.php';		//redis模块



/*letv参数配置*/
$letv['url']			= 'http://api.open.letvcloud.com/live/execute'; //乐视接口地址
$letv['ver']			= "3.0";										//乐视版本号
$letv['userid'] 		= "806634";										//商户号
$letv['letvkey']		= "936e416c2ff1222c2bcf274dd3ccf000";			//商户秘钥
$letv['create'] 		= "letv.cloudlive.activity.create";				//创建活动地址
$letv['modify'] 		= "letv.cloudlive.activity.modify";				//修改活动－－
$letv['search'] 		= "letv.cloudlive.activity.search";				//查询活动－－
$letv['stop'] 			= "letv.cloudlive.activity.stop";				//结束活动－－
$letv['config'] 		= "letv.cloudlive.activity.sercurity.config";	//安全活动设置
$letv['getPlayInfo'] 	= "letv.cloudlive.activity.getPlayInfo";		//查询录播视频的videoUnique
$letv['getUrl'] 		= "letv.cloudlive.activity.playerpage.getUrl";	//获取播放地址
$letv['getPushUrl'] 	= "letv.cloudlive.activity.getPushUrl";			//获取直播推流地址
$letv['getPushToken'] 	= "letv.cloudlive.activity.getPushToken";		//获取推流token
$letv['modifyCoverImg'] = "letv.cloudlive.activity.modifyCoverImg";		//修改直播活动封面



/*redis参数配置*/
$redis['host']				='127.0.0.1';	//连接ip
$redis['port']				='6379';	//端口号
$redis['dbzero']			='0';	//选择0库
$redis['dbeight']			='8';	//选择8库



/*mongo参数配置*/
$mongo['host']				='119.254.100.239:27017';	//连接地址
$mongo['admin']				='';	//用户名
$mongo['password']			='';	//密码
$mongo['database']			='joymedia';

/*mysql参数配置*/
$msql['host']				='';	//连接地址
$msql['username']			='';	//连接用户名
$msql['password']			='';	//连接密码
$msql['database']			='';	//连接数据库
$msql['dbprefix']			='';	//表名前缀
$msql['charset']			='';	//设置字符集
$msql['dbcollat']			='';	//设置字符集参数

/*alipay参数配置*/

$c = array('factory'=>$factory,'letv'=>$letv,'redis'=>$redis,'mongo'=>$mongo,'msqlm'=>$mysql);
echo json_encode($c);
?>