<?php

/**
 *數據庫的鏈接及基本操作都在此定義
*/


class MysqlModel {
	var $querynum = 0;
	var $link;
	var $histories;

	var $dbhost;
	var $dbuser;
	var $dbpw;
	var $charset;
	var $pconn;
	var $tablepre;
	var $time;

	var $goneaway = 5;

	function connect($dbhost, $dbuser, $dbpw, $dbname = '', $charset = '', $pconn = 0, $tablepre='', $time = 0) {
		$this->dbhost = $dbhost;
		$this->dbuser = $dbuser;
		$this->dbpw = $dbpw;
		$this->dbname = $dbname;
		$this->charset = $charset;
		$this->pconn = $pconn;
		$this->tablepre = $tablepre;
		$this->time = $time;

		if($pconn) {
			if(!$this->link = mysql_pconnect($dbhost, $dbuser, $dbpw)) {
				$this->halt('Can not connect to MySQL server');
			}
		} else {
			if(!$this->link = mysql_connect($dbhost, $dbuser, $dbpw)) {
				$this->halt('Can not connect to MySQL server');
			}
		}

		if($this->version() > '4.1') {
			if($charset) {
				mysql_query("SET character_set_connection=".$charset.", 
					character_set_results=".$charset.", character_set_client=binary", 
					$this->link);
			}

			if($this->version() > '5.0.1') {
				mysql_query("SET sql_mode=''", $this->link);
			}
		}

		if($dbname) {
			mysql_select_db($dbname, $this->link);
		}

	}

	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}

	function result_first($sql) {
		$query = $this->query($sql);
		return $this->result($query, 0);
	}

	function fetch_first($sql) {
		$query = $this->query($sql);
		return $this->fetch_array($query);
	}

	function fetch_all($sql, $id = '') {
		$arr = array();
		$query = $this->query($sql);
		while($data = $this->fetch_array($query)) {
			$id ? $arr[$data[$id]] = $data : $arr[] = $data;
		}
		return $arr;
	}

	/*function cache_gc() {
		$this->query("DELETE FROM {$this->tablepre}sqlcaches WHERE expiry<$this->time");
	}*/

	function query($sql, $type = '', $cachetime = FALSE) {
		$func = ($type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query')) ? 'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($sql)) && $type != 'SILENT') {
			$this->halt('MySQL Query Error', $sql);
		}
		$this->querynum++;
		$this->histories[] = $sql;
		return $query;
	}

	function affected_rows() {
		//返回影響的函數
		return mysql_affected_rows($this->link);
	}

	function error() {
		//返回錯誤提示
		return (($this->link) ? mysql_error($this->link) : mysql_error());
	}

	function errno() {
		//返回錯誤代碼號
		return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
	}

	function result($query, $row) {
		//返回查詢資源集的結果中第$row+1條記錄
		$query = @mysql_result($query, $row);
		return $query;
	}

	function num_rows($query) {
		//Get number of rows in result
		$query = mysql_num_rows($query);
		return $query;
	}

	function num_fields($query) {
		//Get number of fields in result
		return mysql_num_fields($query);
	}

	function free_result($query) {
		//Free result memory
		return mysql_free_result($query);
	}

	function insert_id() {
		//Get the ID generated in the last query
		return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	function fetch_row($query) {
		//Get a result row as an enumerated array
		$query = mysql_fetch_row($query);
		return $query;
	}

	function fetch_fields($query) {
		//Get column information from a result and return as an object
		//以對象的形式存儲字段的屬性
		return mysql_fetch_field($query);
	}

	function version() {
		//Get MySQL server info
		return mysql_get_server_info($this->link);
	}

	/*function close() {
		//Close MySQL connection
		return mysql_close($this->link);
	}*/

	function halt($message = '', $sql = '') {
		$error = mysql_error();
		$errorno = mysql_errno();
		if($errorno == 2006 && $this->goneaway-- > 0) {
			$this->connect($this->dbhost, $this->dbuser, $this->dbpw, $this->dbname, $this->charset, $this->pconn, $this->tablepre, $this->time);
			$this->query($sql);
		} else {
			$s = '';
			if($message) {
				$s = "<b>$message</b><br />";
			}
			if($sql) {
				$s .= '<b>SQL:</b>'.htmlspecialchars($sql).'<br />';
			}
			$s .= '<b>Error:</b>'.$error.'<br />';
			$s .= '<b>Errno:</b>'.$errorno.'<br />';
			exit($s);
		}
	}

	function close($result=null){
		if ($result) {
			mysql_free_result($result);
		}
		mysql_close();
	}
}

?>
