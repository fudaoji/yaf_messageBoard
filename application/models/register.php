<?php
	class RegisterModel extends ConfigModel{

		public function __construct(){
			$this->connectDb();
		}

		public function connectDb(){
			$db     = new MysqlModel();
			return $db->connect($this->dbhost, $this->dbuser, 
				$this->dbpwd, $this->dbname, $charset=$this->charset);
		}

		public function isUserExist($username){
			$db = new MysqlModel();
			$sql = "SELECT * FROM `user` WHERE `username`='".$username."'";
			return $db->fetch_all($sql);
		}

		public function getAllUser(){
			$db     = new MysqlModel();
			$sql = "SELECT * FROM `user`";
			return $db->fetch_all($sql);
		}

		public function addUser($username, $pwd, $sex, $realname, $email){
			$db     = new MysqlModel();
			$sql = "INSERT INTO `user` (`username`, `groupeID`, `password`, `realname`, `sex`, `email`) VALUES ('".$username.
					"', 1, '".md5($pwd)."', '".$realname."', '".intval($sex)."', '".$email."')";
			return $db->query($sql);
		}

		public function closeDb(){
			mysql_close();
		}
	}
?>