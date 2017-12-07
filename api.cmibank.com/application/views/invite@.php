<?php $domain = 'http://api.cmibank.com';?>
<?php $static_domain = 'http://static1.cmibank.com';?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,user-scalable=no">
	<meta name="format-detection" telephone="no">
	<title>小伙伴喊你来赚钱了，买多少送多少</title>
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/css/bind_invite_c9adbf9.css">
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/css/dialog.css">
	<script type="text/javascript" src="<?php echo $static_domain; ?>/common/js/jquery-1.7.1.min.js"></script>
</head>
<script type="text/javascript">
<!--
$(document).ready( function () {
$('#btn').click ( function () {
    $.ajax({
    url: '<?php echo $domain;?>/index.php/login/verification_account',
    type: 'POST',
    data:'account=' + $('#account').val(),
    dataType: 'json',
    timeout: 1000,
    error: function(){
     alert('Error loading XML document');
    },
    success: function(data){
    	if(data.error == 1011){
      	 $("#message").html("提示：您已是易米融理财用户哦！");
        }else if(data.error == 1004){
      	 $("#message").html("提示：您输入的账号格式不对哦！");
        }else if(data.error == 0){
        	$("#c1").css('display','none');
        	$("#c2").css('display','block');
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
    }
    });
});

$('#wangcheng').click ( function () {
	$.ajax({
        type : 'POST',
        url : '<?php echo $domain;?>/index.php/login/reguser',
        data:'account=' + $('#account').val() + '&mobileVerify=' + $('#mobileVerify').val()+ '&password1=' + $('#password1').val()+ '&password2=' + $('#password1').val() + "&code=" + $('#code').val() + "&from=web",
        dataType : 'json',
        async : false,
        success : function(data){
        	if(data.error != 0){
        		$("#message2").html("提示：" + data.msg);
            }else{
            	$("#c1").css('display','none');
            	$("#c2").css('display','none');
            	$("#c3").css('display','block'); 
            }
        	
        }
    });	
});

})
function changcolor1(){
	$(".btn").css({"background-color":"#f14b3b"});
}
function changcolor2(){
	$("#wangcheng").css({"background-color":"#f14b3b"});
}
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
//-->
</script>
<body>
<div id="text"></div>
<img class="lmbBanner" src="<?php echo $static_domain; ?>/images/head.jpg" width="100%" />
<ul class="content" id="form">
    <div id="c1">
	<li>
	<input class="radius" type="tel" id="account" name="account" placeholder="请输入您的手机号码" onclick="changcolor1();"/>
	<p class="message" id="message"></p>
	</li>
	<li class="nextBtn"><button id="btn" class="btn">下一步</button>
	<!---<p class="top-message">来自好友<?php echo $phone; ?>的邀请，首次购买定期产品即可获得现金奖励，最低2元，上不封顶。好友也可立马领取5元现金呦！</p>-->
	</li>
	</div>
	
	<div id="c2" style="display:none">
	<li><input id="mobileVerify" name="mobileVerify" type="number" placeholder="请输入您的短信验证码" style="width:70%;"><input onclick="time(this);" class="chongfa" id="chongfa" value="重发"/></li>
	<li><input class="radius" id="password1" name="password1" placeholder="请设置你的登录密码" onclick="changcolor2();">
	<p class="message" id="message2"></p>
	</li>
	
	<li class="nextBtn"><button class="radius" type="button" id="wangcheng">完成</button></li>
	</div>
	
	<div id="c3" style="display:none">
	<li class="nextBtn">
	<p class="gongxi">恭喜您注册成功，快去领取现金奖励吧！</p>
	<button class="radius" id="submit" onclick="window.location='http://a.app.qq.com/o/simple.jsp?pkgname=cn.app.cmibank'" style="background:#f14b3b">下载易米融理财app</button>
	</li>
	</div>
	
</ul>
<input  type ='hidden' value = "<?php echo $code; ?>" id='code'/>
<img src="<?php echo $static_domain; ?>/images/content2.jpg" width="100%" /><img src="<?php echo $static_domain; ?>/images/content3.jpg" width="100%" />
</body>
</html>