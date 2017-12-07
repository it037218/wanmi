<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/redbag">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>redbag" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					红包代码：<input name="code" id="code" value="<?php echo isset($code)?$code:''?>"/>
				</td>
				<td>
					红包名称：<input name="name" id="name" value="<?php echo isset($name)?$name:''?>"/>
				</td>
				<td><input type="hidden" value="search" name="op"><button type="submit">检索</button></td>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
  <div class="panelBar">
        <ul class="toolBar">
            <?php if($editable==1){?>
            <li class="line">line</li>
            <li><a title="添加红包"   href="<?php echo OP_DOMAIN; ?>redbag/addRedbag" target="dialog"  class="icon"><span>添加红包</span></a></li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="90">
        <thead>
            <tr>
                <th width="10%">红包代码</th>
                <th width="10%">红包名称</th>
                <th width="10%">红包类型</th>
                <th width="5%">红包金额</th>
                <th width="5%">发放用户</th>
                <th width="7%">状态</th>
                <th width="7%">红包个数</th>
                <th width="7%">已领个数</th>
                <th width="10%">创建时间</th>
                <th width="10%">抢完时间</th>
                <th width="10%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo $value['code'] ;?></td>
                    <td><?php echo $value['name'] ;?></td>
                    <td><?php
                    	if($value['redbag_type']=='1'){
                    		echo '固定金额红包';
                    	}else if($value['redbag_type']=='2'){
                    		echo '随机金额红包';
                    	}
                    ?></td>
                    <td><?php echo $value['money'] ;?></td>
                    <td><?php
                    	if($value['user_type']=='1'){
                    		echo '新用户';
                    	}else if($value['user_type']=='2'){
                    		echo '老用户';
                    	}else if($value['user_type']=='3'){
                    		echo '所有用户';
                    	}
                    ?></td>
                    <td><?php echo empty($value['status'])?'已抢完':'未抢完' ;?></td>
                    <td><?php echo $value['counts'] ;?></td>
                    <td><a href='<?php echo OP_DOMAIN;?>redbag/getList/<?php echo $value['id'] ;?>' target="navtab" title="领取人列表"><?php echo $value['acceptcounts'] ;?></a></td>
                    <td><?php echo date('Y-m-d H:i:s',$value['ctime']); ;?></td>
                    <td><?php echo empty($value['dtime'])?'':date('Y-m-d H:i:s',$value['dtime']); ;?></td>
                    <td>
                    	<a title="删除" href='<?php echo OP_DOMAIN;?>redbag/delredbag/<?php echo $value['id'] ;?>' target="ajaxTodo" title="您真的要撤销吗?">删除</a>
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


