<?php
/**
 * Base.php
 *
 *  控制器基类
 */
class BaseController extends Yaf_Controller_Abstract {


    /**
     * 获取并分析$_GET数组某参数值
     *
     * 获取$_GET的全局超级变量数组的某参数值,并进行转义化处理，提升代码安全.注:参数支持数组
     * @access public
     * @param string  $string 所要获取$_GET的参数
     * @param string  $default_param 默认参数, 注:只有$string不为数组时有效
     * @return string $_GET数组某参数值
     *
     */
    
    public $messages = array(
        0 => '該用戶名已被註冊！',
        1 => '对不起,登录后才能发表留言!', 
        2 => '該用戶不存在',
        3 => '密码错误',
        4 => '验证码输入错误',
        5 => '留言标题或留言内容不能为空',
        6 => '对不起,登录后才能回覆',
        7 => '请输入搜索关键词',
        );
    public $siteURL = "http://kxliuyan.com/index.php";


    public function get($string, $default_param = null) {

        $param = $this->getRequest()->getParam($string, $default_param);
		$param = is_null($param) ? '' : (!is_array($param) ? trim($param) : $param);
        return $param;
    }

    /**
     * 获取并分析$_POST数组某参数值
     *
     * 获取$_POST全局变量数组的某参数值,并进行转义等处理，提升代码安全.注:参数支持数组
     * @access public
     * @param string $string    所要获取$_POST的参数
     * @param string $default_param 默认参数, 注:只有$string不为数组时有效
     * @return string    $_POST数组某参数值
     */
    public function post($string, $default_param = null) {

       $param = $this->getRequest()->getPost($string, $default_param);
       $param = is_null($param) ? '' : (!is_array($param) ? trim($param) : $param);
       return $param;
    }

	/**
     * 视图变量赋值操作
     *
     * @access public
     * @param mixted $keys 视图变量名
     * @param string $value 视图变量值
     * @return mixted
     */
    public function assign($keys, $value = null) {

        return $this->getView()->assign($keys, $value);
    }

    /**
     * Ajax调用返回
     *
     * 返回json数据,供前台ajax调用
     * @param array $data    返回数组,支持数组
     * @param string $info    返回信息, 默认为空
     * @param boolean $status    执行状态, 1为true, 0为false
     * @return string
     */
    public function ajax($status = 1, $info = null, $data = array()) {
        /**
         * 此处声明一下$data对应的内容：
         * $data=1时，表示未登录状态
         * $data=2时，表示用户名不存在
         * $data=3时，表示密码错误
         * $data=4时，表示验证出现问题
         */

        $result             = array();
        $result['status']   = $status;
        $result['info']     = !is_null($info) ? $info : '';
        $result['data']     = $data;

        header("Content-Type:text/html; charset=utf-8");

        exit(json_encode($result));
    }

    /**
     * 优雅输出print_r()函数所要输出的内容
     *
     * 用于程序调试时,完美输出调试数据,功能相当于print_r().当第二参数为true时(默认为:false),功能相当于var_dump()。
     * 注:本方法一般用于程序调试
     * @access public
     * @param array $data         所要输出的数据
     * @param boolean $option     选项:true或 false
     * @return array            所要输出的数组内容
     */
    public function dump($data, $option = false) {

        //当输出print_r()内容时
        if(!$option){
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        } else {
            ob_start();
            var_dump($data);
            $output = ob_get_clean();

            $output = str_replace('"', '', $output);
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);

            echo '<pre>', $output, '</pre>';
        }

        exit;
    }

    /**
     * trigger_error()的简化函数
     *
     * 用于显示错误信息. 若调试模式关闭时(即:DOIT_DEBUG为false时)，则将错误信息并写入日志
     * @access public
     * @param string $message 所要显示的错误信息
     * @return void
     */
    public function halt($message) {

    	return trigger_error($message, E_USER_ERROR);
    }

    /**
     * 获取当前运行程序的网址域名
     * @access public
     * @return string    网址(域名)
     */
    public static function getServerName() {

        //获取网址域名部分.
        $server_name = !empty($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : $_SERVER['SERVER_NAME'];
        $server_port = ($_SERVER['SERVER_PORT'] == '80') ? '' : ':' . (int)$_SERVER['SERVER_PORT'];

        //获取网络协议.
        $secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 1 : 0;

        return ($secure ? 'https://' : 'http://') . $server_name . $server_port;
    }

    /**
     * 用户调用非法页面时系统做出的应答函数
     * @return [type] [description]
     */
    public function getDisablePage(){
        $commonModel = new CommonModel();
        if (!$id) {
            $commonModel->getDisableView();
        }
    }

}