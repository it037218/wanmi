<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>ranksend">
    <input type="hidden" name="pageNum" value="<?php echo isset($pageNum) ? $pageNum : 0; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo isset($numPerPage) ? $numPerPage : 0; ?>" />
</form>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
        	<?php if($editable==1){?>
            <li><a title="新建补偿单"   href="<?php echo OP_DOMAIN; ?>ranksend/add" target="dialog"  class="icon"><span>新建奖励</span></a></li>
            <li class="line">line</li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="3%">id</th>
                <th width="4%">奖励金额</th>
                <th width="9%">创建时间</th>
                <th width="9%">发放时间</th>
                <th width="30%">账号</th>
                <th width="4%">奖励人数</th>
                <th width="10%">备注</th>
                <th width="4%">状态</th>
                <th width="8%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php if($list){?>
            <?php foreach ($list as $value){?>
            <tr>
                <td><?php echo $value['id'] ;?></td>
                <td><?php echo $value['money'] ;?></td>
                <td><?php echo date('Y-m-d H:i:s',$value['ctime']) ;?></td>
                <td><?php echo empty($value['stime'])?'':date('Y-m-d H:i:s',$value['stime']);?></td>
                <td><?php echo $value['accounts'] ;?></td>
                <td><?php echo $value['counts'] ;?></td>
                <td><?php echo $value['remark'] ;?></td>
                <td><?php echo $value['status'] ? '已发放' : '末发放' ;?></td>
                <td>
                	<?php if($editable==1){?>
                		<?php if(empty($value['status'])){?>
                			<a title="删除" href='<?php echo OP_DOMAIN;?>/ranksend/del/<?php echo $value['id'] ;?>' target="ajaxTodo" title="您真的要撤销吗?">删除</a>
                			<a title="编辑" href='<?php echo OP_DOMAIN;?>/ranksend/update/<?php echo $value['id'] ;?>' target="dialog">编辑</a>
                			<a title="发放" href='<?php echo OP_DOMAIN;?>/ranksend/init/<?php echo $value['id'] ;?>' target="dialog">发放</a>
                		<?php }else{?>
               				<a title="查看" href='<?php echo OP_DOMAIN;?>/ranksend/detail/<?php echo $value['id'] ;?>' target="dialog">查看</a>
               				<?php }?>
                	<?php }?>
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
                <option value="30" <?php echo $numPerPage == 30 ? 'selected' : ''; ?>>30</option>
                <option value="50" <?php echo $numPerPage == 50 ? 'selected' : ''; ?>>50</option>
            </select>
            <span>条，共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>


