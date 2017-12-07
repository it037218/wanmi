<?php
if (@$_SERVER['ENVIRONMENT'] == 'production') {
    $domain = 'http://api.cmibank.com'; 
    $static_domain = 'http://static1.cmibank.com';
} elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
    $domain = 'http://api.cmibank.vip'; 
    $static_domain = 'http://static.cmibank.vip'; 
} else {
    $domain = 'http://api2.cmibank.dev'; 
    $static_domain = 'http://static.cmibank.dev'; 
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="target-densitydpi=device-dpi, width=640px, user-scalable=no"  >
    <meta name="format-detection" telephone="no">
    <title>activity1111小伙伴喊你来赚钱啦！</title>
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

</script>
<body>
<img style="width: 100%;height: 100%;" src="<?php echo $static_domain;?>/images/20171106/yq_header.png"/>

<img style="width: 100%;height: 100%;" src="<?php echo $static_domain;?>/images/20171106/yq_body.png"/>

<style>
    html,body{
        font-size:12px;
    }
body{
    background: #ffd5b3;
}
.rank{
    border: 1px solid #ffdfc8;
    width:85%;
    margin: 50px auto;
    padding-top: 15px;
    background-color: #ffdcc5;
    border-radius: 20px;
    clear: both;
}

.touzi_rank{
    padding: 30px 20px 20px 20px;
    line-height: 32px;
    font-size: 13px;
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
    font-size: 12px;
    margin-top: 8px;
    border: 1px solid #ffebdc;
    display: block;
}

.rank table{
    text-align:center;
}

.rank table tr td{
    padding:12px 4px;
    font-size: 1em;
    
}
.rank table tr.single{
    background-color: #ffceb4;
}
.rank table tr.single,.rank table tr.double{
    border-top: 3px solid #ffebdc;
    border-bottom: 3px solid #ffebdc;
}
.rank table .head td{
    padding: 20px 4px;
    
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
</style>
<div style="background: #ffd5b3;">
    <div class="rank">
        <img class="itemicon" width="70%" src="<?php echo $static_domain;?>/images/20171106/paihang.png"/>
        <strong class="ranktips">中奖结果将在活动结束后公布，以下仅供参考！</strong>  
        <table width="100%">
            <tr class="head">
                <td>
                    当前排名
                </td>
                 <td>
                    邀请人账户
                </td>
                 <td>
                    邀请好友数量
                </td>
                <td>
                    好友首投总额
                </td>
            </tr>
            <?php foreach ($list as $key => $value) { 
                $ranknum = $key+1;
                ?>
            <tr class="<?php if($ranknum%2==0){echo 'double';}else{echo 'single';}?>">
                <td>
                    <?php echo $ranknum;?>
                </td>
                 <td>
                    <?php echo $value['_invite_account'];?>
                </td>
                 <td>
                    <?php echo $value['count'];?>
                </td>
                <td>
                    <?php echo $value['subbuyamout'];
                        if($ranknum == 10){
                            break;
                        }
                    ?>
                </td>
            </tr>
            <?php  };?>
            
        </table>
        <?php
            if(count($list) >10){
        ?>
            <a class="viewbutton" href="<?php echo $domain;?>/invite_page/toprank">查<br/>看<br/>更<br/>多</a>
        <?php
            }
        ?>
        <div class="touzi_rank">投资总额是指所有有效好友第一次投资金额的总和邀请好友投资1000元以上的定期产品为一个有效人数。
            <p style="margin-top: 10px">榜单排序规则：按照邀请好友的数量排序。
            <p style="margin-top: 10px">邀请好友数量相同时，则按照完成邀请的时间先后排名。</p>
        </div>
    </div>
    <img style="width: 100%;height: 100%;margin-top: 20px;" src="<?php echo $static_domain;?>/images/20171106/yq_note.png"/>
    <div class="login" id="login1" style="background: #ffd5b3;margin-top: 50px">
    <!--<?php echo $be_invite_num; ?>-->
    <!-- <p style="color: white">我正在易米融理财抢现金，你也来领取吧！</br><?php if($be_invite_num > 300) echo '<script>alert("活动已结束，购买人首投不再送红包！");</script>';?></p> -->
<!--     <ul>
    <li class="nextBtn"><button class="btn1" id="btn1" style="border-radius:10px;font-weight:400;color:#FFF0D7;background: #EB5F54;font-size: 42px;">邀请我的好友</button></li>
    </ul> -->
</div> 
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