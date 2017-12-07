<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_DOMAIN;?>op/css/rest.css" />
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_DOMAIN;?>op/css/backstage.css" />
<title>用户管理</title>

</head>

<body>
<div id="wrap" class="clear">
	<?php include APPPATH.'/views/v_left.php';?>
	<div class="col-main">
		<div class="menu">
			<div class="menu-inside">
				<ul>
					<?php foreach($menu AS $key=>$value):?>
					<li><a href="<?php echo $value['url'];?>" <?php if($value['name']==$nav[0]):?>class="sel"<?php endif;?>><?php echo $value['name'];?></a></li>
					<?php endforeach;?>
				</ul>
			</div>
		</div>
		<ul class="nav">
			<li class="cur">
				<span>用户列表</span>
			</li>
		</ul>
		<div id="search" style="padding-top:10px;padding-left:45px;">
			用户搜索:&nbsp;&nbsp;<input name="search_keyword" id="search_keyword" value="" style="height:25px;"/>&nbsp;&nbsp;<input type="button" value="搜索" id="search_submit" />
		</div>
		<div class="container">
			<div class="record" >
				<div id="datalist">
					数据获取中...
				</div>
				<div id="pagestring"></div>
			</div>
		</div>
	</div>
</div>
<form action="" method="post" id="data_form" name="data_form">
	<?php if(is_array($hidden) && !empty($hidden)){
			foreach($hidden as $k => $v){
	?>
		<input type="hidden" name="<?php echo $k;?>" id="<?php echo $k;?>" value="<?php echo $v;?>" />
	<?php }}?>
</form>
<script language="javascript" src="<?php echo STATIC_DOMAIN;?>common/js/jquery-1.7.1.min.js"></script>
<script language="javascript">
	$(document).ready(function(){
		dataformat(1);

		$('#search_submit').click(function(){
			var search_keyword = $.trim($('#search_keyword').val());
			if(!search_keyword)
				alert('请输入关键字');
			else{
				var append_html = "<input type='hidden' name='keyword' id='keyword' value='"+search_keyword+"'>";
				$('#data_form').append(append_html);
				dataformat(1);
			}
		})
	})
	
	function changestatus(obj){
		var id_str = $.trim($(obj).attr('id'));
		var arr = id_str.split('_');
		var userid = arr[0];
		var type = arr[1];

		var url = "<?php echo OP_DOMAIN;?>user/changeuser";
		var data = "json=1&uid="+userid+'&type='+type+'&v='+Math.random(0,1);
		$.ajax({
			type:'POST',
			url:url,
			dataType:'json',
			data:data,
			error:function(){},
			success:function(return_data){
				if(parseInt(return_data.error) == 0){
					var page = parseInt($('#page').val());
					dataformat(page);
				}
				else
					alert(return_data.msg);
			}
		});	
	}
	
	function dataformat(page){
		var url = "<?php echo OP_DOMAIN;?>user/userlist_data";
		var data = $('#data_form').serialize()+'&page='+page+'&v='+Math.random(0,1);
		$.ajax({
			type:'POST',
			url:url,
			dataType:'json',
			data:data,
			error:function(){},
			success:function(return_data){
				if(parseInt(return_data.error) == 0){
					$('#datalist').html(return_data.list);
					$('#pagestring').html(return_data.pagestring);
					$('#page').val(page);
				}
				else
					alert(return_data.msg);
			}
		});				

	}

</script>
</body>
</html>
