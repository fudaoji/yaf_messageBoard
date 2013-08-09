<?php

class LoginModel extends PostModel{
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

	public function checkPwd($username, $pwd){
		$db = new MysqlModel();
		$sql = "SELECT * FROM `user` WHERE `username`='".$username."' AND `password`='".$pwd."'";
		return $db->fetch_all($sql);
	}

	public function userInfo($username){
		$db     = new MysqlModel();
		$sql    = "SELECT * FROM `user` WHERE `username`='{$username}'";
		$result = $db->query($sql);
		$data   = $db->fetch_array($result);
		return $data;
	}

	/*public function isLogin(){
		session_start();
		if (!empty($_SESSION['username'])) {
			$info['user']  = $this->userInfo($_SESSION['username']);
			$info['statu'] = '退出';
			$info['url']   = '/index.php/login/logout';
		}else{
			$info['user']  = null;
			$info['statu'] = '登录';
			$info['url']   = '/index.php/login';
		}
		return $info; 
	}*/
}