$(function() {
	$('#username').focus();
	$('#submit').click(function() {
		var username = $('#username').val();
		var pwd = $('#pwd').val();
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
		if (code.length != 4) {
			alert('請輸入正確的驗證碼');
			$('#code').val('');
			$('#code').focus();
			return false;
		};
		$.post('/index.php/login/login', {username:username,pwd:pwd,code:code}, function(data) {
			if (data.status == true) {
				location.href = '/index.php/login/success';
			} else {
				alert(data.info);
				switch(data.data){
					case 4:
						$('#code').val('');
						$('#code').focus();
						return false;break;
					case 2:
						$('#username').val('');
						$('#username').focus();
						return false;break;
					case 3:
						$('#pwd').val('');
						$('#pwd').focus();
						return false;break;
				}
			}
		}, 'json');
	});
});