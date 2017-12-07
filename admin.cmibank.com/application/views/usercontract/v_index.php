<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/product">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
        <?php if($editable==1){?>
            <li><a title="新增关联模版" href="<?php echo OP_DOMAIN; ?>usercontract/addusercontract" target="dialog"  class="icon"><span>新增关联模版</span></a></li>
            <li class="line">line</li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="15%">模板名称</th>
                <th width="15%">模板编号</th>
                <th width="15%">模板页面名称</th>
                <th width="15%">业务类型</th>
                <th width="15%">业务名称</th>
                <th width="15%">模板链接</th>
                <th width="15%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo !empty($value['tplname']) ? $value['tplname'] : '末命名' ;?></td>
                    <td><?php echo !empty($value['tplnumber']) ? $value['tplnumber'] : '末编号'; ?></td>
                    <td><?php echo $value['tpl_pagename'] ?></td>
                    <td><?php echo $proftype[$value['profid']]['proftype'] ;?></td>
                    <td><?php echo $proftype[$value['profid']]['profname'] ;?></td>
                    <td><a href="<?php echo !empty($value['tpllink']) ? $value['tpllink']: '';?>?ucid=<?php echo  $value['ucid']; ?> " target="_blank"><?php echo !empty($value['tpllink']) ? $value['tpllink'] : '';?></a></td>
                    <td>
                    <?php if($editable==1){?>
                        <a href="<?php echo OP_DOMAIN;?>usercontract/editusercontract/<?php echo $value['ucid'];?>" target="navtab" title="用户合同编辑">编辑</a>
                        <a href="<?php echo OP_DOMAIN;?>usercontract/delusercontract/<?php echo $value['ucid']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
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


