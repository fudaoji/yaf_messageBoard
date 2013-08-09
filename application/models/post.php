<?php
/**
 * file: Post.php
 *
 * 留言管理模型,一些操作的实现方法都在这儿
 * @author cosmos
 * @package Model
 * @since 1.0
 */

class PostModel extends ConfigModel {
	//protected $db;

	public function __construct(){
		$this->connectDb();
	}

	public function connectDb(){
		$db = new MysqlModel();
		return $db->connect($this->dbhost, $this->dbuser, 
			$this->dbpwd, $this->dbname, $charset=$this->charset);
	}

	/**
	 * 获取所有留言列表
	 *
	 * @access public
	 * @return string
	 */
	public function getPostList() {
		$db     = new MysqlModel();
		$sql    = "select * from posts where `pid`=0 order by `time` DESC";
		$data   = $db->query($sql);
		$posts  = array();
		while ($row=$this->$db->fetch_array($data)) {
			$posts[] = $row;
		}
		$this->mysqlClose($data);
		return $posts;
	}

	public function getSomePost($id=0, $from, $to){
		/**
		 * 根据参数$id的不同返回$to条评论或留言
		 * $id=0对应留言，$id>0对应id为$id的留言的评论		 
		 * */
		$order  = $id? 'ASC':'DESC';
		$db     = new MysqlModel();
		$sql    = "select * from posts where `pid`={$id} order by `time` {$order} Limit {$from}, {$to}";
		$data   = $db->query($sql);
		$posts  = array();
		while ($row=$db->fetch_array($data)) {
			$posts[] = $row;
		}
		$this->mysqlClose($data);
		return $posts;
	}

	/**
	 * 获取所有包含该关键词的留言
	 * @param string $key 搜素关键词
	 * @return array
	 */
	public function getPostByKey($key, $from, $to){
		$db     = new MysqlModel();
		$posts  = array();
		$sql    = "select * from `posts` where `pid`=0 and `title` like '%{$key}%' or `content` like '%{$key}%' order by `time` DESC Limit {$from},{$to} ";
		$result = $db->query($sql);
		if ($result) {
			while ($row=$db->fetch_array($result)) {
				$posts[] = $row;
			}
		}
		$this->mysqlClose($result);
		return $posts;
	}

	/**
	 * 写入数据表
	 *
	 * @access public
	 * @param string $title 留言标题
	 * @param string $content 留言内容
	 * @return boolean
	 */
	public function insertPost($pid, $title, $content, $author) {

		if ($pid!=0 && (!$content || !$author)) {
			return false;
		}elseif ($pid==0 && (!$title || !$content || !$author)) {
			return false;
		}

		$db     = new MysqlModel();

		$title = $this->htmltocode($title);
		$content = $this->htmltocode($content);

		$sql = "INSERT INTO `posts` (`id` ,`pid`, `title` ,`content` ,`author` ,`comment_num`, `time`) VALUES (NULL ,{$pid},  '{$title}', '{$content}', '{$author}', 0, CURRENT_TIMESTAMP)";
		$result = $db->query($sql);
		//post成功插入后同时更新用户的留言数
		$updateUserPost = $this->addPostNum($author); 
		
		return ($result && $updateUserPost);
	}

	public function addCommentNum($id){
		$db     = new MysqlModel();
		$sql    = "UPDATE `posts` SET `comment_num` = `comment_num`+1 WHERE `id` = {$id}";
		$result = $db->query($sql);
		return $result;
	}

	/**
	 * 删除留言
	 *
	 * @access public
	 * @param integer $id 留言ID
	 * @return boolean
	 */
	public function deletePost($id) {

		if (!$id) {
			return false;
		}

		$db     = new MysqlModel();
		$id     = trim($id);
		$sql    = "DELETE FROM `posts` WHERE `id` = {$id}";
		$result = $db->query($sql);

		return $result;
	}

	/**
	 * 根据ID获取留言内容
	 *
	 * @access public
	 * @param integer $id 留言ID
	 * @return array
	 */
	public function getContentById($id) {
		$db     = new MysqlModel();
		//parse params
		if (!$id) {
			return false;
		}

		$id       = trim($id);
		$sql      = "select * from `posts` where `id`={$id}";
		$result   = $db->query($sql);
		$postInfo = $db->fetch_array($result);
		$this->mysqlClose($data);
		return $postInfo;
	}

	/**
	 * 根据ID获取留言的所有评论内容
	 */
	public function getCommentsById($id) {
		$db     = new MysqlModel();
		if (!$id) {
			return false;
		}

		$id       = trim($id);
		$sql      = "select * from posts where pid={$id} order by `time` DESC";
		$result   = $db->query($sql);
		$postInfo = array();
		while ($row=$db->fetch_array($result)) {
			$postInfo[] = $row;
		}
		$this->mysqlClose($result);
		return $postInfo;
	}

	/**
	 * 编辑留言内容
	 *
	 * @access public
	 * @param integer $id 留言ID
	 * @param string $title 留言标题
	 * @param string $content 留言内容
	 * @return boolean
	 */
	public function editPost($id, $title, $content) {

		if (!$id || !$title || !$content) {
			return false;
		}

		$db = new MysqlModel();

		$id = trim($id);
		$title = $this->htmltocode($title);
		$content = $this->htmltocode($content);

		$sql = "UPDATE `posts` SET `title` = '{$title}', `content` = '{$content}' WHERE `id` ={$id}";

		$result = $db->query($sql);

		return $result;
	}

	public function getPostNum($id=0, $key=null){
		/**
		 * 根据参数$id，或$key的不同返回评论的总条数和留言的总条数
		 * $id=0对应留言数，$id>0对应id为$id的留言的总评论数
		 * @id     int
		 * @key    string
		 */
		$db     = new MysqlModel();
		$sql    = "select * from `posts` where `pid`={$id}";
		if ($key!=null) {
			$sql = "select * from `posts` where `pid`=0 and `title` like '%{$key}%' or `content` like '%{$key}%' ";
		}
		$result = $db->query($sql);
		return $db->num_rows($result);
	}

	/**
	 * 字符串格式化，保留用户留言的原样！
	 */
	public function htmltocode($content){
		$content = str_replace("\n", "<br/>", str_replace(" ", "&nbsp;", $content));
		return $content;
	}

	/**
	 * 更新用户的留言数
	 */
	public function addPostNum($username){
		$db    = new MysqlModel();
		$sql   = "UPDATE `user` SET `post_num` = `post_num`+1 WHERE `username` = '{$username}'";
		$result= $db->query($sql);
		return $result;
	}

	/**
	 * 关闭数据库连接
	 */
	public function mysqlClose($link=null){
		$db = new MysqlModel();
		$db->close($link);
	}
}