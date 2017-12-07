<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_DOMAIN;?>op/css/rest.css" />
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_DOMAIN;?>op/css/backstage.css" />
<title>新闻详情页</title>

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
		<div id="main_container">
			<div class="container">
				<div class="record" >
					<div id="datalist">
						<form method="post" name="news_form" id="news_form">
						<table class="table1">
							<tr>
								<td width=150px height=50px>新闻标题:</td>
								<td width=700px align="left" class="tdleft"><input type="text" value="<?php echo $info['title'];?>" name="title" id="title" style="width:400px;height:25px;margin-left:20px;" /><span></span></td>
							</tr>
							<tr>
								<td width=150px height=50px>新闻图片:</td>
								<td width=700px align="left" class="tdleft">
									<input type="file" name="titlepic_file" id="titlepic_file" style="height:25px;margin-left:20px;"/>&nbsp;
									<a id="titlepic_submit" style="cursor:pointer;">上传</a>
									<img name="titlepic_skan" id="titlepic_skan" alt="图片预览" width="80px" height="60px;" src="<?php echo $info['titlepic'];?>"/>
									<span></span> 
								</td>
							</tr>
							<tr>
								<td width=150px height=50px>新闻内容:</td>
								<td width=700px align="left" class="tdleft">
									<textarea name="content" id="content" style="height:500px;width:600px;margin-left:20px;" rows=12><?php echo $info['content'];?></textarea>
								</td>						
							</tr>
							<tr>
								<td width=150px height=50px></td>
								<td width=700px align="left" class="tdleft">
									<input type="button" value="修改" style="margin-left:20px;" id="news_submit"/>
								</td>						
							</tr>
						</table>
						<input type="hidden" value="<?php echo $info['titlepic'];?>" name="titlepic" id="titlepic" />
						<input type="hidden" value="<?php echo $type;?>" name="type" />
						<input type="hidden" value="<?php echo $mid;?>" name="mid" id="mid"/>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script language="javascript" src="<?php echo STATIC_DOMAIN;?>common/js/jquery-1.7.1.min.js"></script>
<script language="javascript" src="<?php echo STATIC_DOMAIN;?>common/js/ajaxfileupload.js"></script>
<script language="javascript">
	$(document).ready(function(){


		$('#titlepic_submit').click(function(){
			 var imgPath = $.trim($('#titlepic_file').val());
			 var imgEx = (imgPath.substr(imgPath.length -5)).substr((imgPath.substr(imgPath.length -5)).indexOf('.')+1).toLowerCase();
			 if(imgEx != 'jpg' && imgEx != 'jpeg' && imgEx != 'gif' && imgEx != 'png')
				 alert('请选择正确的文件格式');
			 else{
				 var url = "<?php echo OP_DOMAIN;?>sysmessage/uoloadtitlepic/";
		         $.ajaxFileUpload({
		                url:url,       //需要链接到服务器地址
		                secureuri:false,
		                fileElementId:'titlepic_file',                            //文件选择框的id属性
		                dataType: 'json',                                   //服务器返回的格式，可以是json
		                success: function (data, textStatus) {            //相当于java中try语句块的用法
		                    if(parseInt(data.error) == 0){
			                    $('#titlepic_skan').attr('src',data.msg);
			                    $('#titlepic').val(data.msg);
		                    }
		                    else
		                    	alert(data.msg);
			                    
		                },
		                error: function (data, status, e) {           //相当于java中catch语句块的用法
							alert(e);
		                }
		        });	
			 }
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
				var fun = "<?php echo $fun;?>list";
				$.ajax({
					type:'POST',
					url:url,
					dataType:'json',
					data:data,
					error:function(){},
					success:function(return_data){
						if(parseInt(return_data.error) == 0){
							window.location.href = "<?php echo OP_DOMAIN;?>sysmessage/"+fun;
						}
						else
							alert(return_data.error)
					}
				});					
			}
		})
	})
	
</script>
</body>
</html>
