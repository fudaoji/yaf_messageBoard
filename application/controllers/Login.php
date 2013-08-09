<?php
	class LoginController extends BaseController{
		public function indexAction(){

		}

		public function loginAction(){
			session_start();
			$username   = $this->post('username');
			$pwd        = $this->post('pwd');
			$code		= $this->post('code');

			if (!$username || !$pwd || !$code) {
				$this->ajax(false, '请确认信息是否完整填写');
			}

			if ($_SESSION['valcode']!=$code) {
				$this->ajax(false, $this->messages[4], 4);
			}

			$loginModel = new LoginModel();

			$result = $loginModel->isUserExist($username);
			if (empty($result)) {
				$this->ajax(false, $this->messages[2], 2);
			}else{
				$result = $loginModel->checkPwd($username, md5($pwd));
				if (empty($result)) {
					$this->ajax(false, $this->messages[3], 3);
				}else{
					$_SESSION['username'] = $username;
					$this->ajax(true);
				}
			}
		}

		public function successAction(){
		}

		public function logoutAction(){
			session_start();
			$_SESSION['username'] = null;
			header('Location: /index.php');
		}

	}
?>