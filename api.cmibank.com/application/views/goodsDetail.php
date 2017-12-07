<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"/>
<title>商品详情</title>
<style type="text/css">

</style>
</head>
<body style='padding:0px;margin:0px'>
	<div style='background-color:#EBEBEB'>
		<table style='width:100%'>
			<tr style='height:15px'></tr>
			<?php $p_data = explode('##', $detail['desc']);?>
			<?php foreach ($p_data as $_p){
    			list($title, $content) = explode('@@', $_p);
			?>
			<tr>
				<td style='width:5%'></td>
				<td style='width:80%;font-size:18px;font-weight:bold;color:#000000'><?php echo $title; ?></td>
			</tr>
			<tr>
				<td></td>
				<td  style='font-size:16px;color:#808080'><?php echo $content; ?></td>
			</tr>
			<tr style='height:20px'></tr>
			<?php }?>
		</table>
	</div>
</body>
</html>