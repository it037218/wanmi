<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>后台管理</title>
    <link href="<?php echo STATIC_DOMAIN; ?>/admin/dwz/themes/css/login.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="login">
    <div id="login_header">
        <h1 class="login_logo">
            <a href="<?php echo OP_DOMAIN; ?>/v2/enter"><img src="<?php echo STATIC_DOMAIN; ?>/admin/dwz/themes/default/images/login_logo.png" /></a>
        </h1>
        <div class="login_headerContent">
            <div class="navList">
                <ul>
                    <li><a href="javascript:;">反馈</a></li>
                    <li><a href="javascript:;">帮助</a></li>
                </ul>
            </div>
            <h2 class="login_title"><img src="<?php echo STATIC_DOMAIN; ?>/admin/dwz/themes/default/images/login_title.png" /></h2>
        </div>
    </div>
    <div id="login_content">
        <div class="loginForm">
            <div style="text-align:center;height: 10px">
                <span class="_tips" style="color: red;text-align: center"></span>
            </div>
            <p style="padding-top: 10px">
                <label style="font-size: 12px;width: 55px">账号：</label>
                <input type="text" name="name" size="18" class="login_input" />
            </p>
            <p style="padding-top: 10px">
                <label style="font-size: 12px;width: 55px">密码：</label>
                <input type="password" name="password" size="18" class="login_input" />
            </p>
            <p style="padding-top: 10px">
                <label style="font-size: 12px;width: 55px">验证码：</label>
                <input class="code" type="text" size="5" value="pzcr"/>
                <span><img src="<?php echo STATIC_DOMAIN; ?>/admin/dwz/themes/default/images/header_bg.png" alt="" width="75" height="24" /></span>
            </p>
            <div class="login_bar" style="padding-left: 65px;padding-top: 10px">
                <input class="sub _login_btn" type="button" value=" " />
            </div>
        </div>
        <div class="login_banner"><img src="<?php echo STATIC_DOMAIN; ?>/admin/dwz/themes/default/images/login_banner.jpg" /></div>
        <div class="login_main">
            <ul class="helpList">
                <li><a href="javascript:;">xxxxxxx</a></li>
                <li><a href="javascript:;">xxxxxxxxxxxx</a></li>
                <li><a href="javascript:;">xxxxxxxxxxxxxxxxx</a></li>
                <li><a href="javascript:;">xxxxxxxxxxxxxxxxxxxxxxx</a></li>
            </ul>
            <div class="login_inner">
                <p>xxxxxxxxxxxxxxxxxxxxxxx</p>
                <p>xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</p>
                <p>xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</p>
            </div>
        </div>
    </div>
    <div id="login_footer">
        <?php echo $site_info['copyright']; ?>
    </div>
</div>
<script src="<?php echo STATIC_DOMAIN; ?>/admin/dwz/js/jquery-1.7.2.js" type="text/javascript"></script>
<script type="text/javascript">
    function loginTip(text){
        $('._tips').text(text);
        $('._tips').fadeIn(2000);
        $('._tips').fadeOut(3000);
    }
    $(function(){
        //$('input').val('');
        $('input[name="username"]').focus();

        $('._login_btn').click(function(){

            var _name = $('input[name="name"]');
            if($.trim(_name.val()) == ''){
                loginTip('登录账号不能为空');
                _name.focus();
                return false;
            }

            var _password = $('input[name="password"]');
            if($.trim(_password.val()) == ''){
                loginTip('密码不能为空！');
                _password.focus();
                return false;
            }

//            var _verify = $('input[name="verify"]');
//            if($.trim(_verify.val()) == ''){
//                loginTip('验证码不能为空！');
//                _verify.focus();
//                return false;
//            }
            $.ajax({
                type : 'POST',
                url : '<?php echo OP_DOMAIN; ?>/login/ajaxlogin',
                data : 'name='+_name.val()+'&password='+_password.val(),
                dataType : 'json',
                async : false,
                success : function($data){
                    if($data['flag']>0){
                        location.href = '<?php echo rtrim(OP_DOMAIN,'/'); ?>/homepage';
                        return true;
                    }else{
                        loginTip( $data['msg']);
                        return false;
                    }
                }
            });
        });

        document.onkeydown = function(e){
            var ev = document.all ? window.event : e;
            if(ev.keyCode==13) {
                $('input[class="sub _login_btn"]').click();
            }
        }
    });
</script>
</body>
</html>