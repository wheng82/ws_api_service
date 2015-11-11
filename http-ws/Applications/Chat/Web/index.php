<html><head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>websocket客户端</title>
  <script type="text/javascript">
  //WebSocket = null;
  </script>
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/style.css" rel="stylesheet">
  <!-- Include these three JS files: -->
  <script type="text/javascript" src="/js/swfobject.js"></script>
  <script type="text/javascript" src="/js/web_socket.js"></script>
  <script type="text/javascript" src="/js/json.js"></script>
  <script type="text/javascript" src="/js/jquery.min.js"></script>

  <script type="text/javascript">
  	
    if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
    WEB_SOCKET_SWF_LOCATION = "/swf/WebSocketMain.swf";
    WEB_SOCKET_DEBUG = true;
    var ws, client_list={},timeid, reconnect=false;
	    
    function init() {
    	
       // 创建websocket
    	ws = new WebSocket("ws://"+document.domain+":8001");
    	// 调整连接与断开按钮的作用
    	$("button[id='disconnect']").removeAttr('disabled');
    	$("button[id='connect']").attr('disabled',"ture");
    	
    	
      // 当socket连接打开时，输入用户名
      //  这里就是被固化的与连接一通请求的login和relogin操作
      ws.onopen = function() {
    	  timeid && window.clearInterval(timeid);
      };
      // 当有消息时根据消息类型显示不同信息
      ws.onmessage = function(e) {
    	console.log(e.data);
        var data = JSON.parse(e.data);
        switch(data['type']){
              // 服务端ping客户端
              case 'ping':
            	ws.send(JSON.stringify({"type":"pong"}));
                break;;
              case 'ws_login':
              	clean_dialog();
              	say(data['client_id'], data['user_id'],  data['user_id']+' 加入了聊天室', data['time']);
              	flush_client_list(data['client_list']);
              	console.log(data['user_id']+"登录成功");
              	break;
              case 'logout':
            	  //{"type":"logout","client_id":xxx,"time":"xxx"}
            	  say(data['from_client_id'], data['from_client_name'], data['from_client_name']+' 退出了', data['time']);
          		 flush_client_list(data['client_list']);
          	  default:
          	  		report((data).toSource());
          	  		//say("1","2",(data).toSource());
          	  		break;
          		
        }
      };
      ws.onclose = function() {
    	  flush_client_list(client_list);
    	  clean_dialog();
      };
      ws.onerror = function() {
    	  console.log("出现错误");
      };
    }


	// 提交对话
	function onLogin() {
	  var user_id = document.login.user_id.value;
	  var room_id = document.login.room_id.value;
	  
	  ws.send(JSON.stringify({"type":"ws_login","user_id":user_id,"room_id":room_id}));
	  $('#dialog').scrollTop();
	}
    // 提交对话
    function onSubmit() {
      var json = document.wsform.json.value;
      //var value = document.wsform.val.value;
      var to_client_id = $("#client_list option:selected").attr("value");
      var room_id = $("input[name='room_id']").val();
      //json = json.replace(/\"/g, '');
      //alert(JSON.stringify({"json":json}));
      ws.send(JSON.stringify({"json":json}));
      $('#dialog').scrollTop();
      //$('#dialog').scrollTop($('#dialog').scrollTop());
    }

    // 刷新用户列表框
    function flush_client_list(client_list){
    	var userlist_window = $("#userlist");
    	var client_list_slelect = $("#client_list");
    	userlist_window.empty();
    	client_list_slelect.empty();
    	userlist_window.append('<h4>在线用户</h4><ul>');
    	client_list_slelect.append('<option value="all" id="cli_all">所有人</option>');
    	client_list_slelect.append('<option value="self" id="cli_all">自己</option>');
    	for(var p in client_list){
    		userlist_window.append('<li id="'+client_list[p]['client_id']+'">'+client_list[p]['user_id']+'</li>');
    		client_list_slelect.append('<option value="'+client_list[p]['client_id']+'">'+client_list[p]['user_id']+'</option>');
        }
    	$("#client_list").val(select_client_id);
    	userlist_window.append('</ul>');
    }
    
    function clean_dialog() {
    	$("#dialog").empty();
    }

    // 发言
    function say(from_client_id, from_client_name, content, time){
    	$("#dialog").append('<div class="speech_item"><img src="http://lorempixel.com/38/38/?'+from_client_id+'" class="user_icon" /> '+from_client_name+' <br> '+time+'<div style="clear:both;"></div><p class="triangle-isosceles top">'+content+'</p> </div>');
    }
    
    function report(content){
    	$("#dialog").append('<div class="speech_item"> <br> <div style="clear:both;"></div><p class="triangle-isosceles top">'+decodeUTF8(content)+'</p> </div>');
    }
    
    function disconnect_ws(){
    	if (ws != null) {
    		ws.close();
    	}
    	// 调整连接与断开按钮的作用
    	$("button[id='connect']").removeAttr('disabled');
    	$("button[id='disconnect']").attr('disabled',"ture");
    }
    
    function decodeUTF8(str){  
    	return str.replace(/(\\u)(\w{4}|\w{2})/gi, function($0,$1,$2){  
	        return String.fromCharCode(parseInt($2,16));  })}; 

    $(function(){
    	select_client_id = 'all';
	    $("#client_list").change(function(){
	        select_client_id = $("#client_list option:selected").attr("value");
	    });
	});
  </script>
