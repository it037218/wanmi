<!DOCTYPE html>
<html>
<head>
    <title><?php echo $type=='obj'?'项目描述':'资金保障'; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link href="<?php STATIC_DOMAIN?>/jcc/mobile/css/core.css" rel="stylesheet" type="text/css"/>
    <link href="/jcc/mobile/css/product_detail_more.css" rel="stylesheet" type="text/css"/>
    	<script type="text/javascript" src="http://libs.baidu.com/jquery/1.8.3/jquery.min.js"></script>
    <style type="text/css">

    	.new_big_img{
    		width:100%;
    		height:100%;
    		background-color:#000;
    		position:absolute;
    		top:0;
    		display:none;
    		margin:auto auto;
    	}
    	
    	#bigImg{
    		width:100%;
    		position:absolute;
    		top:0;
    		display:none;
    		z_index=100;
    		
    	}

        * {
            margin: 0;
            padding: 0;
            /*
            -webkit-user-select: none;
            -webkit-touch-callout: none;
            -webkit-text-size-adjust: none;
            */
        }
        
        body {
            color: #2B2B2B;
            font-size: 62.5%;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            background: #F0F0F0;
        }
        
        button {
            max-width: 100%;
            border: 0;
            background: none;
        }
        
        img {
            max-width: 100%;
            height: auto;
        }
        
        input:focus {
            outline: none; /*鍘婚櫎杈撳叆鏃剁殑澶栬竟妗�*/
        }
        
        button:focus {
            outline: none; /*鍘婚櫎鐐瑰嚮鐨勫杈规*/
        }
        
        h2{
            line-height: 1.4em;
        }
        
        * {
            color: #2B2B2B;
        }
        
        h4 {
            font-size: 1.6em;
            background: #E0DCDC;
            padding: 0.6em;
            margin: 0.6em 0;
        }
        
        h4:first-child {
            margin-top: 0;
        }
        
        p {
            font-size: 1.4em;
            line-height: 1.2em;
            padding: 0 0.6em;
        }
        
        ul {
            padding: 0 0.6em;
        }
        
        li {
            text-align: center;
            margin: 10px;
            list-style: none;
        }

</style>
</head>
<body>
<div id="body">
<?php $p_data = explode('##', $desc);?>
<?php foreach ($p_data as $_p){

    list($title, $content) = explode('@@', $_p);
?>
<h4><?php echo $title; ?>
</h4>

<p><?php echo $content; ?>
</p>
<?php }?>
<?php if($img){ ?>
<h4>相关文件</h4>
<ul>
    <li><img src="<?php echo $img; ?>" id="old_img"  onclick="showBigImg(this)"/></li>
</ul>
</div>
<?php } ?>
<div class="new_big_img">
	<img src=""  id="bigImg" onclick="recovery()"/>
</div>
</body>

<script type="text/javascript">
	function showBigImg(img){
		var url = img.src;
		document.getElementById('bigImg').src = url;
		//$(".new_big_img").height();
		//$(".new_big_img").width($(document).width());
		$("#bigImg").width($(document).width());
		$('.new_big_img').show();
		$('#bigImg').show();
		var meta = document.getElementsByTagName('meta'); 
		meta[0].setAttribute('content',"width=device-width, initial-scale=2.0, user-scalable=yes");
		$('#body').css('position','fixed');
	}
	function recovery(){
		$('.new_big_img').hide();
		$('#bigImg').hide();
		var meta = document.getElementsByTagName('meta'); 
		meta[0].setAttribute('content',"width=device-width, initial-scale=1.0, user-scalable=no");
		$('#body').css('position','absolute');
	}
</script>
</html>
