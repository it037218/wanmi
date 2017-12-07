<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>duihuan">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <input type="hidden" value="search" name="op">
    <input type="hidden" value="<?php echo isset($account)?$account:''?>" name="account">
    <input type="hidden" value="<?php echo $type; ?>" name="type">
     <input type="hidden" value="<?php echo $status; ?>" name="status">
    <input type="hidden" value="<?php echo isset($stime)?$stime:''?>" name="stime">
    <input type="hidden" value="<?php echo isset($etime)?$etime:''?>" name="etime">
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>duihuan" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					手机号码：<input type="text"  value="<?php echo isset($account)?$account:''?>"  id="id_account" name="account" class='filed-text required'>
				</td>
				<td>
				商品类型：
					<select name="type">
						<option value="0" <?php if($type == 0){ echo 'selected';}?>>全部</option>
						<option value="1" <?php if($type == 1){ echo 'selected';}?>>体验金</option>
						<option value="2" <?php if($type == 2){ echo 'selected';}?>>抵用券</option>
						<option value="3" <?php if($type == 3){ echo 'selected';}?>>邀请红包</option>
						<option value="4" <?php if($type == 4){ echo 'selected';}?>>实物商品</option>
					</select>
				</td>
				<td>
					状态：
					<select name="status">
						<option value="3" <?php if($status == 3){ echo 'selected';}?>>全部</option>
						<option value="1" <?php if($status == 1){ echo 'selected';}?>>已使用</option>
						<option value="0" <?php if($status == 0){ echo 'selected';}?>>未使用</option>
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
	<table class="list" width="100%" layoutH="65">
        <thead>
            <tr>
            	<th width="1%">序号</th>
            	<th width="4%">电话号码</th>
                <th width="3%">姓名</th>
                <th width="6%">时间</th>
                <th width="5%">类型</th>
                <th width="6%">物品名称</th>
                <th width="4%">积分</th>
                <th width="4%">支出</th>
                <th width="4%">状态</th>
                <th width="4%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)){?>
            <?php foreach($list AS $key=>$value){
            	$name = '';
            	switch ($value['type'])
	            {
	            case 1:
	            	$name='体验金';
	            	break;
	    		case 2:
	    			$name='抵用券';
	    			break;
	    		case 3:
	    			$name='邀请红包';
	    			break;
	    		case 4:
	    			$name='实物商品';
	    			break;
	    		}
            	?>
                <tr><td><?php echo $key+1;?></td>
                	<td><?php echo empty($phones[$value['uid']])?'':$phones[$value['uid']] ;?></td>
                    <td><?php echo empty($names[$value['uid']])?'':$names[$value['uid']] ;?></td>
                    <td><?php echo date('Y-m-d H:i:s',$value['ctime']) ;?></td>
                    <td><?php echo $name;?></td>
                    <td><?php echo $value['name'];?></td>
                    <td><?php echo $value['jifeng'];?></td>
                    <td><?php echo $value['realmoney'];?></td>
                    <td><?php echo $value['status']==0?'未使用':'已使用';?></td>
                    <td>
                    	<?php if($editable==1){if($value['type']==4 && $value['status']==0){?>
                    		<a title="兑换" href='<?php echo OP_DOMAIN;?>/duihuan/initDuihuan/<?php echo $value['wid'] ;?>' target="dialog" title="您真的要兑换吗?">兑换</a>
                    	<?php }}?>
                    </td>
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
