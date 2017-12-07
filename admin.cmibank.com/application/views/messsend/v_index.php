<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/messsend">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">
  <div class="panelBar">
        <ul class="toolBar">
            <?php if($editable==1){?>
            <li><a title="新建消息"   href="<?php echo OP_DOMAIN; ?>messsend/addMessSend" target="dialog"  class="icon"><span>新建消息</span></a></li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="65">
        <thead>
            <tr>
                <th width="10%">消息主题</th>
                <th width="5%">发送用户</th>
                <th width="25%">消息内容</th>
                <th width="5%">链接</th>
                <th width="5%">发送时间</th>
                <th width="3%">状态</th>
                <th width="8%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo $value['title'] ;?></td>
                    <td><?php
                    	if($value['type']=='1'){
                    		echo '所有用户';
                    	}else if($value['type']=='2'){
                    		echo '指定用户';
                    	}
                    ?></td>
                    <td><?php echo $value['content'] ;?></td>
                    <td><a href='<?php echo $value['link'] ;?>' target="_blank">查看</a></td>
                    <td><?php 
                    	if(!empty($value['stime'])){
                    		echo date('Y-m-d H:i:s',$value['stime']);
                    	}
                    ?></td>
                     <td>
                   	 <?php if($editable==1){?>
                   	 	<?php if($value['status']==1){?>
                   	 	已发放
                   	 	<?php }else{?>
                   	 	待发放
                   	 	<?php }?>
                   	<?php }?>
                    </td>
                    <td>
                   	 <?php if($editable==1){?>
                   	 	<?php if(empty($value['status'])){?>
                   	 		<a href="<?php echo OP_DOMAIN;?>messsend/onLine/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要发送吗?">发送</a>
                        	<a href="<?php echo OP_DOMAIN;?>messsend/editMessSend/<?php echo $value['id']?>" target="dialog">|&nbsp&nbsp编辑</a>&nbsp&nbsp|&nbsp&nbsp
                        	<a href="<?php echo OP_DOMAIN;?>messsend/delMessSend/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
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


