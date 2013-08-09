<?php
/**
 * file: Index.php
 *
 * 留言管理   Yaf_Controller_Abstract
 * @author guochen2
 * @package Controller
 */
class IndexController extends BaseController {

	/**
	 * 留言板首页
	 *
	 * @access public
	 * @留言列表 array 
	 */
	public function indexAction() {
		session_start();

		$url = 'index/page/';  //显示翻页块中要用到
		$commonModel = new CommonModel();
		$pageModel = new PageModel($id=0, $key=null);

		
		//每页显示的条数
		$postPerPage = $pageModel->postPerPage; 

		$pageNav = (int)$this->get('page');
		$pageNav = $pageNav?$pageNav:1 ;

		//存储视图模版的变量
		$info = array();
		$info['postList'] = $pageModel->getPosts(0,$key=null,$postPerPage, $pageNav);
		$arr              = $commonModel->isLogin();
		$info['user']     = $arr['user'];
		$info['statu']    = $arr['statu'];
		$info['url']	  = $arr['url'];
		$pagelist_html = $pageModel->html($url, $postPerPage, $pageNav); 
		$this->assign('info', $info);
		$this->assign('pagelist_html', $pagelist_html);
	}


	/**
	 * 发表留言
	 *
	 * @access public
	 * @return string
	 */
	public function insertPostAction() {

		session_start();
		if (empty($_SESSION['username'])) {
			$this->ajax(false, $this->messages[1], 1);
		}
		//get params
		$title    = $this->post('title');
		$content  = $this->post('content');
		$username = $_SESSION['username'];

		//parse params
		if (!$title || !$content) {
			$this->ajax(false, $this->messages[5], 5);
		}

		$postModel = new PostModel();

		$result = $postModel->insertPost(0,$title, $content, $username);

		if ($result) {
			$postModel->mysqlClose();
			$this->ajax(true, '留言成功');
		} else {
			$this->ajax(false, '对不起,操作失败！请重新提交');
		}

	}

	/**
	 * 回覆留言
	 *
	 * @access public
	 * @return string
	 */
	public function replyPostAction() {

		session_start();
		if (empty($_SESSION['username'])) {
			$this->ajax(false, $this->messages[6], 6);
		}
		//get params
		$content   = $this->post('content');
		$pid       = $_SESSION['pid'];
		//$ppid      = $_SESSION['ppid'];
		$username  = $_SESSION['username'];
		$title     = '';

		//parse params
		if (!$content) {
			$this->ajax(false, '請輸入內容');
		}
		if (!$username) {
			$this->ajax(false, '出現錯誤，請重新回覆');
		}

		$postModel = new PostModel();

		$result = $postModel->insertPost($pid, $title, $content, $username);
		$flag   = $postModel->addCommentNum($pid);
		if ($result && $flag) {
			$this->ajax(true, '回覆成功');
		} else {
			$this->ajax(false, $title); //'对不起,操作失败！请重新提交'
		}

	}

	/**
	 * 编辑留言
	 *
	 * @access public
	 * @return string
	 */
	public function editPostAction() {

		//get params
		$id = (int)$this->get('id');
		if (!$id) {
			$this->getDisableP();
		}

		//get post content
		$postModel = new PostModel();

		$info = $postModel->getContentById($id);

		$this->assign('data', $info);
	}

	/**
	 * ajax编辑留言
	 *
	 * @access public
	 * @return string
	 */
	public function handleEditPostAction() {

		//get params
		$id = (int)$this->post('id');
		$title = $this->post('title');
		$content = $this->post('content');

		if (!$id || !$title || !$content) {
			$this->ajax(false, '参数错误');
		}

		$postModel = new PostModel();

		$result = $postModel->editPost($id, $title, $content);

		if ($result) {
			$this->ajax(true, '修改成功');
		} else {
			$this->ajax(false, '对不起,操作失败！请重新提交');
		}
	}

	/**
	 * 删除留言
	 *
	 * @access public
	 * @return string
	 */
	public function deletePpostAction() {

		//get params
		$id = (int)$this->post('id');
		if (!$id) {
			$this->ajax(false, '对不起,你进行了非法调用');
		}

		$postModel = new PostModel();

		$result = $postModel->deletePost($id);

		if ($result) {
			$this->ajax(true, '删除成功');
		} else {
			$this->ajax(false, '对不起,操作失败！请得新提交');
		}
	}

	/**
	 * 显示留言的评论
	 * @return array 返回两个数组
	 */
	public function detailAction(){
		//get params
		session_start();
		$id = (int)$this->get('id');
		if (!$id) {
			$this->getDisablePage();
		}

		$commonModel = new CommonModel();
		$pageModel   = new PageModel($id, $key=null);
		
		//每页显示的条数
		$postPerPage = $pageModel->postPerPage; 
		$url         = 'detail/id/'.$id.'/page/';
		$pageNav     = (int)$this->get('page');
		$pageNav     = $pageNav?$pageNav:1 ;
		$pagelist_html    = $pageModel->html($url, $postPerPage, $pageNav);
		
		$_SESSION['pid']  = $id;
		$info             = array();
		$post             = $pageModel->getContentById($id);
		$info['comments'] = $pageModel->getPosts($id, $key=null, $postPerPage, $pageNav);
		$_SESSION['ppid'] = $post['pid']; //留言的父ID
		$info['pid']      = $id;  //保存评论的父ID
		$info['postInfo']    = $post;
		$pageInfo 		  = array(); 
		$pageInfo['pagelist']= $pagelist_html;
		$pageInfo['pageNav'] = $pageNav;
		$pageInfo['perPage'] = $postPerPage;

		$arr  = $commonModel->isLogin();
		$info['user']  = $arr['user'];
		$info['statu'] = $arr['statu'];
		$info['url']   = $arr['url'];
		$this->assign('info', $info);
		$this->assign('pageInfo', $pageInfo);
	}

	/**
	 * 搜索页面的控制器
	 * @留言数组 array [搜索到相关留言则显示，否则显示无查询结果]
	 */
	public function searchPostAction(){
		$info = array();
		$commonModel  = new CommonModel();

		$key = $this->get('key');
		if (!$key) {
			$this->getDisablePage();
		}else{
			$pageModel   = new PageModel($id=0, $key=$key);
			//每页显示的条数
			$postPerPage = $pageModel->postPerPage; 
			$url         = 'searchPost/key/'.$key.'/page/';
			$pageNav     = (int)$this->get('page');
			$pageNav     = $pageNav?$pageNav:1 ;
			$info['postList'] = $pageModel->getPosts(0, $key, $postPerPage, $pageNav);
			$pagelist_html = $pageModel->html($url, $postPerPage, $pageNav);
		}
		
		$arr  = $commonModel->isLogin();
		$info['user']  = $arr['user'];
		$info['statu'] = $arr['statu'];
		$info['url']   = $arr['url'];
		$info['pageList'] = $pagelist_html;
		$this->assign('info', $info);
		$this->assign('key', $key);
	}
	
}
