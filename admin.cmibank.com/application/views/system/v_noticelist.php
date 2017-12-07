<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_DOMAIN;?>op/css/rest.css" />
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_DOMAIN;?>op/css/backstage.css" />
<title>系统公告</title>

</head>
<style type="text/css">
	.col-main .table1 th,.col-main .table1 td{height:40px;text-align:left}	
</style>
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
			<li class="cur" id="cur_0">
				<span>公告列表</span>
			</li>
			<li id="cur_1">
				<span>添加公告</span>
			</li>			
			
		</ul>
		<div id="main_container">
			<div class="container">
				<div id="search" style="padding-top:10px;padding-left:45px;">
					公告搜索:&nbsp;&nbsp;<input name="search_keyword" id="search_keyword" value="" style="height:25px;"/>&nbsp;&nbsp;<input type="button" value="搜索" id="search_submit" />
				</div>
				<div class="record" >
					<div id="datalist">
						数据获取中...
					</div>
					<div id="pagestring"></div>
				</div>
			</div>
			
			<div class="container hidden">
				<div class="record" >
					<div id="datalist">
						<form method="post" name="news_form" id="news_form">
						<table class="table1">
							<tr>
								<td width=150px height=50px>公告标题:</td>
								<td width=700px align="left" class="tdleft"><input type="text" name="title" id="title" style="width:400px;height:25px;margin-left:20px;" /><span></span></td>
							</tr>
							<tr>
								<td width=150px height=50px>公告内容:</td>
								<td width=700px align="left" class="tdleft">
									<textarea name="content" id="content" style="height:500px;width:600px;margin-left:20px;" rows=12></textarea>
								</td>						
							</tr>
							<tr>
								<td width=150px height=50px></td>
								<td width=700px align="left" class="tdleft">
									<input type="button" value="预发布" style="margin-left:20px;" id="news_submit"/>
								</td>						
							</tr>
						</table>
						<input type="hidden" value="2" name="type" />
						</form>
					</div>
				</div>
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
<script language="javascript" src="<?php echo STATIC_DOMAIN;?>common/js/ajaxfileupload.js"></script>
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

		$('#nav').find('li').each(function(){
			$(this).click(function(){
				var id_str = $(this).attr('id');
				changenav(id_str);
			})
		})


		//**文章发布
		$('#news_submit').click(function(){
			var title = $.trim($('#title').val());
			var content = $.trim($('#content').val());

			if(!title)
				alert('请填写标题');
			else if(!content)
				alert('请填写内容');
			else{
				var url = "<?php echo OP_DOMAIN;?>sysmessage/addmessage/";
				var data = $('#news_form').serialize()+'&json=1';
				$.ajax({
					type:'POST',
					url:url,
					dataType:'json',
					data:data,
					error:function(){},
					success:function(return_data){
						if(parseInt(return_data.error) == 0){
							dataformat(1);
							changenav('nav_0');	
						}
						else
							alert(return_data.error)
					}
				});					
			}
		})
	})
	
	function changenav(nav_id){
		var arr = nav_id.split('_');
		var index = arr[1];
		$('#main_container').find('.container').each(function(){
			$(this).addClass('hidden');
		})
		$('#main_container').find('.container').eq(index).removeClass('hidden');
		$('#nav').find('.cur').removeClass('cur');
		$('#'+nav_id).addClass('cur');
	}

	
	function changestatus(obj){
		var id_str = $.trim($(obj).attr('id'));
		var arr = id_str.split('_');
		var id = arr[0];
		var type = arr[1];

		var url = "<?php echo OP_DOMAIN;?>sysmessage/changemessage";
		var data = "json=1&id="+id+'&type='+type+'&v='+Math.random(0,1);
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
		var url = "<?php echo OP_DOMAIN;?>sysmessage/messaglist_data";
		$('#page').val('');
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
