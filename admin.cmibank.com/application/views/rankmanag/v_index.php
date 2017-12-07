<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>rankmanag" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
			<?php if($editable==1){?>
				<td>
					手机号码：<input name="phone" id="phone" value="<?php echo isset($phone)?$phone:''?>"/>
				</td>
				<td>
					分数：<input name="score" id="score" value="<?php echo isset($score)?$score:''?>"/>
				</td>
				<td>
					类型：<select name="type" id="id_type">
						<option value="1">积分奖励</option>
						<option value="2">积分补偿</option>
						</select>
				</td>
				<td><input type="hidden" value="search" name="op"><button type="submit">添加</button></td>
				<?php }else{?>
				<td>没有权限</td>
				<?php }?>
			</tr>
		</table>
	</div>
	</form>
</div>

