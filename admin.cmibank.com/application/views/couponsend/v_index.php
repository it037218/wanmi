<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/couponsend">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">
  <div class="panelBar">
        <ul class="toolBar">
            <?php if($editable==1){?>
            <li class="line">line</li>
            <li><a title="发放抵用券"   href="<?php echo OP_DOMAIN; ?>couponsend/addCouponSend" target="dialog"  class="icon"><span>发放抵用券</span></a></li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="65">
        <thead>
            <tr>
                <th width="10%">活动名称</th>
                <th width="10%">发放用户</th>
                <th width="15%">赠送抵用券</th>
                <th width="10%">创建时间</th>
                <th width="10%">发放时间</th>
                <th width="7%">发放人数</th>
                <th width="7%">发放券数</th>
                <th width="10%">状态</th>
                <th width="10%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo $value['name'] ;?></td>
                    <td><?php
                    	if($value['type']=='1'){
                    		echo '所有用户';
                    	}else if($value['type']=='2'){
                    		echo '指定用户';
                    	}
                    ?></td>
                    <td><?php echo $value['cnames'] ;?></td>
                    <td><?php echo date('Y-m-d H:i:s',$value['ctime']); ;?></td>
                    <td><?php 
                    	if(!empty($value['stime'])){
                    		echo date('Y-m-d H:i:s',$value['stime']);
                    	}
                    ?></td>
                    <td><?php echo $value['usertotal'] ;?></td>
                    <td><?php echo $value['coupontotal'] ;?></td>
                     <td>
                   	 <?php if($editable==1){?>
                   	 	<?php if($value['status']==1){?>
                   	 	未发放&nbsp&nbsp|&nbsp&nbsp
                   	 	<a href="<?php echo OP_DOMAIN;?>couponsend/onLine/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要发放吗?">发放</a>
                   	 	<?php }else{?>
                   	 	已发放
                   	 	<?php }?>
                   	<?php }?>
                    </td>
                    <td>
                   	 	<a href="<?php echo OP_DOMAIN;?>couponsend/detail/<?php echo $value['id']?>" target="dialog">查看&nbsp&nbsp</a>
                   	 <?php if($editable==1){?>
                   	 	<?php if($value['status']==1){?>
                        <a href="<?php echo OP_DOMAIN;?>couponsend/editCouponSend/<?php echo $value['id']?>" target="dialog">|&nbsp&nbsp编辑</a>&nbsp&nbsp|&nbsp&nbsp
                        <a href="<?php echo OP_DOMAIN;?>couponsend/delCouponSend/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
                        <?php }?>
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


