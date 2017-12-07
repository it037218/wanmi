<?php 
if(isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '127.0.0.1'){
    $domain = 'http://api.cmibank.vip'; 
    $static_domain = 'http://static.cmibank.vip';  
}else if((isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '10.9.160.199') || (isset($_SERVER['HOSTNAME']) && $_SERVER['HOSTNAME'] == '10-9-160-199')){
    $domain = 'http://api.cmibank.vip'; 
    $static_domain = 'http://static.cmibank.vip';   
}else{
	$domain = 'http://api.cmibank.com'; 
    $static_domain = 'http://static1.cmibank.com';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="target-densitydpi=device-dpi, width=640px, user-scalable=no"  >
	<meta name="format-detection" telephone="no">
	<title>易米融理财邀请红包</title>
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/css/bind_invite_c9adbf9.css">
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/js/jquery-easyui/themes/metro/easyui.css">
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/js/jquery-easyui/themes/mobile.css">
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/js/jquery-easyui/themes/icon.css">
	<script type="text/javascript" src="<?php echo $static_domain; ?>/js/jquery-easyui/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $static_domain; ?>/js/jquery-easyui/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="<?php echo $static_domain; ?>/js/jquery-easyui/jquery.easyui.mobile.js"></script>
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
	    url: '<?php echo $domain;?>/login/verifyLuckyBagAccount',
	    type: 'POST',
	    data:'account=' + $('#account').val()+"&code="+$('#code').val()+"&lid="+$('#lid').val(),
	    dataType: 'json',
	    timeout: 3000,
	    error: function(){
	     alert('亲！网络不给力哦。请到网络好的地方试试');
	    },
	    success: function(data){
	    	if(data.error == 1011){
	    		showdownloadBg();
	        }else if(data.error == 1004){
			  showBg();	
	      	 $("#tishi2").html(data.msg);
	        }else if(data.error == 0){
	        	$("#login1").css('display','none');
	        	$("#login2").css('display','block');
	     	}
	    }
	    });
	});

	$('#btn2').click ( function () {
		$.ajax({
	        type : 'POST',
	        url : '<?php echo $domain;?>/login/regLuckybagUser',
	        data:'account=' + $('#account').val() + '&mobileVerify=' + $('#mobileVerify').val()+ '&password=' + $('#password').val() + "&from=web&plat=luckybag"+"&code="+$('#code').val()+"&lid="+$('#lid').val(),
	        dataType : 'json',
	        async : false,
	        success : function(data){
	        	if(data.error != 0){
					showBg();
	        		$("#tishi2").html(data.msg);
	            }else{
	            	$("#login1").css('display','none');
	            	$("#login2").css('display','none');
	            	showdownloadBg();
	            	$("#tishi_reg").html(data.msg);
	            }
	        }
	    });	
	});
// 	showMask();
})
function doDownload(){
		var u = navigator.userAgent;
		if (u.indexOf('iPhone') > -1){
			window.location='http://a.app.qq.com/o/simple.jsp?pkgname=cn.app.cmibank';
		}else{
			window.location='http://api.cmibank.com/download?qudao=cmibank';
			}
	}	
