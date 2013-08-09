$(function() {
	$('#submit_button').click(function() {
		var content = $('#content_box').val();
		if (content == '') {
			alert('请输入回覆内容!');
			$('#content_box').focus();
			return false;
		}
		$.post('/index.php/index/replyPost', {
			content: content
		}, function(data) {
			if (data.status == true) {
				alert(data.info);
				$('#content_box').val('');
				location.reload();
			} else {
				alert(data.info);
				if (data.data == 6) {
					location.href = "/index.php/login/";
				};
			}
		}, 'json');
	});
});
$(function() {
	$('#huifu').click(function() {
		$('#content_box').focus();
	})
})