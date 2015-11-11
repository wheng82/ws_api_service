<?php
error_reporting(E_ALL^E_NOTICE^E_WARNING); 

class mysql_model {
	
	//原生语句只需要添加相应的过滤条件即可
	private $mysqlm;
	
	private $_select;
	
	private $_table;
	/*public function __construct()
	{
		$host = $this->mysqlm['host'];
		$username = $this->mysqlm['username'];
		$userpass = $this->mysqlm['password'];
		$connect=mysql_connect($host,$username,$userpass) or die("Unable to connect to the MySQL!");
		$database = $this->msqlm['database'];
		mysql_select_db($database,$connect);
	} */
	
	
	//基础查询类
	public function query($sql, $binds = FALSE, $return_object = NULL)
	{
		if ($sql === '')
		{
			//添加报错信息
		}
		elseif ( ! is_bool($return_object))
		{
			$return_object = ! $this->is_write_type($sql);
		}

		// Verify table prefix and replace if necessary
		if ($this->dbprefix !== '' && $this->swap_pre !== '' && $this->dbprefix !== $this->swap_pre)
		{
			$sql = preg_replace('/(\W)'.$this->swap_pre.'(\S+?)/', '\\1'.$this->dbprefix.'\\2', $sql);
		}

		// Compile binds if needed
		if ($binds !== FALSE)
		{
			$sql = $this->compile_binds($sql, $binds);
		}

		// Is query caching enabled? If the query is a "read type"
		// we will load the caching class and return the previously
		// cached query if it exists
		if ($this->cache_on === TRUE && $return_object === TRUE && $this->_cache_init())
		{
			$this->load_rdriver();
			if (FALSE !== ($cache = $this->CACHE->read($sql)))
			{
				return $cache;
			}
		}

		// Save the query for debugging
		if ($this->save_queries === TRUE)
		{
			$this->queries[] = $sql;
		}

		// Start the Query Timer
		$time_start = microtime(TRUE);

		// Run the Query
		if (FALSE === ($this->result_id = $this->simple_query($sql)))
		{
			if ($this->save_queries === TRUE)
			{
				$this->query_times[] = 0;
			}

			// This will trigger a rollback if transactions are being used
			if ($this->_trans_depth !== 0)
			{
				$this->_trans_status = FALSE;
			}

			// Grab the error now, as we might run some additional queries before displaying the error
			$error = $this->error();

			// Log errors
			log_message('error', 'Query error: '.$error['message'].' - Invalid query: '.$sql);

			if ($this->db_debug)
			{
				// We call this function in order to roll-back queries
				// if transactions are enabled. If we don't call this here
				// the error message will trigger an exit, causing the
				// transactions to remain in limbo.
				if ($this->_trans_depth !== 0)
				{
					do
					{
						$this->trans_complete();
					}
					while ($this->_trans_depth !== 0);
				}

				// Display errors
				return $this->display_error(array('Error Number: '.$error['code'], $error['message'], $sql));
			}

			return FALSE;
		}

		// Stop and aggregate the query time results
		$time_end = microtime(TRUE);
		$this->benchmark += $time_end - $time_start;

		if ($this->save_queries === TRUE)
		{
			$this->query_times[] = $time_end - $time_start;
		}

		// Increment the query counter
		$this->query_count++;

		// Will we have a result object instantiated? If not - we'll simply return TRUE
		if ($return_object !== TRUE)
		{
			// If caching is enabled we'll auto-cleanup any existing files related to this particular URI
			if ($this->cache_on === TRUE && $this->cache_autodel === TRUE && $this->_cache_init())
			{
				$this->CACHE->delete();
			}

			return TRUE;
		}

		// Load and instantiate the result driver
		$driver		= $this->load_rdriver();
		$RES		= new $driver($this);

		// Is query caching enabled? If so, we'll serialize the
		// result object and save it to a cache file.
		if ($this->cache_on === TRUE && $this->_cache_init())
		{
			// We'll create a new instance of the result object
			// only without the platform specific driver since
			// we can't use it with cached data (the query result
			// resource ID won't be any good once we've cached the
			// result object, so we'll have to compile the data
			// and save it)
			$CR = new CI_DB_result($this);
			$CR->result_object	= $RES->result_object();
			$CR->result_array	= $RES->result_array();
			$CR->num_rows		= $RES->num_rows();

			// Reset these since cached objects can not utilize resource IDs.
			$CR->conn_id		= NULL;
			$CR->result_id		= NULL;

			$this->CACHE->write($sql, $CR);
		}

		return $RES;
	}

	//分布式查询方法
	public function select($select = '*') {
		if (is_string($select))
		{
			$this->_select = explode(',', $select);
		}
		
	}	
	public function from($table){
		
	}
	public function where(){
		
	}
	public function or_where() {
		
	}
	public function where_in() {
		
	}
	public function where_not_in(){
		
	}
	public function join(){
		
	}
	public function limit(){
		
	}
	public function order_by(){
		
	}
	public function num_rows(){
		
	}
	public function free_result(){
		
	}
	public function last_query() {
		
	}
	public function insert(){
		
	}
	public function update(){
		
	}
	
	
	public function trans_begin(){
		
	}
	public function trans_status(){
		
	}
	public function trans_rollback(){
		
	}
	public function trans_commit(){
		
	}

	
}
$c = new mysql_model();
var_dump($c->select());
?>