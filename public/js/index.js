$(function(){
	$('#submit_button').click(function(){
		var title   = $('#title_box').val();
		var content = $('#content_box').val();
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
		$.post('/index.php/index/insertPost', {title:title,content:content}, function(data){
			if(data.status==true){
				alert(data.info);
				$('#title_box').val('');
				$('#content_box').val('');
				//window.scrollTo('0','0');
				location.reload();
			}else{
				alert(data.info);
				if (data.data==1) {
					location.href="/index.php/login/";
				};
			}
		}, 'json');
	});
});

function deletePost(id){
	if(!confirm('你确认要删除该留言?')){
		return false;
	}
	$.post('/index.php/index/deletePost', {id:id}, function(data){
		if(data.status==true){
			alert(data.info);
			location.reload();
		} else {
			alert(data.info);
		}
	}, 'json');
}
