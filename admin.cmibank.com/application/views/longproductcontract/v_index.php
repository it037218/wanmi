<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/product">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">
	<div class="panelBar">
	<!--  
        <ul class="toolBar">
            <li><a title="新建合同" href="<?php echo OP_DOMAIN; ?>contract/addcontract" target="navtab"  class="icon"><span>添加合同</span></a></li>
            <li class="line">line</li>
            
        </ul>
        -->
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="20%">id</th>
                <th width="20%">活期模板名字</th>
                <th width="20%">创建时间</th>
                <th width="40%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                <td><?php echo $value['cid'] ;?></td>
                <td><?php echo $value['corname'] ?></td>
                <td><?php echo date('Y-m-d',$value['ctime']);?></td>
                <td>
                <?php if($editable==1){?>
                <a href="<?php echo OP_DOMAIN;?>longproductcontract/editLongproductcontract/<?php echo $value['cid'];?>" target="navtab" title="合同编辑">编辑</a>
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


