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
		<ul class="nav" id="nav">
			<?php $i =0; foreach($user_type as $k => $v){ ?>
				<li id="cur_<?php echo $i;?>"><span><?php echo $v['s']; ?></span></li>
			<?php $i++;} ?>
		</ul>

		<div class="container">
			<div class="record" >
				<div id="datalist">
					
				</div>
			</div>
		</div>
	</div>
</div>
<script language="javascript" src="<?php echo STATIC_DOMAIN;?>common/js/jquery-1.7.1.min.js"></script>
<script language="javascript">
	$(document).ready(function(){
		//dataformat(1);
		$('#cur_0').addClass('cur');
		$('#nav').find('li').each(function(){
			$(this).click(function(){
				$('#nav').find('.cur').removeClass('cur');
				$(this).addClass('cur');
			})
		})
	})
		
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
