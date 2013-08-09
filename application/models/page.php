<?php
/**
 *file: page.php
 *func: 翻页的功能在此实现
 * 
 */
class PageModel extends PostModel{
	public $postPerPage;
	public $postTotal;
	public $pageNums;

	public function __construct($id, $key){
		$this->postPerPage = 5;                         //定义每页显示的条目
		$this->postTotal = $this->getPostNum($id, $key); //留言总条数

		$this->pageNums  = ceil($this->postTotal/$this->postPerPage); //总页数
	}

	public function html($url, $postPerPage=20, $pageNav=1){
		/**
		 * 构造页面显示的翻页框
		 * @var int
		 * $postPerPage: 每页显示的条数
		 * $pageNav: 指定要到达的页码
		 */
		$url = '/index.php/index/index/'.$url;
		$str = '';
		if ($pageNav>=2) {
			$str .= "<a href='{$url}1'><span class='pageItem'>首页</span></a>
			<a href='{$url}".($pageNav-1)."'><span class='pageItem'>上一页</span></a>";
		}
		for($i=1; $i<$this->pageNums+1; ++$i){
			$str .= "<a href='{$url}{$i}'><span class='pageNum'>{$i}</span></a>";
		}
		if ($pageNav<$this->pageNums) {
			$str .= "<a href='{$url}".($pageNav+1)."'><span class='pageItem'>下一页</span></a>
			<a href='{$url}{$this->pageNums}'><span class='pageItem'>尾页</span></a>";
		}
		return $str;
		
	}

	public function getPosts($id=0, $key=null, $postPerPage=20, $pageNav=1){
		/**
		 * 获取符合要求的留言,id为0且key不为null时获取搜索留言；id为0且key为
		 * null时获取常规留言；id不为0且key为null时获取评论
		 * @id           int 
		 * @key          string
		 * @postPerPage  int
		 * @pageNav      int
		 */
		$posts = array();
		$pageNums = $this->pageNums;
		$postTotal  = $this->postTotal;

		//$pageNav = ($pageNav>$pageNums)? $pageNumes: $pageNav; //确定当前的页码
		$commonModel = new CommonModel();
		if ($pageNav>$pageNums && $pageNums>=1) {
			//$pageNav = $pageNums;
			$commonModel->getDisableView();
		}
		$from = ($postPerPage*$pageNav)-$postPerPage;
		$to   = ($pageNav<$pageNums)?$postPerPage:($postTotal-$from);
		if ($key!=null) {
			$posts = $this->getPostByKey($key, $from, $to);
		}else{
			$posts = $this->getSomePost($id, $from, $to);
		}
		return $posts;  //返回了留言条对象组成的数组
	}

}
?>