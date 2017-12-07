<?php
    $type = @$_GET['type'] ? $_GET['type'] : null;
	$agent = $_SERVER['HTTP_USER_AGENT'];

    //PC下载
	if($type == 1){
        header("location: https://itunes.apple.com/us/app/易米融/id1291154070?l=zh&ls=1&mt=8");
    }elseif ($type == 2){
	    header("location: http://static.cmibank.com/apk/cmibank.apk");
    }
    //移动端下载
    if (stristr($agent, 'iPhone') || stristr($agent, 'Ipad')){
        header("location: https://itunes.apple.com/us/app/易米融/id1291154070?l=zh&ls=1&mt=8");
    }else if (stristr($agent, 'Android')){
	    if (stristr($agent, 'MicroMessenger')){
	        echo "<html>";
            echo "<head>";
            echo "<meta charset=\"UTF-8\">";
            echo "<meta name=\"viewport\" content=\"width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;\">";
            echo "<meta name=\"apple-mobile-web-app-capable\" content=\"yes\">";
            echo "<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\">";
            echo "<meta name=\"format-detection\" content=\"telephone=no\">";
            echo "<style>";
            echo "*{padding:0;margin:0;}";
            echo "</style>";
            echo "<title>易米融App下载提示页</title>";
            echo "</head>";
            echo "<body style='height: 100%'>";
            echo "<div style=\"position:relative;width:100%;height: auto;\">";
            echo "<img width='100%' style=\"height: auto\" src=\"http://www.cmibank.com/images/and_wx.png\" />";
            echo "<div style=\"position:absolute;width:100%;height:auto;z-indent:2;text-align: center;bottom: 17%;font-size: 12px;font-family:微软雅黑;font-weight: 200;color: #666666\">版本：1.0.3 / 大小：28MB</div>";
            echo "</div>";
            echo "</body>";
            echo "</html>";
        }else{
            header("location: http://static.cmibank.com/apk/cmibank.apk");
        }
    }else{
        echo "<p><button><a href='http://static.cmibank.com/apk/cmibank.apk'>安卓版下载</a></button></p>";
        echo "<p><button><a href='https://itunes.apple.com/us/app/易米融/id1291154070?l=zh&ls=1&mt=8'>Iphone版下载</a></button></p>";
    }
	exit(0);
?>