<!doctype html>
<html class="clean-layout">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no,height=device-height" />
<meta content="yes" name="apple-mobile-web-app-capable">
<meta content="yes" name="apple-touch-fullscreen">
<meta content="black" name="apple-mobile-web-app-status-bar-style">
<meta content="telephone=no" name="format-detection">
<title>万米金融</title>
<link rel="stylesheet" type="text/css" href="https://lantouzi.com/css/mobile/clean_layout.css?v=1438249079334&_v=3.10.8">

<style>
#hd,.layout {display: none;}
</style>


<script type="text/javascript" src="https://lantouzi.com/js/jquery-1.11.1.min.js?_v=3.10.8"></script>
<script type="text/javascript" src="https://lantouzi.com/js/ltz.common.js?3&_v=3.10.8"></script></head>
<body class="page-jump2yeepay">
<style type="text/css">
	body,html {
		background: #fff !important;
	}
	footer.layout{
		display: none;
	}
</style>
<div id="bd">
		<p style="padding-top: 80px;color: #666666;font-size: 14px;text-align: center">正在前往易宝，完成相关操作，请稍等</p>
		<div class="jump2yeepay-wrap">
		</div>
		<form id="yeepaytocharge" method="post" action="<?php echo $yee_service?>">
			<input type="hidden" name="sign" value="<?php echo $sign ?>" />
			<input type="hidden" name="req" value='<?php echo $reg ?>' />
		</form>
</div>
<footer class="layout">
	<p>万米金融 <span>ICP</span></p>
</footer>

<script>
    document.getElementById("yeepaytocharge").submit();
</script>
</body>
</html>
