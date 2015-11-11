<?php
error_reporting(E_ALL^E_NOTICE^E_WARNING); 

class mongo_model {
    
/*** Mongodb类** examples:     
* $mongo = new mongo_model();  
* $mongo->selectDb("test_db");   
* 创建索引   
* $mongo->ensureIndexm("test_table", array("id"=>1), array('unique'=>true));   
* 获取表的记录   
* $mongo->countm("test_table");   
* 插入记录   
* $mongo->insertm("test_table", array("id"=>2, "title"=>"asdqw"));   
* 更新记录   
* $mongo->updatem("test_table", array("id"=>1),array("id"=>1,"title"=>"bbb"));   
* 更新记录-存在时更新，不存在时添加-相当于set   
* $mongo->updatem("test_table", array("id"=>1),array("id"=>1,"title"=>"bbb"),array("upsert"=>1));   
* 查找记录   
* $mongo->findm("c", array("title"=>"asdqw"), array("start"=>2,"limit"=>2,"sort"=>array("id"=>1)))   
* 查找一条记录   
* $mongo->findOnem("$mongo->findOne("ttt", array("id"=>1))", array("id"=>1));   
* 删除记录   
* $mongo->removem("ttt", array("title"=>"bbb"));   
* 仅删除一条记录   
* $mongo->removem("ttt", array("title"=>"bbb"), array("justOne"=>1));   
* 获取Mongo操作的错误信息   
* $mongo->getError();   
*/        
     
    //Mongodb连接     
    var $mongo;     
     
    var $curr_db_name;        
    var $error;     
    var $mongom;
    /**   
    * 构造函数   
    * 支持传入多个mongo_server(1.一个出问题时连接其它的server 2.自动将查询均匀分发到不同server)   
    *   
    * 参数：   
    * $mongo_server:数组或字符串-array("127.0.0.1:1111", "127.0.0.1:2222")-"127.0.0.1:1111"   
    * $connect:初始化mongo对象时是否连接，默认连接   
    * $auto_balance:是否自动做负载均衡，默认是   
    *   
    * 返回值：   
    * 成功：mongo object   
    * 失败：false   
    */     
    public function connent($connect=true, $auto_balance=true)     
    { 
	    $mongo_server=$this->mongom['host'];
        if (is_array($mongo_server))     
        {     
            $mongo_server_num = count($mongo_server);     
            if ($mongo_server_num > 1 && $auto_balance)     
            {     
                $prior_server_num = rand(1, $mongo_server_num);     
                $rand_keys = array_rand($mongo_server,$mongo_server_num);     
                $mongo_server_str = $mongo_server[$prior_server_num-1];     
                foreach ($rand_keys as $key)     
                {     
                    if ($key != $prior_server_num - 1)     
                    {     
                        $mongo_server_str .= ',' . $mongo_server[$key];     
                    }     
                }     
            }     
            else     
            {     
                $mongo_server_str = implode(',', $mongo_server);     
            }                  }     
        else     
        {     
            $mongo_server_str = $mongo_server;     
        }     
        try {     
            $this->mongo = new MongoClient($mongo_server, array('connect'=>$connect));
            $this->curr_db_name = $this->mongom['database'];  
        }     
        catch (MongoConnectionException $e)     
        {     
            $this->error = $e->getMessage();     
            return false;     
        }     
    }     
     
    public function getInstancem($mongo_server, $flag=array())     
    {     
        static $mongodb_arr;     
        if (emptyempty($flag['tag']))     
        {     
            $flag['tag'] = 'default';          }     
        if (isset($flag['force']) && $flag['force'] == true)     
        {     
            $mongo = new mongo_model($mongo_server);     
            if (emptyempty($mongodb_arr[$flag['tag']]))     
            {     
                $mongodb_arr[$flag['tag']] = $mongo;     
            }     
            return $mongo;     
        }     
        else if (isset($mongodb_arr[$flag['tag']]) && is_resource($mongodb_arr[$flag['tag']]))     
        {     
            return $mongodb_arr[$flag['tag']];     
        }     
        else     
        {     
            $mongo = new mongo_model($mongo_server);     
            $mongodb_arr[$flag['tag']] = $mongo;     
            return $mongo;                  }          }          
        
     
    /**   
    * 创建索引：如索引已存在，则返回。   
    *   
    * 参数：   
    * $table:表名   
    * $index:索引-array("id"=>1)-在id字段建立升序索引   
    * $index_param:其它条件-是否唯一索引等   
    *   
    * 返回值：   
    * 成功：true   
    * 失败：false   
    */     
    public function ensureIndexm($table, $index, $index_param=array())     
    {     
        $dbname = $this->curr_db_name;     
        $index_param['safe'] = 1;     
        try {     
            $this->mongo->$dbname->$table->ensureIndex($index, $index_param);     
            return true;     
        }     
        catch (MongoCursorException $e)     
        {     
            $this->error = $e->getMessage();     
            return false;     
        }     
    }     
     
