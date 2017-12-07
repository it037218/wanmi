<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>jifeng">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <input type="hidden" value="search" name="op">
    <input type="hidden" value="<?php echo isset($account)?$account:''?>" name="account">
    <input type="hidden" value="<?php echo $type; ?>" name="type">
    <input type="hidden" value="<?php echo isset($stime)?$stime:''?>" name="stime">
    <input type="hidden" value="<?php echo isset($etime)?$etime:''?>" name="etime">
</form>
<div class="pageHeader">
	<form onsubmit="return jifenValidateCallback(this);" action="<?php echo OP_DOMAIN?>jifeng" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					手机号码：<input type="text"  value="<?php echo isset($account)?$account:''?>"  id="id_account" name="account" class='filed-text required'>
				</td>
				<td>
					收支：
					<select name="type">
						<option value="0" <?php if($type == 0){ echo 'selected';}?>>全部</option>
						<option value="1" <?php if($type == 1){ echo 'selected';}?>>签到</option>
						<option value="2" <?php if($type == 2){ echo 'selected';}?>>新手任务</option>
						<option value="3" <?php if($type == 3){ echo 'selected';}?>>购买定期</option>
						<option value="4" <?php if($type == 4){ echo 'selected';}?>>兑换商品</option>
					</select>
				</td>
				<td>
					日期：<input name="stime" readonly="true"  class="date"  value="<?php echo isset($stime) ? $stime : "";?>"  />&nbsp;&nbsp;至
            			 <input name="etime" readonly="true" class="date"  value="<?php echo isset($etime) ? $etime : "";?>"  />
				</td>
				<td><input type="hidden" value="search" name="op"><button type="submit">检索</button></td>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <li><span>总积分：</span></li>
            <li style="padding-top:7px"><?php echo isset($total) ? $total : 0;?></li>
            <li style="padding-left:20px"><span>已兑换积分：</span></li>
            <li style="padding-top:7px"><?php echo isset($totalduihuang) ? $totalduihuang : 0;?></li>
            <li style="padding-left:20px"><span>剩余积分：</span></li>
            <li style="padding-top:7px"><?php echo isset($left) ? $left : 0;?></li>
            <li style="padding-left:20px"><span>虚拟商品兑换金额：</span></li>
            <li style="padding-top:7px"><?php echo isset($totalxuni) ? $totalxuni : 0;?></li>
            <li style="padding-left:20px"><span>虚拟商品实际支出：</span></li>
            <li style="padding-top:7px"><?php echo isset($totalzhichu) ? $totalzhichu : 0;?></li>
            <li style='padding-left:70px;'><span>实物商品支出：</</span></li>
            <li style="padding-top:7px;"><?php echo isset($totalshiwu) ? $totalshiwu : 0;?></li>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="100">
        <thead>
            <tr>
                <th width="5%">姓名</th>
                <th width="6%">时间</th>
                <th width="6%">类型</th>
                <th width="6%">收入</th>
                <th width="7%">支出</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)){?>
            <?php foreach($list AS $key=>$value){
	    		$in = $value['action']<50?$value['value']:'';
	    		$out = $value['action']<50?'':$value['value'];
            	?>
                <tr>
                    <td><?php echo empty($names[$value['uid']])?'':$names[$value['uid']] ;?></td>
                    <td><?php echo date('Y-m-d H:i:s',$value['ctime']) ;?></td>
                    <td><?php echo $value['name'];?></td>
                    <td><?php echo $in;?></td>
                    <td><?php echo $out;?></td>
                </tr>
            <?php }?>
		<?php }?>
		</tbody>
	</table>
	<div class="panelBar">
        <div class="pages">
            <span>显示</span>
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
                <option value="20" <?php echo $numPerPage == 20 ? 'selected' : ''; ?>>20</option>
                <option value="40" <?php echo $numPerPage == 40 ? 'selected' : ''; ?>>40</option>
            </select>
            <span>条，共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>

<script type="text/javascript">
function jifenValidateCallback(form) {
	var $form = $(form);

	if (!$form.valid()) {
		return false;
	}
	return navTabSearch(form);
}
</script>
