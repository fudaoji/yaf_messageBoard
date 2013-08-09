$(function() {
	$('#submit').click(function() {
		var username = $('#username').val();
		var pwd = $('#pwd').val();
		var repeat_pwd = $('#repeat_pwd').val();
		var realname = $('#realname').val();
		var sex = $('sex').val();
		var email = $('#email').val();
		var code = $('#code').val();


		if (username == '') {
			alert('请輸入用戶名');
			$('#username').focus();
			return false;
		}
		if (pwd == '') {
			alert('请输入密碼!');
			$('#pwd').focus();
			return false;
		}
		if (repeat_pwd != pwd) {
			alert('兩次輸入的密碼不一致，請重輸！');
			$('#repeat_pwd').focus();
			return false;
		};
		if (realname == '') {
			alert('請輸入真實姓名');
			$('#realname').focus();
			return false;
		};
		if (code.length != 4) {
			alert('請輸入正確的驗證碼');
			$('#code').focus();
			return false;
		};
		if (username.length < 4 || username.length > 30) {
			alert('用戶名長度必須在4到30之間！！');
			$('#username').focus();
			return false;
		};
		if (pwd.length < 6 || pwd.length > 30) {
			alert('密碼長度必須在6到30之間！！');
			$('#pwd').focus();
			return false;
		};
		if (realname.length < 2 || realname.length > 20) {
			alert('請輸入真實的姓名！！');
			$('#realname').focus();
			return false;
		};
		$.post('/index.php/register/save', {
			username: username,
			pwd: pwd,
			repeat_pwd: repeat_pwd,
			realname: realname,
			sex: sex,
			email: email,
			code: code
		}, function(data) {
			if (data.status == true) {
				location.href = '/index.php/register/success';
			} else {
				alert(data.info);
				if (data.data == 4) {
					$('#code').val('');
					$('#code').focus();
					return false;
				};
				if (data.data == 0) {
					$('#username').val('');
					$('#username').focus();
					return false;
				};
			}
		}, 'json');
	});
});