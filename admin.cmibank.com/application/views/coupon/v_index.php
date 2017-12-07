<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/coupon">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">
  <div class="panelBar">
        <ul class="toolBar">
            <?php if($editable==1){?>
            <li class="line">line</li>
            <li><a title="添加抵用券"   href="<?php echo OP_DOMAIN; ?>coupon/addcoupon" target="dialog"  class="icon"><span>添加抵用券</span></a></li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="10%">抵用券名称</th>
                <th width="3%">金额</th>
                <th width="15%">适用产品</th>
                <th width="5%">起购金额</th>
                <th width="8%">有效期</th>
                <th width="6%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo $value['name'] ;?></td>
                    <td><?php echo $value['sendmoney'] ;?></td>
                    <td><?php echo $value['pnames'] ;?></td>
                    <td><?php echo $value['minmoney'];?></td>
                    <td><?php 
                    	if(empty($value['days'])){
                    		echo (empty($value['stime'])?'':date('Y-m-d',$value['stime'])).'至'.date('Y-m-d',$value['etime']);
                    	}else{
                    		echo $value['days'].'天';
                    	}
                    ?></td>
                    <td>
                   	 <?php if($editable==1){?>
                        <a href="<?php echo OP_DOMAIN;?>coupon/editCoupon/<?php echo $value['id']?>" target="dialog">编辑</a>&nbsp&nbsp|&nbsp&nbsp
                        <a href="<?php echo OP_DOMAIN;?>coupon/delCoupon/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
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


