<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/chongzhi">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <input type="hidden" value="search" name="op">
</form>


<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/chongzhi" method="post">
	<div class="searchBar">
		<table class="searchContent">
		<tr>
			<td>类型:</td>
			<td>
				<select name="type">
					<option value="0" >全部</option>
					<option value="1" <?php if($type == 1){ echo 'selected';}?>>活期利息</option>
					<option value="2" <?php if($type == 2){ echo 'selected';}?>>运营</option>
					<option value="3" <?php if($type == 3){ echo 'selected';}?>>宝付手续费</option>
					<option value="4" <?php if($type == 4){ echo 'selected';}?>>富友手续费</option>
				</select>
			</td>
			<td>日期:</td>
			<td>
				<input type="text"  value="<?php echo isset($stime)?$stime:''?>"  id="id_stime" name="stime" class="date">&nbsp;&nbsp;至
        		<input type="text"  value="<?php echo isset($etime)?$etime:''?>"  id="id_etime" name="etime" class="date">
			</td>
			<input type="hidden" value="search" name="op">
			<td><div class="buttonActive"><div class="buttonContent"><button type="submit">检索</button></div></div></td>
		</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
  <div class="panelBar">
        <ul class="toolBar">
            <?php if($editable==1){?>
            <li class="line">line</li>
            <li><a title="充值"   href="<?php echo OP_DOMAIN; ?>chongzhi/addchongzhi" target="dialog"  class="icon"><span>添加充值</span></a></li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="115">
        <thead>
            <tr>
                <th width="10%">类型</th>
                <th width="10%">时间</th>
                 <th width="5%">金额</th>
                <th width="10%">备注</th>
                <th width="10%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    
                    <td><?php if ($value['type'] == 1){
                    	echo "活期利息";
                    }else{
                    	echo "运营费用";
                    }
                    ?></td>
                    <td><?php echo date('Y-m-d ',$value['ctime']);?></td> 
                    <td><?php echo $value['money'] ;?></td>
                    <td><?php echo $value['remark'];?></td>
                    <td>
                    	<a href='<?php echo $value['url'];?>' id="_open_service_image" target="_blank">查看凭证</a>
                   	 <?php if($editable==1){?>
                        &nbsp&nbsp|&nbsp&nbsp<a href="<?php echo OP_DOMAIN;?>chongzhi/editChongzhi/<?php echo $value['id']?>" target="dialog">编辑</a>
                        &nbsp&nbsp|&nbsp&nbsp<a href="<?php echo OP_DOMAIN;?>chongzhi/delChongzhi/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
                   	<?php }?>
                    </td>
                </tr>
            <?php endforeach;?>
		<?php endif;?>
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