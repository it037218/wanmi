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
	<title>易米融理财现金红包</title>
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/css/bind_invite_c9adbf9.css">
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/js/jquery-easyui/themes/metro/easyui.css">
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/js/jquery-easyui/themes/mobile.css">
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/js/jquery-easyui/themes/icon.css">
	<script type="text/javascript" src="<?php echo $static_domain; ?>/js/jquery-easyui/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $static_domain; ?>/js/jquery-easyui/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="<?php echo $static_domain; ?>/js/jquery-easyui/jquery.easyui.mobile.js"></script>
</head>
<script type="text/javascript">
$(document).ready( function () {
	var bh = $(window).height()-83; 
	var bw = $(window).width(); 
	$("#main_page").css({ 
		height:bh, 
		width:bw
	}); 
})
</script>
<body style="margin: 0"> 
<div id="main_page" style="background: url('<?php echo $static_domain; ?>/images/bg/hongb01.jpg');background-size:cover;background-repeat:no-repeat;width: 100%"> 
	<div style="height: 30%;text-align: center;font-size: 36px">
			<div style="height: 60px"></div>
			<div style="font-size: 80px"><img alt="" src="<?php echo $static_domain; ?>/images/bg/hongb04.png"></div>
	</div>
	<div style="height: 35%">
		<div style="text-align: center;font-size: 70px;padding-top: 170px;color:#ffee95;"><font style="font-size: 140px"><?php echo $money; ?></font>元</div>
	</div>
	<div class="login" id="login1" style="background: none;height: 20%">
		<ul style="margin: 15% 5% 0 5%">
			<li>
				<div  id="show_detail" style="text-align: center;font-size: 27px;color:#ffce95">
					<input name="btn1" class="btn1" id="btn3" type="button"  style="font-size:36px;color:#a51617 border:0; background:url('<?php echo $static_domain; ?>/images/bg/hongb_btn.png') left;" value='立刻查看' onClick="window.location='http://a.app.qq.com/o/simple.jsp?pkgname=cn.app.cmibank'"/>
					<div style="padding-top:20px" id="tishi1">红包已放入易米融账户:<?php echo $account; ?></div>
					<?php if(!empty($isnew)){?>
						<div id="font1">请于七天内注册绑卡，否则系统将自动收回</div>
					<?php }?>
				</div>
			</li>
		</ul>
	</div>
</div>

<div style="height: 500px">
	<div class="easyui-tabs" data-options="fit:true,border:false,pill:true,justified:true,tabWidth:80,tabHeight:80" >
		<div title="看看大家的手气" style="padding:0px;height: 50px">
				<?php if(!empty($list)){?>
					<?php foreach($list AS $key=>$value){?>
						<div class="shouqi">
							<div style="height:100%;width: 50%;float: left;">
								<div style="text-align: left; padding-left: 30px;padding-top: 25px;font-size: 28px;"><?php echo substr($value['phone'],0,3).'****'.substr($value['phone'],7,4);?></div>
								<div style="text-align: left; padding-top: 15px;padding-left: 30px;color: #989898;font-size: 22px;"><?php echo date('Y-m-d H:i:s',$value['ctime']);?></div>
							</div>
							<div style="height:100%;width: 50%;float: right;">
								<p style="color:#ff4747;padding-top: 40px;font-size: 36px;padding-left: 150px"><?php echo str_pad($value['money'], 4, "0", STR_PAD_RIGHT);?></p>
							</div>
						</div>
					<?php }?>
				<?php }?>
		</div>
		<div title="活动规则" style="padding-top:20px;">
			<ul style="padding-left: 30px;padding-right: 30px;line-height: 35px;list-style-type:disc;font-size:24px">
				<li>成功领取红包的用户，红包自动发放到您的易米融账户余额中。</li>
				<li>每个红包每人限领取1次，同一微信号，同一手机号或同一身份证都默认为同一人。</li>
				<li>如有问题，请联系易米融理财客服：400-080-5611</h2></li>
			</ul>
		</div>
	</div>
</div>

<div id="fullbg" ></div> 
<div id="dialog"> 
	<div style="text-align: right;height: 10%"><img src="<?php echo $static_domain; ?>/images/hd/x.png" onclick="closeBg();"></div>
	<div style="background: url('<?php echo $static_domain; ?>/images/hd/dikuang.png');background-size:contain;background-repeat:no-repeat;height: 30%;width: 100%;height: 100%">
		<div style="height: 20%;text-align: center;font-size: 40px;padding-top: 10px;color: #f1eb88"><font>提示</font></div>
		<div style="height: 80%;text-align: center;color: #711900;font-size: 36px">
			<div style="height: 55%;padding-top: 10%;width: 100%;text-align: center;padding-left: 20%;padding-right: 20%"><font id="tishi_msg"></font></div>
			<div style="height: 30%">
				<input type="button" value="立刻下载" style="background: url('<?php echo $static_domain; ?>/images/hd/anniu.png');background-size:contain;background-repeat:no-repeat;width: 70%;height: 70%;border: none;font-size: 32px;color:#f1eb88" onClick="doDownload();">
			</div>
		</div>
	</div>
</div>

<div id="mask" style="background: url('<?php echo $static_domain; ?>/images/hd/beijing.png');background-size:contain;background-repeat:no-repeat;">
	<div class="showmessage" style="text-align: center">
		<div style="height: 40%"><img alt="" src="<?php echo $static_domain; ?>/images/hd/icon.png" style="margin-top: 90px"></div>
		<div style="height: 40%;padding-top:65px"><div style="color: white;font-size: 28px">易米融理财</div><div style="color: white;font-size: 40px;padding-top:20px">给您送现金红包啦</div></div>
		<div style="height: 20%;margin-top: 110px"><img alt="" src="<?php echo $static_domain; ?>/images/hd/kai.png" onclick="closeMask();"></div>
		<div></div>
	</div> 
</div>

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
/* border:1px solid;  */
height:540px; 
left:37%; 
margin:-200px 0 0 -200px; 
padding:1px; 
position:fixed !important; /* 浮动对话框 */ 
position:absolute; 
top:40%; 
width:90%; 
z-index:5; 
border-radius:5px; 
/* background:white; */
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
	$("#fullbg,#dialog,#mask").hide(); 
} 

function closeMask() { 
	$("#mask").hide(); 
// 	$("#newMain").css({display:"block"});
} 
</script>
</body>
</html>