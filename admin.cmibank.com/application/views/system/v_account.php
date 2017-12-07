<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>system/index">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">
		<h2 class="contentTitle">最近登录IP</h2>
		<table width="100%" class="list" layoutH="65">
			<tr>
				<th width="198">IP地址</th>
				<th>IP所在地</th>
				<th width="198">登陆时间</th>
				<th>错误提示</th>
				<th width="68">状态</th>
			</tr>
			<?php if(!empty($list)):?>
			<?php foreach($list AS $key=>$value):?>
			<tr>
				<td><?php echo $value['user_ip'];?></td>
				<td><?php echo $value['address'];?></td>
				<td><?php echo date("Y-m-d H:i:s", $value['loginTime']);?></td>
				<td><?php echo $value['content'];?></td>
				<td><?php echo $value['status'];?></td>
			</tr>
			<?php endforeach;?>
			<?php endif;?>
		</table>
		<div class="panelBar">
            <div class="pages">
                <span>显示</span>
                <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
                    <option value="30" <?php echo $numPerPage == 30 ? 'selected' : ''; ?>>30</option>
                    <option value="50" <?php echo $numPerPage == 50 ? 'selected' : ''; ?>>50</option>
                </select>
                <span>条，共<?php echo $count; ?>条</span>
            </div>
            <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
        </div>
</div>
