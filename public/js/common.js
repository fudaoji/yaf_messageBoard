function showDiv(obj) {
	obj.style.display = "block";
}

function hideDiv(obj) {
	obj.style.display = "none";
}

window.onscroll = function() {
	var oDiv = document.getElementById('scrollTo');
	var b = document.documentElement.scrollTop == 0 ? document.body.scrollTop : document.documentElement.scrollTop;
	var c = document.documentElement.scrollTop == 0 ? document.body.scrollHeight : document.documentElement.scrollHeight;

	if (b > c / 4) {
		showDiv(oDiv);
	} else {
		hideDiv(oDiv);
	};

}
function trim(str){
	return str.replace(/(^\s*)|(\s*)|(\s*$)/g, "");
}
$(function(){
	$('#searchKey').focus(function(){
		$('#searchKey').val('');
	});

	$('#searchImg').click(function(){
		var searchContent = $('#searchKey').val();
		if(searchContent==''){
			alert('请输入查询关键字！');
			$('#searchKey').focus();
			return false;
		}
		if (searchContent.length>10) {
			alert('关键字不得超过10个字！');
			$('#searchKey').focus();
			$('#searchKey').val('');
			return false;
		};
		searchContent = trim(searchContent);
		var url = '/index.php/index/index/searchPost/key/'+searchContent;
		location.href = url;
	});
});