</script>
<body style="margin: 0"> 
<div id="main_page" style="background: url('<?php echo $static_domain; ?>/images/hd/hongbao.jpg');background-size:cover;background-repeat:no-repeat;width: 100%;height: 180%"> 
	<div style="height: 30%;text-align: center;color: yellow;font-size: 36px">
		<?php if($error==1){?>
			<div style="font-size: 60px;padding-top: 60px;">红包已被领走</div>
		<?php }?>
		<?php if($error==0){?>
			<div style="height: 60px"></div>
			<div style="font-size: 80px">恭喜您</div>
			<div>获得1个现金红包</div>
		<?php }?>
	</div>
	<div style="height: 30%">
		<div style="text-align: center;font-size: 60px;padding-top: 210px;color:white;"><font style="font-size: 160px"><?php echo $money; ?></font>元</div>
		<input  type ='hidden' value = "<?php echo $money; ?>" id='money' name='money'/>
	</div>
	<div class="login" id="login1" style="background: none;height: 20%">
		<ul style="margin: 10% 5% 0 5%">
		<li><input class="radius" type="tel" id="account" name="account" placeholder="输入手机号码,领取红包" style="border-radius:8px;border: 1px;height: 70px;font-size: 40px"></li>
		<li class="nextBtn">
			<?php if($error==1){?>
				<button class="btn1" style="color:white;background: grey;font-size: 34px;border-radius:8px;height: 70px;">立刻领取现金</button>
			<?php }?>
			<?php if($error==0){?>
				<button class="btn1" id="btn1" style="color:crimson;background: yellow;font-size: 34px;border-radius:8px;height: 70px">立刻领取现金</button>
			<?php }?>
		</li>
		</ul>
	</div>
	<div class="login" id="login2" style="background: none;display:none;height: 20%">
		<ul style="margin: 10% 5% 0 5%">
		<li><input class="radius" id="password" name="password" placeholder="请设置你的登录密码" style="border-radius:8px;border: 1px;height: 70px;"></li>
		<li><input id="mobileVerify" name="mobileVerify" type="number" placeholder="请输入您的短信验证码" style="border-radius:8px;width:60%;border-top-right-radius:0em;border-bottom-right-radius:0em;height: 70px"><input style="border-radius:8px;border-top-left-radius:0em;border-bottom-left-radius:0em; height: 70px;width:40%;" readonly="readonly" class="chongfa" onClick="time(this);" type="bottom"  id="chongfa" value='获取验证码'></li>
		<li class="nextBtn"><button class="btn2" id="btn2" style="color:crimson;background: yellow;font-size: 34px;border-radius:8px;height: 70px;">立刻领取现金</button></li>
		</ul>
	</div>
	<div style="height: 80px">
		<img alt="" src="<?php echo $static_domain; ?>/images/hd/guizhe.png">
	</div>
	<div style="height:320px;font-size: 26px"> 
		<ul style="padding-left: 30px;padding-right: 30px;color: #f8e48b;line-height: 35px;list-style-type:disc">
			<?php 
			$three = '';
			if($luckybagDetail['usetype']==1){
				$three = '新用户首次需购买['.$luckybagDetail['pnames'].']产品,首次购买金额为'.$luckybagDetail['goumaimoney'].'元';
			}else if($luckybagDetail['usetype']==2){
				$three = '新用户首次需购买['.$luckybagDetail['pnames'].']产品,首次购买金额为 红包金额的'.$luckybagDetail['goumaibeishu'].'倍';
			}
			?>
			<li>成功领取红包的用户，下载易米融理财APP根据条件激活红包即可到账，绑卡后可以购买理财产品或取现。</li>
			<li>每个人限领取1次红包，同一手机号或同一身份证都默认为同一人。</li>
			<?php if(!empty($three)){?>
				<li><?php echo $three;?></li>
			<?php }?>
			<li>如有问题，请联系易米融理财客服：</br><h2>400-871-9299</h2></li>
		</ul>
	</div>
</div>
<div id="downloadDialog"> 
	<p class="close"><a href="#" onClick="closeBg();">X</a></p> 
	<div class="showmessage">
		<ul>
		<li class="tishi1">提示</li>
		<li class="tishi2" id="tishi_reg">您已是易米融注册用户，该活动仅限未注册用户参加，如果还没有安装易米融，那就赶紧下载吧！</li>
		<li class="nextBtn"><button style="width:180px;float: left;"  onClick="closeBg();">取消</button><button style="width:180px;float: right;" onClick="window.location='http://a.app.qq.com/o/simple.jsp?pkgname=cn.app.cmibank'">下载</button></li>
		</ul>
	</div> 
</div>
<div id="fullbg" ></div> 
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

<input  type ='hidden' value = "<?php echo $code; ?>" id='code' name='code'/>
<input  type ='hidden' value = "<?php echo $lid; ?>" id='lid' name='lid'/>
<style>
.tabs-title{
	font-size: 30px;
	font-family: initial;
}
#account::-webkit-input-placeholder {
  font-size: 28px;
  padding-top:8px;
  padding-left:5px
}
.tishi{
	width: 45px;
    margin: 35px auto;
}
.shouqi{
	text-align: center;
	font-size: 34px;
	height: 120px;
	border: solid 1px #dedede;
}
#fullbg { 
background-color:grey;  
left:0; 
opacity:0.9;  
position:absolute; 
top:0; 
z-index:3; 
filter:alpha(opacity=50); 
-moz-opacity:0.5; 
-khtml-opacity:0.5; 
} 

 
#mask { 
border:1px solid #ffd200; 
height:774px; 
width:540px; 
left:47%; 
margin:-350px 0 0 -250px; 
padding:1px; 
position:fixed !important; /* 浮动对话框 */ 
position:absolute; 
top:46%; 
z-index:5; 
border-radius:5px; 
background:#ffd200;
border-radius:16px;
display:none; 
} 

#mask p { 
margin:0 0 12px; 
height:24px; 
line-height:24px; 
} 
#mask p.close { 
text-align:right; 
padding-right:10px; 
} 
#mask p.close a { 
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
#downloadDialog { 
		border:1px solid #ffd200; 
		height:300px; 
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

function showMask() { 
	var bh = $("body").height(); 
	var bw = $("body").width(); 
	$("#fullbg").css({ 
	height:bh, 
	width:bw, 
	display:"block" 
	}); 
	$("#mask").show(); 
}
//关闭灰色 jQuery 遮罩 
function closeBg() { 
	$("#fullbg,#dialog,#mask,#downloadDialog").hide(); 
} 

function closeMask() { 
	$("#mask").hide(); 
// 	$("#newMain").css({display:"block"});
} 
</script>
</body>
</html>