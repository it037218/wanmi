<div class="pageHeader">
	<form onsubmit="return myValidateCallback(this);"  class="pageForm required-validate" action="<?php echo OP_DOMAIN?>redbag/getListByAccount" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					手机号码：<input name="account" id="account" value="<?php echo empty($account)?'':$account?>" class='filed-text required'/>
				</td>
				<td><input type="hidden" value="search" name="op"><button type="submit">检索</button></td>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
	<table class="list" width="100%" layoutH="50">
        <thead>
            <tr>
            	<th width="3%">序号</th>
                <th width="10%">电话号码</th>
                <th width="5%">姓名</th>
                <th width="5%">金额</th>
                <th width="10%">领取时间</th>
                <th width="10%">激活时间</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                	<td><?php echo $key+1 ;?></td>
                    <td><?php echo $value['phone'] ;?></td>
                    <td><?php echo empty($names[$value['phone']])?'':$names[$value['phone']] ;?></td>
                    <td><?php echo $value['money'] ;?></td>
                    <td><?php echo empty($value['ctime'])?'':date('Y-m-d H:i:s',$value['ctime']); ;?></td>
                    <td><?php echo empty($value['utime'])?'':date('Y-m-d H:i:s',$value['utime']); ;?></td>
                </tr>
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</div>

<script type="text/javascript">
function myValidateCallback(form) {
	var $form = $(form);

	if (!$form.valid()) {
		return false;
	}
	return navTabSearch(form);
}
</script>
