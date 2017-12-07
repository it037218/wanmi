<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/buchang">
    <input type="hidden" name="pageNum" value="<?php echo isset($pageNum) ? $pageNum : 0; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo isset($numPerPage) ? $numPerPage : 0; ?>" />
</form>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
        	<?php if($editable==1){?>
<!--            <li><a title="新建补偿单"   href="--><?php //echo OP_DOMAIN; ?><!--buchang/addbuchang?num=one" target="dialog"  class="icon"><span>新建补偿单</span></a></li>-->
                <li><a title="新建补偿单"   href="<?php echo OP_DOMAIN; ?>buchang/addbuchang?num=more" target="dialog"  class="icon"><span>新建补偿单</span></a></li>
                <li><a title="确实要撤销这些记录吗?" target="selectedTodo" rel="check_all" postType="string" href="<?php echo OP_DOMAIN; ?>buchang/back_shenghe" class="delete"><span>批量撤销</span></a></li>
                <li class="line">line</li>
            <?php }?>
        </ul>
    </div>

	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="3%"><input type="checkbox" group="check_all" name="selectall" class="checkboxCtrl"></th>
                <th width="3%">id</th>
                <th width="8%">补尝类型</th>
                 <th width="8%">补尝金额</th>
                <th width="8%">申请时间</th>
                <th width="8%">审核时间</th>
                <th width="5%">补尝人UID</th>
                <th width="15%">备注</th>
                <th width="15%">状态</th>
                <th width="15%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php if($list){?>
            <?php foreach ($list as $value){?>
            <tr>
                <?php 
                    if($value['btype'] == 1){
                        $btype = '老用户买送奖励';
                    }else if($value['btype'] == 2){
                        $btype = '邀请奖励补发';
                    }else if($value['btype'] == 3){
                        $btype = '取现失败补偿';
                    }else if($value['btype'] == 4){
                        $btype = '充值失败补偿';
                    }else{
                        $btype = '末知';
                    }
                ?>
                <td><input name="check_all" value="<?php echo $value['bid']?>" type="checkbox"></td>
                <td><?php echo $value['bid'] ;?></td>
                <td><?php echo $btype ;?></td>
                <td><?php echo $value['money'] ;?></td>
                <td><?php echo date('Y-m-d H:i:s',$value['ctime']) ;?></td>
                <td><?php echo date('Y-m-d H:i:s',$value['sh_time']);?></td>
                <td><?php echo $value['uid'] ;?></td>
                <td><?php echo $value['desc'] ;?></td>
                <td><?php echo $value['status'] ? '已审核' : '末审核' ;?></td>
                <td><?php if($editable==1){?><a title="撤销" href='<?php echo OP_DOMAIN;?>/buchang/back_shenghe?code=<?php echo $value['bid'] ;?>' target="ajaxTodo" title="您真的要撤销吗?">撤销</a><?php }?></td>
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

