<?php $domain = 'http://api.cmibank.com';?>
<?php $static_domain = 'http://static1.cmibank.com';?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="target-densitydpi=device-dpi, width=640px, user-scalable=no"  >
	<meta name="format-detection" telephone="no">
	<title>小伙伴喊你来赚钱了，买多少送多少</title>
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/css/bind_invite_c9adbf9.css">
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/css/dialog.css">
	<script type="text/javascript" src="<?php echo $static_domain; ?>/common/js/jquery-1.7.1.min.js"></script>
</head>
<script type="text/javascript">
var wait=60;
function time(o) {
		if(wait == 60){
			$.ajax({
			type : 'POST',
			url : '<?php echo $domain;?>/index.php/login/send_phone_code',
			data:'phone=' + $('#account').val(),
			dataType : 'json',
			async : false,
			success : function(data){
			}
			});
		}
        if (wait == 0) {
            o.removeAttribute("disabled");            
            o.value="获取验证码";
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
<!--
$(document).ready( function () {
$('#btn1').click ( function () {
    $.ajax({
    url: '<?php echo $domain;?>/index.php/login/verification_account',
    type: 'POST',
    data:'account=' + $('#account').val(),
    dataType: 'json',
    timeout: 1000,
    error: function(){
     alert('亲！网络给力哦。请到网络好的地方试试');
    },
    success: function(data){
    	if(data.error == 1011){
		  showBg();
		  $("#tishi2").html("您已是易米融理财用户哦！");
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
        url : '<?php echo $domain;?>/index.php/login/reguser',
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



//-->
</script>
<body>
<div class="head"><img  src="<?php echo $static_domain; ?>/images/content_new_1.jpg" width="100%"/></div>
<div class="login" id="login1">
	<p style="color:#272727;">我正在易米融理财抢现金，你也快来领取吧！</br>最低12元，上不封顶！</p>
	<ul>
	<li><input class="radius" type="tel" id="account" name="account" placeholder="请输入您的手机号码"></li>
	<li class="nextBtn"><button class="btn1" id="btn1">领取现金</button></li>
	</ul>
</div>
<div class="login" id="login2" style="display:none;">
	<p style="color:#272727;">我正在易米融理财抢现金，你也快来领取吧！</br>最低12元，上不封顶！</p>
	<ul>
	<li><input class="radius" id="password1" name="password1" placeholder="请设置你的登录密码"></li>
	<li><input id="mobileVerify" name="mobileVerify" type="number" placeholder="请输入您的短信验证码" style="width:70%;border-top-right-radius:0em;border-bottom-right-radius:0em;"><input style="border-top-left-radius:0em;border-bottom-left-radius:0em;" class="chongfa" onclick="time(this);" type="bottom"  id="chongfa" value='获取验证码'></li>
	<li class="nextBtn"><button class="btn2" id="btn2">领取现金</button></li>
	</ul>
</div>

<div class="download" id="download" style="display:none;">
	<P>
	恭喜你注册成功，离奖励更近一步！<br/>
	立即下载易米融理财，马上领取现金<br/>
	上不封顶呦！
	</P>
	<ul>
	<li class="nextBtn"><button class="radius" onclick="window.location='http://a.app.qq.com/o/simple.jsp?pkgname=cn.app.cmibank'">立即下载</button></li>
	</ul>
</div>
<div style="background:#ea3f2c;">
<div class="shuoming"><img  src="<?php echo $static_domain; ?>/images/content_new_2.jpg" width="100%" /><img  src="<?php echo $static_domain; ?>/images/content_new_3.jpg" width="100%" /><img  src="<?php echo $static_domain; ?>/images/content_new_4.jpg" width="100%" /></div>
<div class="bottom">
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
//关闭灰色 jQuery 遮罩 
function closeBg() { 
$("#fullbg,#dialog").hide(); 
} 
</script>
</body>
</html>