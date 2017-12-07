<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>manager">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
     <?php if(isset($title)){?>
     <input type="hidden" name="username" value="<?php echo $title; ?>" />
     <?php }?>
</form>
<div class="pageHeader">
    <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN; ?>/manager" method="post">
        <div class="searchBar">
            <table class="searchContent">   
                <tr>
                    <td>
                        <input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($title)?$title:'请输入搜索内容'?>" id="username" name="username">
                    </td>
                </tr>
            </table>
            <div class="subBar">
                <ul>
                    <li><div class="buttonActive"><div class="buttonContent"><input type="hidden" value="search" name="op"><button type="submit">检索</button></div></div></li>
                </ul>
            </div>
        </div>
    </form>
</div>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
         <?php if($editable==1){?>
            <li><a title="添加新用户" target="dialog" href="<?php echo OP_DOMAIN; ?>manager/add" class="icon"><span>添加新用户</span></a></li>
            <li class="line">line</li>
            <?php }?>
        </ul>
    </div>
    <table class="list" width="100%" layoutH="115">
        <tr>
			<th width="10%">序号</th>
            <th width="10%">账号</th>
			<th width="20%">用户名</th>
			<th width="10%">所属分组</th>
			<th width="10%">岗位</th>
			<th width="15%">添加时间</th>
			<th width="15%">操作</th>
		</tr>
		<?php if(!empty($list)):?>
		<?php foreach($list AS $key=>$value):?>
		<tr>
            <td><?php echo $key+1;?></td>
			<td><?php echo $value['name'];?></td>
			<td><?php echo $value['realname'];?></td>
			<td><?php echo $value['inner_group'];?></td>
			<td><?php echo $value['post'];?></td>
			<td><?php echo date("Y-m-d" , $value['createTime']);?></td>
			<td>
			<?php if($editable==1){?>
				<?php if($value['name'] != 'admin'):?>
				<a href="<?php echo OP_DOMAIN;?>manager/modify/<?php echo $value['id'];?>" target="dialog" title="编辑">编辑</a>
				&nbsp;|&nbsp;
				<a href="<?php echo OP_DOMAIN; ?>/manager/delete?idlist=<?php echo $value['id'];?>" target="ajaxTodo" title="您真的要删除用户吗?">删除</a>
				<?php endif;?>
				<?php }?>
			</td>
			
		</tr>
		<?php endforeach;?>
    <?php endif;?>
    
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
