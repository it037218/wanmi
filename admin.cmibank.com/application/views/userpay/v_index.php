<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/userpay">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/userpay" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					注册手机号：<input type="text"  value="<?php echo isset($account)?$account:''?>"  id="id_account" name="account">
					<input type="hidden" value="search" name="op">
				</td>
				<td><button type="submit">检索</button></td>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
  <div class="panelBar">
        <ul class="toolBar">
            <?php if($editable==1){?>
            <li><a title="添加用户充值"   href="<?php echo OP_DOMAIN; ?>userpay/adduserpay" target="dialog"  class="icon"><span>添加用户充值</span></a></li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="85">
        <thead>
            <tr>
                <th width="10%">电话号码</th>
                <th width="10%">姓名</th>
                <th width="5%">金额</th>
                <th width="10%">创建时间</th>
                <th width="10%">充值时间</th>
                <th width="10%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo $value['account'] ;?></td>
                    <td><?php echo empty($names[$value['uid']])?'':$names[$value['uid']] ;?></td>
                    <td><?php echo $value['money'] ;?></td>
                    <td><?php echo date('Y-m-d H:i:s',$value['ctime']);?></td>
                    <td><?php echo date('Y-m-d H:i:s',$value['dtime']);?></td>
                    <td>
                   	 <?php if($editable==1){?>
                   	 	<?php if($value['status']==0){?>
                   	 		<a href="<?php echo OP_DOMAIN;?>userpay/initUserpay/<?php echo $value['id']?>" target="dialog">充值</a>&nbsp&nbsp|&nbsp&nbsp
	                        <a href="<?php echo OP_DOMAIN;?>userpay/editUserpay/<?php echo $value['id']?>" target="dialog">编辑</a>&nbsp&nbsp|&nbsp&nbsp
	                        <a href="<?php echo OP_DOMAIN;?>userpay/delUerpay/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
                   	 	<?php }else{?>
                   	 		已充值&nbsp&nbsp|&nbsp&nbsp
                   	 		<a href="<?php echo OP_DOMAIN;?>userpay/detail/<?php echo $value['id']?>" target="dialog">查看</a>
                   	 	<?php }?>
                   	<?php }else{?>
                   		<a href="<?php echo OP_DOMAIN;?>userpay/detail/<?php echo $value['id']?>" target="dialog">查看</a>
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