</head>
<body>
	<center><h1 class="page-header">Websocket Client Tester</h1></center>
	<div class="row placeholders">
	
    <div class="container-fluid">
    	<div class="row">
    		<div class="col-md-2 column"></div>
    			  
    		<div class="col-md-8 column">
    			<div class="thumbnail">
    				<label class="col-sm-4 control-label">目前本测试客户端只支持firefox浏览器</label>
    				 <!--<input type="text" id="token" class="form-control" placeholder="token" name="token" value="" aria-describedby="basic-addon1">-->
    				<button class="btn btn-default" id="connect" onclick="init()" type="button">连接</button> 
    				<button class="btn btn-default" id="disconnect" onclick="disconnect_ws()" type="button">断开</button>
    			</div>
    		</div>
    	</div>
    	
    	
	    <div class="row">
	        <div class="col-md-2 column">
		        <br><br>
	    	</div>
	    	  
		 <div class="col-md-8 column">
		 	<div class="thumbnail">
		 	<form name="login" onsubmit="onLogin(); return false">
		 		 <div class="input-group">
		 		   <span class="input-group-addon" id="basic-addon1">user_id</span>
		 		   <input type="text" class="form-control" placeholder="user_id" name="user_id" value="" aria-describedby="basic-addon1">
		 		   <span class="input-group-addon" id="basic-addon1">room_id</span>
		 		   <input type="text" class="form-control" placeholder="room_id" name="room_id" value="" aria-describedby="basic-addon1">
		 		 </div>
		 		 <input type="submit" class="btn btn-default" value="登录" />
		 	</form>
		 	 <form name="wsform">
		 	 <div class="input-group">
		 	   <span class="input-group-addon" id="basic-addon1">json</span>
		 	   <input type="text" class="form-control" placeholder="type" name="json" value="" aria-describedby="basic-addon1">
		 	 </div>
		 	</form>
		 	<form onsubmit="onSubmit(); return false;">
		 	     <select style="margin-bottom:8px" id="client_list">
		 	         <option value="all">所有人</option>
		 	         <option value="self">自己</option>
		 	     </select>
		 	     <input type="submit" class="btn btn-default" value="提交请求" /></div>
		 	</form> 
		 	</div>
		 </div>
		</div>
		     
		    <div class="row">
		    		<div class="col-md-2 column"></div>
		    		<div class="col-md-6 column"> 
			           <div class="thumbnail">
			           <div class="caption" style=" overflow:auto; height:300px;" id="dialog"></div>
			           </div>
		           </div>
		           <div class="col-md-2 column">
		              <div class="thumbnail">
		                  <div class="caption" id="userlist"></div>
		              </div>
		           </div>
		    </div>
               
      </div>
	</div>
    <script type="text/javascript">var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F7b1919221e89d2aa5711e4deb935debd' type='text/javascript'%3E%3C/script%3E"));</script>
</body>
</html>