    /**   
    * 插入记录   
    *   
    * 参数：   
    * $table:表名   
    * $record:记录   
    *   
    * 返回值：   
    * 成功：true   
    * 失败：false   
    */     
    public function insertm($table, $record)     
    {     
        $dbname = $this->curr_db_name;   
        try {     
            $this->mongo->$dbname->$table->insert($record);     
            return true;     
        }     
        catch (MongoCursorException $e)     
        {     
            $this->error = $e->getMessage();     
            return false;     
        }     
    }     
     
    /**   
    * 查询表的记录数   
    *   
    * 参数：   
    * $table:表名   
    *   
    * 返回值：表的记录数   
    */     
    public function countm($table)     
    {     
        $dbname = $this->curr_db_name;     
        return $this->mongo->$dbname->$table->count();     
    }     
     
    /**   
    * 更新记录   
    *   
    * 参数：   
    * $table:表名   
    * $condition:更新条件   
    * $newdata:新的数据记录   
    * $options:更新选择-upsert/multiple   
    *   
    * 返回值：   
    * 成功：true   
    * 失败：false   
    */     
    public function updatem($table, $condition, $newdata, $options=array())     
    {     
        $dbname = $this->curr_db_name;     
        //$options['safe'] = 1;     
        if (!isset($options['multiple']))     
        {     
            $options['multiple'] = 0;          }     
        try {     
            $this->mongo->$dbname->$table->update($condition, $newdata, $options);     
            return true;     
        }     
        catch (MongoCursorException $e)     
        {     
            $this->error = $e->getMessage();     
            return false;     
        }          }     
     
    /**   
    * 删除记录   
    *   
    * 参数：   
    * $table:表名   
    * $condition:删除条件   
    * $options:删除选择-justOne   
    *   
    * 返回值：   
    * 成功：true   
    * 失败：false   
    */     
    public function removem($table, $condition, $options=array())     
    {     
        $dbname = $this->curr_db_name;     
        $options['safe'] = 1;     
        try {     
            $this->mongo->$dbname->$table->remove($condition, $options);     
            return true;     
        }     
        catch (MongoCursorException $e)     
        {     
            $this->error = $e->getMessage();     
            return false;     
        }          }     
     
    /**   
    * 查找记录   
    *   
    * 参数：   
    * $table:表名   
    * $query_condition:字段查找条件   
    * $result_condition:查询结果限制条件-limit/sort等   
    * $fields:获取字段   
    *   
    * 返回值：   
    * 成功：记录集   
    * 失败：false   
    */     
    public function findm($table, $query_condition, $result_condition=array(), $fields=array())     
    {     
        $dbname = $this->curr_db_name;
       if(empty($query_condition)){
	        $cursor = $this->mongo->$dbname->$table->find();
        } else {
	        $cursor = $this->mongo->$dbname->$table->find($query_condition, $fields);
        }      
        if (!empty($result_condition['start']))     
        {     
            $cursor->skip($result_condition['start']);     
        }     
        if (!empty($result_condition['limit']))     
        {     
            $cursor->limit($result_condition['limit']);     
        }     
        if (!empty($result_condition['sort']))     
        {     
            $cursor->sort($result_condition['sort']);     
        }     
        $result = array();     
        try {     
            while ($cursor->hasNext())     
            {     
                $result[] = $cursor->getNext();     
            }     
        }     
        catch (MongoConnectionException $e)     
        {     
            $this->error = $e->getMessage();     
            return false;     
        }     
        catch (MongoCursorTimeoutException $e)     
        {     
            $this->error = $e->getMessage();     
            return false;     
        }     
        return $result;     
    }  
    
   	/***查询数量***/
   	public function findcount($table,$where)
   	{
	   	$dbname = $this->curr_db_name;
	   	if(empty($where)){
	        $result = $this->mongo->$dbname->$table->find().count();
        } else {
	        $result = $this->mongo->$dbname->$table->find($where).count();
        }     
        return $result; 
   	}    
     
    /**   
    * 查找一条记录   
    *   
    * 参数：   
    * $table:表名   
    * $condition:查找条件   
    * $fields:获取字段   
    *   
    * 返回值：   
    * 成功：一条记录   
    * 失败：false   
    */     
    public function findOnem($table, $condition, $fields=array())     
    {     
        $dbname = $this->curr_db_name;     
        return $this->mongo->$dbname->$table->findOne($condition, $fields);     
    }     
     
    /**   
    * 获取当前错误信息   
    *   
    * 参数：无   
    *   
    * 返回值：当前错误信息   
    */     
    public function getErrorm()     
    {     
        return $this->error;     
    }  
    /*Mongid*/
    public function getmongoid($uid) {
	    $cc = NEW MongoId($uid);
	    var_dump($cc);exit;
    }
}     
     
?> 