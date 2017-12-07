<?php
if (@$_SERVER['ENVIRONMENT'] == 'production') {
    $domain = 'http://api.cmibank.com'; 
    $static_domain = 'http://static1.cmibank.com';
} elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
    $domain = 'http://api.cmibank.vip'; 
    $static_domain = 'http://static.cmibank.vip'; 
} else {
    $domain = 'http://api.cmibank.dev'; 
    $static_domain = 'http://static.cmibank.dev'; 
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
        data:'account=' + $('#account').val() + '&mobileVerify=' + $('#mobileVerify').val()+ '&password1=' + $('#password1').val()+ '&password2=' + $('#password1').val() + "&code=" + $('#code').val() + "&from=web&plat=<?php echo $from;?>",
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
$('#rightbtn').click(function(){
    $(".huodongshuoming").show();
    $(this).hide();
});
$('#closebtn').click(function(){
    $(".huodongshuoming").hide();
    $('#rightbtn').show();
});

})
function doDownload(){
    window.location.replace("http://www.cmibank.com/download");
    // var u = navigator.userAgent;
    // if (u.indexOf('iPhone') > -1){
    //         window.location='https://itunes.apple.com/us/app/易米融/id1291154070?l=zh&ls=1&mt=8';
    // }else{
    //         window.location='http://api.cmibank.com/download?qudao=cmibank';
    // }
}

</script>
<body>
<img style="width: 100%;height: 100%;" src="<?php echo $static_domain;?>/images/20171106/ad_01.png"/>
<img style="width: 100%;height: 100%;" src="<?php echo $static_domain;?>/images/20171106/ad_02_2.png"/>
<img style="width: 100%;height: 100%;" src="<?php echo $static_domain;?>/images/20171106/ad_03.png"/>
<style>
body{
    background: #FFD5B3;
}
.rank{
    border: 1px solid #ffdfc8;
    width:83%;
    margin: 50px auto;
    padding-top: 15px;
    background-color: #ffdcc5;
    border-radius: 20px;
    clear: both;
}

.touzi_rank{
    margin: 30px 50px 20px 50px;
    line-height: 40px;
    font-size: 18px;
    font-family: Arial,Helvetica,sans-serif,"新宋体";
    color: #666666;
}

.itemicon{
    margin: -44px auto 0;
    display: block;
}

.ranktips{
    background-color: #ffceb4;
    color: #a8432d;
    padding: 17px;
    font-size: 23px;
    margin-top: 8px;
    border: 1px solid #ffebdc;
    display: block;
}

.rank table{
    text-align:center;
}

.rank table tr td{
    padding:12px 15px;
    font-size: 1.2em;
    
}
.rank table tr.single{
    background-color: #ffceb4;
}
.rank table tr.single,.rank table tr.double{
    border-top: 3px solid #ffebdc;
    border-bottom: 3px solid #ffebdc;
}
.rank table .head td{
    padding: 20px 10px;
    
}
.viewbutton{
    display: inline-block;
    text-align: center;
    width: 2.5rem;
    padding: 1rem 5px;
    float: right;
    margin-top: -9rem;
    margin-right: -2.5rem;
    font-size: 1.2em;
    color:#ffceb4;
    clear: both;
    height:9rem;
    background: url('<?php echo $static_domain;?>/images/20171106/chakangengduo.png') no-repeat;
    background-size:4.5rem 9rem;
}
a.viewbutton:hover,.viewbutton:active{
    color:#ffceb4;
}
#rightbtn{
    width: 100px;
    height: 80px;
    float:right;
    background: transparent;
    font-size:1.7rem;
    text-align:center;
    color:white;
    position:fixed;
    right:0;
    top:9rem;
}
.huodongshuoming{
    display:none;
    position:fixed;
    z-index:100;
    top:0;
    padding-top:2rem;
    width:100%;
    height:100%;
    text-align:center;
    background:url("<?php echo $static_domain;?>/images/20171106/touming2.png");
}
.closeimg{
    width:3rem;
}
.huodongshuoming img{
    opacity:1;
}
</style>
<div style="background: #FFD5B3;">
    <div id="rightbtn">
        <img src="<?php echo $static_domain;?>/images/20171106/active_button.png"/>
    </div>
    <div class="huodongshuoming">
        <img width="90%" src="<?php echo $static_domain;?>/images/20171106/guize.png"/>

        <div id="closebtn"><img class="closeimg" width="70%" src="<?php echo $static_domain;?>/images/20171106/close.png"/></div>
    </div>

    <div class="login" id="login1" style="background: #FFD5B3;margin-top: 50px">
    <!--<?php echo $be_invite_num; ?>-->
    <!-- <p style="color: white">我正在易米融理财抢现金，你也来领取吧！</br><?php if($be_invite_num > 300) echo '<script>alert("活动已结束，购买人首投不再送红包！");</script>';?></p> -->
    <ul>
    <li><input class="radius" style="border-radius:10px;border:1px #EC6459 solid;text-align: center;padding-left: 10px;" type="tel" id="account" name="account" placeholder="请输入您的手机号码"></li>
    <li class="nextBtn"><button class="btn1" id="btn1" style="border-radius:10px;font-weight:400;color:#FFF0D7;background: #EB5F54;font-size: 42px;">领取现金奖励</button></li>
    </ul>
</div> 
<div class="login" id="login2" style="display:none;background: #ffd5b3">
    <!-- <p style="color: white">我正在易米融理财抢现金，你也来领取吧！</br>（50元受邀奖励已经结束！）</p> -->
    <ul>
    <li><input class="radius" style="border-radius:10px;border:1px #EC6459 solid;text-align: center;padding-left: 10px;" id="password1" name="password1" placeholder="请设置你的登录密码"></li>
    <li><input id="mobileVerify" name="mobileVerify" type="number" placeholder="请输入您的短信验证码" style="width:70%;border-radius:10px;border:1px #EC6459 solid;text-align: center;padding-left: 10px;"><input style="border-radius:10px;border:1px #EC6459 solid;text-align: center;padding-left: 10px;" class="chongfa" onClick="time(this);"  readonly="readonly" type="bottom"  id="chongfa" value='获取验证码'></li>
    <li class="nextBtn"><button class="btn2" id="btn2" style="border-radius:10px;font-weight:400;color:#FFF0D7;background: #EB5F54;font-size: 42px;">领取现金奖励</button></li>
    </ul>
</div>
<div class="download" id="download" style="display:none;">
    <P style="color:#FF5B55">
    恭喜你注册成功，离奖励更近一步！<br/>
    立即下载易米融理财，马上领取现金<br/>
    </P>
    <ul>
    <li class="nextBtn"><button style="border-radius:10px;font-weight:400;color:#FFF0D7;background: #EB5F54;font-size: 42px;" class="radius" onClick="doDownload()">立即下载</button></li>
    </ul>
</div>
<img style="width: 100%;height: 100%;" src="<?php echo $static_domain;?>/images/20171106/ad_foot.png"/>
<!--     <hr>
    <p> <center >Copyright<span style="font-size:1.5em; bottom:0">&copy;</span>万米财富有限公司ALL Rights Reserved</center> </p>
    <p><center>ICP备案号：沪ICP备15008416号</center></p> -->
</div></div>
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
<div style="display: none;">
<script src="https://s22.cnzz.com/z_stat.php?id=1268891117&web_id=1268891117" language="JavaScript"></script>
</div>
</body>
</html>