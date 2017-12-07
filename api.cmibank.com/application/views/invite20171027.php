<?php
if (@$_SERVER['ENVIRONMENT'] == 'production') {
    $domain = 'http://api.cmibank.com'; 
    $static_domain = 'http://static1.cmibank.com';
} elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
    $domain = 'http://api.cmibank.vip'; 
    $static_domain = 'http://static.cmibank.vip'; 
} else {
    $domain = 'http://api.cmibank.vip'; 
    $static_domain = 'http://static.cmibank.vip'; 
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="target-densitydpi=device-dpi, width=640px, user-scalable=no"  >
	<meta name="format-detection" telephone="no">
	<title>小伙伴喊你来赚钱啦！</title>
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/css/bind_invite_c9adbf9.css?232">
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/css/dialog.css">
	<script type="text/javascript" src="<?php echo $static_domain; ?>/common/js/jquery-1.7.1.min.js"></script>
</head>
<script type="text/javascript">
var wait=60;
function time(o) {
		if(wait == 60){
			$.ajax({
			type : 'POST',
			url : '<?php echo $domain;?>/login/send_phone_code',
			data:'phone=' + $('#account').val(),
			dataType : 'json',
			async : false,
			success : function(data){
			}
			});	
		}
        if (wait == 0) {
            o.removeAttribute("disabled");            
            o.value="免费获取验证码";
            wait = 60;
        } else { // www.jbxue.com
            o.setAttribute("disabled", true);
            o.value="(" + wait + ")";
            wait--;
            setTimeout(function() {
                time(o)
            },
			
            1000)
        }
    }

$(document).ready( function () {
$('#btn1').click ( function () {
    $.ajax({
    url: '<?php echo $domain;?>/login/verification_account',
    type: 'POST',
    data:'account=' + $('#account').val(),
    dataType: 'json',
    timeout: 1000,
    error: function(){
     alert('亲！网络不给力哦。请到网络好的地方试试');
    },
    success: function(data){
    	if(data.error == 1011){
    		showdownloadBg();
        }else if(data.error == 1004){
		  showBg();	
      	 $("#tishi2").html("您输入的账号格式不对哦！");
        }else if(data.error == 0){
        	$("#login1").css('display','none');
        	$("#login2").css('display','block');
			time(chongfa);
     	}
    }
    });
});

$('#btn2').click ( function () {
	$.ajax({
        type : 'POST',
        url : '<?php echo $domain;?>/login/reguser',
        data:'account=' + $('#account').val() + '&mobileVerify=' + $('#mobileVerify').val()+ '&password1=' + $('#password1').val()+ '&password2=' + $('#password1').val() + "&code=" + $('#code').val() + "&from=web&plat=invite",
        dataType : 'json',
        async : false,
        success : function(data){
        	if(data.error != 0){
				showBg();
        		$("#tishi2").html(data.msg);
            }else{
            	$("#login1").css('display','none');
            	$("#login2").css('display','none');
            	$("#download").css('display','block'); 
            }
        	
        }
    });	
});

})
function doDownload(){
    var u = navigator.userAgent;
    if (u.indexOf('iPhone') > -1){
            window.location='https://itunes.apple.com/us/app/易米融/id1291154070?l=zh&ls=1&mt=8';
    }else{
            window.location='http://api.cmibank.com/download?qudao=cmibank';
    }
}
</script>
<body>
<div class="head"><img  src="<?php echo $static_domain; ?>/images/yaoqing20171027.png?timestamp=<?php echo time(); ?>" width="100%"/></div>
<div class="login" id="login1" style="background: #ff5970">
	<p style="color: white">我正在易米融理财抢现金，你也来领取吧！</br>(目前活动已超200人)</p>
	<ul>
	<li><input class="radius" type="tel" id="account" name="account" placeholder="请输入您的手机号码"></li>
	<li class="nextBtn"><button class="btn1" id="btn1" style="color:#FBFBFB;background: #fbc74d">领取现金</button></li>
	</ul>
</div>
<div class="login" id="login2" style="display:none;background: #ff5970">
	<p style="color: white">我正在易米融理财抢现金，你也来领取吧！</br>(目前活动已超200人)</p>
	<ul>
	<li><input class="radius" id="password1" name="password1" placeholder="请设置你的登录密码"></li>
	<li><input id="mobileVerify" name="mobileVerify" type="number" placeholder="请输入您的短信验证码" style="width:70%;border-top-right-radius:0em;border-bottom-right-radius:0em;"><input style="border-top-left-radius:0em;border-bottom-left-radius:0em;" class="chongfa" onClick="time(this);"  readonly="readonly" type="bottom"  id="chongfa" value='获取验证码'></li>
	<li class="nextBtn"><button class="btn2" id="btn2" style="color:#FBFBFB;background: #fbc74d">领取现金</button></li>
	</ul>
</div>

<div class="download" id="download" style="display:none;">
	<P>
	恭喜你注册成功，离奖励更近一步！<br/>
	立即下载易米融理财，马上领取现金<br/>
	</P>
	<ul>
	<li class="nextBtn"><button class="radius" onClick="doDownload()">立即下载</button></li>
	</ul>
</div>
<div style="background: #ff5970">
<div class="shuoming"><img  src="<?php echo $static_domain; ?>/images/yaoqing20171027_2.png?timestamp=<?php echo time(); ?>" width="100%" />
<div   style="margin-top:50px; font-size:20px"  >
    <hr>
    <p> <center >Copyright<span style="font-size:1.5em; bottom:0">&copy;</span>万米财富有限公司ALL Rights Reserved</center> </p>
    <p><center>ICP备案号：沪ICP备15008416号</center></p>
</div></div>
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

<div id="downloadDialog"> 
<p class="close"><a href="#" onClick="closeBg();">X</a></p> 
<div class="showmessage">
	<ul>
	<li class="tishi1">提示</li>
	<li class="tishi2">您已是易米融注册用户，如果还没有安装易米融，那就赶紧下载吧！</li>
	<li class="nextBtn"><button style="width:180px;float: left;"  onClick="closeBg();">取消</button><button style="width:180px;float: right;" onClick="doDownload()">下载</button></li>
	</ul>
</div> 
</div>

<input  type ='hidden' value = "<?php echo $code; ?>" id='code'/>
<style>

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
position:fixed !important; /* 浮动对话框 */ 
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
position:fixed !important; /* 浮动对话框 */ 
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
//显示灰色 jQuery 遮罩层 
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
//关闭灰色 jQuery 遮罩 
function closeBg() { 
$("#fullbg,#dialog,#downloadDialog").hide(); 
} 
</script>
</body>
</html>