<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="target-densitydpi=device-dpi, width=640px, user-scalable=no"  >
	<meta name="format-detection" telephone="no">
	<script type="text/javascript" src="http://static1.cmibank.com/common/js/jquery-1.7.1.min.js"></script>
	<title>易米融理财</title>
	<script type="text/javascript">
	function doDownload(){
		var u = navigator.userAgent;
		if (u.indexOf('iPhone') > -1){
			window.location='http://a.app.qq.com/o/simple.jsp?pkgname=cn.app.cmibank';
		}else{
			window.location='http://api.cmibank.com/download?qudao=<?php echo isset($qudao)?$qudao:'jrtt'?>&wcg';
			}
	}
</script>
</head>
<body style="margin: 0;padding: 0;background: #57eafe;">
<div><img  src="http://static1.cmibank.com/images/xiazai01.jpg" width="100%"/></div>
<div style="height: 20%;text-align: center;">
	<button style="color:#f85e56;background: #fbda1c;font-size: 34px;border:0;border-radius:2em;height: 80px;width:90%" onClick="doDownload();">App下载</button>
</div>
<div><img  src="http://static1.cmibank.com/images/xiazai02.jpg" width="100%" /></div>
<div   style="margin-top:50px; font-size:20px"  >
    <hr>
    <p> <center >Copyright<span style="font-size:1.5em; bottom:0">&copy;</span>万米财富管理有限公司ALL Rights Reserved</center> </p>
    <p><center>ICP备案号：沪ICP备16014583号</center></p>
</div>
</body>
</html>