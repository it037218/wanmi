<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/emailmanage">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <li><a title="新增公司账户" href="<?php echo OP_DOMAIN; ?>emailmanage/addemail" target="dialog"  class="icon"><span>新增公司账户</span></a></li>
            <li class="line">line</li>
            
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="20%">公司名称</th>
                <th width="30%">收件账户</th>
                <th width="15%">抄送账户</th>
                <th width="15%">操作</th>             
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>                
                    <td><?php echo $value['corname']?></td>
                    <td><?php echo $value['address']?></td>
                    <td><?php echo str_replace(',','<br>',$value['copyaddress'])?></td>
                    <td>
                        <a href="<?php echo OP_DOMAIN;?>emailmanage/detailemail/<?php echo $value['corid'];?>" target="dialog" title="查看详情">查看详情</a>
                        <?php if($editable==1){?>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo OP_DOMAIN;?>emailmanage/editemail/<?php echo $value['corid'];?>" target="dialog" title="编辑">编辑</a>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo OP_DOMAIN;?>emailmanage/delemail/<?php echo $value['corid'];?>" target=ajaxTodo title="删除">删除</a>
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
                <option value="30" <?php echo $numPerPage == 30 ? 'selected' : ''; ?>>30</option>
                <option value="50" <?php echo $numPerPage == 50 ? 'selected' : ''; ?>>50</option>
            </select>
            <span>条，共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div> 
</div>


