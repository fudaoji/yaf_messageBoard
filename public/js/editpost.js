$(function(){
	$('#submit_button').click(function(){
		var title=$('#title_box').val();
		var content=$('#content_box').val();
		if(title==''){
			alert('请输入留言标题!');
			$('#title_box').focus();
			return false;
		}
		if(content==''){
			alert('请输入留言内容!');
			$('#content_box').focus();
			return false;
		}
		$.post('/index.php/index/handleEditPost', {id:'<?php echo $data['id']; ?>',title:title,content:content}, function(data){
			if(data.status==true){
				alert(data.info);
				location.href='/index.php';
			} else {
				alert(data.info);
			}
		}, 'json');
	});
});