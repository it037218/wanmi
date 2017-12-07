<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/expmoneyactivity">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">
  <div class="panelBar">
        <ul class="toolBar">
            <?php if($editable==1){?>
            <li class="line">line</li>
            <li><a title="添加体验金活动"   href="<?php echo OP_DOMAIN; ?>expmoneyactivity/addExpmoneyActivity" target="dialog"  class="icon"><span>添加体验金活动</span></a></li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="10%">活动名称</th>
                <th width="5%">活动类型</th>
                <th width="5%">体验金金额</th>
                <th width="20%">活动时间</th>
                <th width="8%">状态</th>
                <th width="8%">操作</th>
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
                    		echo '注册赠送';
                    	}else if($value['type']=='2'){
                    		echo '购买赠送';
                    	}else if($value['type']=='3'){
                    		echo '系统赠送';
                    	}else
                    ?></td>
                    <td><?php echo $value['money'] ;?></td>
                    <td><?php 
                    		echo date('Y-m-d H:i:s',$value['stime']).'  至     '.date('Y-m-d H:i:s',$value['etime']);
                    ?></td>
                     <td>
                   	 <?php if($editable==1){?>
                   	 	<?php if($value['status']==1){?>
                   	 	未发布
                   	 	<?php }else if($value['status']==2){?>
                   	 		<?php if($value['stime']>NOW){?>
                   	 			待发布
                   	 		<?php }else{?>
		                   	 	发布中
                   	 		<?php }?>
                   	 	<?php }else{?>
                   	 	已下架
                   	 	<?php }?>
                   	<?php }?>
                    </td>
                    <td>
                   	 <?php if($editable==1){?>
                   	 	<?php if($value['status']==1){?>
                   	 	<a href="<?php echo OP_DOMAIN;?>expmoneyactivity/onLine/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要发布吗?">发布</a>&nbsp&nbsp|&nbsp&nbsp
                   	 	<a href="<?php echo OP_DOMAIN;?>expmoneyactivity/editExpmoneyActivity/<?php echo $value['id']?>" target="dialog">编辑</a>&nbsp&nbsp|&nbsp&nbsp
                   	 	<a href="<?php echo OP_DOMAIN;?>expmoneyactivity/delExpmoneyActivity/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
                   	 	<?php }else if($value['status']==2){?>
                   	 		<a href="<?php echo OP_DOMAIN;?>expmoneyactivity/downLine/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要下架吗?">下架</a>
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


