<?php
/**
 * file: Register.php
 * fun:  Register 
 */
	class RegisterController extends BaseController{
		public function indexAction(){
			session_start();
			if (!empty($_SESSION['username'])) {
				header('Location: http://kxliuyan.com/index.php/error');
			}
		}

		public function valicodeAction(){
			$commonModel = new CommonModel();
			return $commonModel->validateCode();
		}

		public function saveAction(){
			session_start();
			$username   = $this->post('username');
			$pwd        = $this->post('pwd');
			$repeat_pwd = $this->post('repeat_pwd');
			$realname   = $this->post('realname');
			$sex		= $this->post('sex');
			$email		= $this->post('email');
			$code		= $this->post('code');

			if (!$username || !$pwd || !$repeat_pwd || !$realname || !$email || !$code) {
				$this->ajax(false, '请确认信息是否完整填写');
			}

			if ($_SESSION['valcode']!=$code) {
				$this->ajax(false, $this->messages[4], 4);
			}

			$registerModel = new RegisterModel();

			$result = $registerModel->isUserExist($username);
			if (!empty($result)) {
				$this->ajax(false, $this->messages[0], 0);
			}

			
			$result = $registerModel->addUser($username, $pwd, $sex, $realname, $email);
			if ($result) {
				$registerModel->closeDb();
				$this->ajax(true);
			} else {
				$this->ajax(false, '註冊失敗!');
			}
		}

		public function successAction(){
		}
	}
