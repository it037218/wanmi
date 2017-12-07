<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="target-densitydpi=device-dpi, width=640px, user-scalable=no"  >
	<meta name="format-detection" telephone="no">
	<title>易米融理财</title>
	<script type="text/javascript" src="http://static1.cmibank.com/common/js/jquery-1.7.1.min.js"></script>
	<style type="text/css">
	body,nav,dl,dt,dd,p,h1,h2,h3,h4,ul,ol,li,input,button,textarea,footer{margin:0;padding:0}
	header,footer,article,section,nav,menu{display:block;clear:all}
	*{box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box}
	input::-webkit-outer-spin-button,input::-webkit-inner-spin-button{-webkit-appearance:none!important}
	body{font-family:"Helvetica Neue",Helvetica,STHeiTi,Arial,sans-serif;font-size:16px;color:#333;-webkit-text-size-adjust:none;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;min-width:320px;background:#ffbb00;font-family:"微软雅黑"}
	ul,ol{list-style:none}
	a{text-decoration:none;color:#1a1a1a}
	a:hover,a:active,a:focus{color:#1c5aa2;text-decoration:none}
	a:active{color:#aaa}
	img{vertical-align:middle;border:0;-ms-interpolation-mode:bicubic}
	button,input,select,textarea{font-family:"Helvetica Neue",Helvetica,STHeiTi,Arial,sans-serif;font-size:12px;vertical-align:middle;-webkit-appearance:none;outline:0}
	button,input[type=button],input[type=reset],input[type=submit]{cursor:pointer;-webkit-appearance:none;-moz-appearance:none}
	input:focus:-moz-placeholder,input:focus::-webkit-input-placeholder{color:transparent}
	button::-moz-focus-inner,input::-moz-focus-inner{padding:0;border:0}
	.chongfa{width:35%;background:#fff0c7;color:#ff8128;text-align:center;border:1px #ffd59c solid; height:83px; font-size:25px;font-family:"微软雅黑"}
	.text_input{width:100%;padding: 10px 10px 10px 40px;border:1px #ffd59c solid;height:83px;border-radius:2em;font-size:30px;font-family:"微软雅黑";}
	.nextBtn button{width:100%;background:#fbda1c;color:#692b00;text-align:center;border:0;font-weight:700;margin-bottom:37px;border-radius:1.1em; height:84px; font-size:35px}
	.nextBtn button.disabled{background:#ededed;color:#999}
	button,input,select,textarea{font-family:"Helvetica Neue",Helvetica,STHeiTi,Arial,sans-serif;font-size:12px;vertical-align:middle;-webkit-appearance:none;outline:0}
	.showmessage ul{margin: 0 10% 0 10%;}
	.showmessage li{border-radius:30px; font-size:21px}
	.showmessage li.nextBtn button{width:100%;background:#ff6600;color:#fff;text-align:center;border:0;font-weight:700;margin-bottom:37px;border-radius:15px; height:67px; font-size:30px;margin:10px auto;}
	.tishi1{ color:#ea3f2c; width:45px; margin:10px auto;}
	.tishi2{ color:#272727; width:315px; margin:10px auto;}
	#fullbg { 
		background-color:gray; 
		left:0; 
		opacity:0.5; 
		position:absolute; 
		top:0; 
		z-index:3; 
		filter:alpha(opacity=50); 
		-moz-opacity:0.5; 
		-khtml-opacity:0.5; 
	} 
	#downloadDialog { 
		border:1px solid #ffd200; 
		height:250px; 
		left:42%; 
		margin:-200px 0 0 -200px; 
		padding:1px; 
		position:fixed !important;
		position:absolute; 
		top:40%; 
		width:500px; 
		z-index:5; 
		border-radius:5px; 
		background:#ffd200;
		border-radius:16px;
		display:none;
	} 
	#downloadDialog p { 
		margin:0 0 12px; 
		height:24px; 
		line-height:24px; 
		background:#ffd200; 
	} 
	#downloadDialog p.close { 
		text-align:right; 
		padding-right:10px; 
	} 
	#downloadDialog p.close a { 
		color:#fff; 
		text-decoration:none; 
	} 
	#dialog { 
		border:1px solid #ffd200; 
		height:250px; 
		left:42%; 
		margin:-200px 0 0 -200px; 
		padding:1px; 
		position:fixed !important; 
		position:absolute; 
		top:40%; 
		width:500px; 
		z-index:5; 
		border-radius:5px; 
		background:#ffd200;
		border-radius:16px;
		display:none;
	} 
	#dialog p { 
		margin:0 0 12px; 
		height:24px; 
		line-height:24px; 
		background:#ffd200; 
	} 
	#dialog p.close { 
		text-align:right; 
		padding-right:10px; 
	} 
	#dialog p.close a { 
		color:#fff; 
		text-decoration:none; 
	}
	</style>
	<script type="text/javascript">
	var wait=60;
	function time(o) {
		if($('#account').val()==''){
			alert('请输入您的手机号码！');
			return;
		}
		if($('#password').val()==''){
			alert('请输入您的登录密码！');
			return;
		}
		if(wait == 60){
			$.ajax({
			type : 'POST',
			url : 'http://api.cmibank.com/login/send_phone_code',
			data:'phone=' + $('#account').val(),
			dataType : 'json',
			async : false,
			success : function(data){
				if(data.error != 0){
		        	if(data.error == 1006){
		        		showdownloadBg();
			        }else{
						showBg();
		        		$("#tishi2").html(data.msg);
				        }
	            }
			}
			});	
		}
        if (wait == 0) {
            o.removeAttribute("disabled");            
            o.value="免费获取验证码";
            wait = 60;
        } else {
            o.setAttribute("disabled", true);
            o.value="(" + wait + ")";
            wait--;
            setTimeout(function() {
                time(o)
            },
			
            1000)
        }
    }

	function showBg() { 
		var bh = $("body").height(); 
		var bw = $("body").width(); 
		$("#fullbg").css({ 
			height:bh, 
			width:bw, 
			display:"block" 
		}); 
		$("#dialog").show(); 
	}
	function closeBg() { 
		$("#fullbg,#dialog,#downloadDialog").hide(); 
	} 
	function showdownloadBg() { 
		var bh = $("body").height(); 
		var bw = $("body").width(); 
		$("#fullbg").css({ 
		height:bh, 
		width:bw, 
		display:"block" 
		}); 
		$("#downloadDialog").show(); 
	}
	$(document).ready( function () {
		$('#btn2').click ( function () {
			if($('#account').val()==''){
				alert('请输入您的手机号码！');
				return;
			}
			if($('#password').val()==''){
				alert('请输入您的登录密码！');
				return;
			}
			if($('#mobileVerify').val()==''){
				alert('请输入您的短信验证码！');
				return;
			}
			$.ajax({
		        type : 'POST',
		        url : 'http://api.cmibank.com/login/regQudaoUser',
		        data:'account=' + $('#account').val() + '&mobileVerify=' + $('#mobileVerify').val()+ '&password=' + $('#password').val() + "&from=web&plat="+$('#qudao').val()+"",
		        dataType : 'json',
		        async : false,
		        success : function(data){
		        	if(data.error != 0){
			        	if(data.error == 1006){
			        		showdownloadBg();
				        }else{
							showBg();
			        		$("#tishi2").html(data.msg);
					        }
		            }else{
		            	showdownloadBg();
		            	$("#tishi_reg").html("恭喜你注册成功,如果还没有安装易米融，那就赶紧下载吧！");
		            }
		        }
		    });	
		});
	})

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
<body style="margin: 0;padding: 0;background: #0cc991;">
<div><img  src="http://static1.cmibank.com/images/zhuce01.jpg" width="100%"/><input type="hidden" value="<?php echo isset($qudao)?$qudao:''?>" id="qudao"></div>
<div style="height: 20%;text-align: center;" id="login">
	<ul style="list-style: none;margin:0 5% 0 5%">
	<li style="margin-top:20px;"><input class="text_input" id="account" name="account" placeholder="请输入您的手机号码"></li>
	<li style="margin-top:20px;"><input class="text_input" id="password" name="password" placeholder="请设置您的登录密码"></li>
	<li style="margin-top:20px;"><input id="mobileVerify" class="text_input" name="mobileVerify" type="number" placeholder="请输入您的短信验证码" style="width:65%;border-top-right-radius:0em;border-bottom-right-radius:0em;"><input style="border-top-left-radius:0em;border-bottom-left-radius:0em;border-bottom-right-radius:2em;border-top-right-radius:2em" class="chongfa" onClick="time(this);" type="bottom"  id="chongfa" value='免费获取验证码'></li>
	<li style="margin-top:20px;" class="nextBtn"><button class="btn2" id="btn2">立即注册</button></li>
	</ul>
</div>
<div><img  src="http://static1.cmibank.com/images/zhuce02.jpg" width="100%" /></div>
<div   style="margin-top:50px; font-size:20px"  >
    <hr>
    <p> <center >Copyright<span style="font-size:1.5em; bottom:0">&copy;</span>万米财富管理有限公司ALL Rights Reserved</center> </p>
    <p><center>ICP备案号：沪ICP备16014583号</center></p>
</div>
<div id="downloadDialog"> 
	<p class="close"><a href="#" onClick="closeBg();">X</a></p> 
	<div class="showmessage">
		<ul>
		<li class="tishi1">提示</li>
		<li class="tishi2" id="tishi_reg">您已是易米融注册用户，如果还没有安装易米融，那就赶紧下载吧！</li>
		<li class="nextBtn"><button style="width:180px;float: left;"  onClick="closeBg();">取消</button><button style="width:180px;float: right;" onClick="doDownload();">下载</button></li>
		</ul>
	</div> 
</div>
<div id="fullbg"></div> 
<div id="dialog"> 
	<p class="close"><a href="#" onClick="closeBg();">X</a></p> 
	<div class="showmessage">
		<ul>
		<li class="tishi1">提示</li>
		<li class="tishi2" id="tishi2">请输入正确的手机号码和验证码</li>
		<li class="nextBtn"><button onClick="closeBg();">确定</button></li>
		</ul>
</div> 
</div>
</body>
</html>