<?php
/**
 * 定义了公共类，如验证码，翻页等功能
 */

class CommonModel extends LoginModel{
	public function validateCode(){
		session_start();  //将验证码上的内容保存在session上
	 	for ($i=0; $i < 4; $i++) {
	 		/*生成随机数*/
			 $rand .= dechex(rand(1, 15));
		}

		$_SESSION['valcode'] = $rand;

		//创建图片
		$width = 80; $height = 30;
		$img = imagecreatetruecolor($width, $height);

		//设置颜色
		$bgColor = imagecolorallocate($img, 0, 0, 0);
		$fontColor = imagecolorallocate($img, 255, 255, 255);

		//将随机数写入到图片中
		imagestring($img, rand(4,6), rand(10, 40), rand(2, 8), $rand, $fontColor);

		//画干扰线
		/*for ($i = 0; $i < 2 ; $i++) {
			$randColor = imagecolorallocate($img, rand(10,255), rand(10,255), rand(10,255));
			imageline($img, 0,rand(0,30), 100, rand(0,30), $randColor);
		}*/

		//画噪点
		//imagesetpixel(图像句柄,x, y, color)
		for ($i = 0; $i < 200; $i++) {
			$randColor = imagecolorallocate($img, rand(10,255), rand(10,255), rand(10,255));
			imagesetpixel($img, rand()%100, rand()%30, $randColor);
		}

		//输出图像
		header("Content-Type: image/jpeg");
		imagejpeg($img);
	}

	public function getDisableView(){
        header('Location: http://kxliuyan.com/index.php/error/illegal');
        exit();
    }

    public function isLogin(){
    	session_start();
    	$info = array();
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
    }
}