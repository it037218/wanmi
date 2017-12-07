<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <li><a title="添加部门"    href="<?php echo OP_DOMAIN; ?>authority/addgroupmaster" target="dialog"  class="icon"><span>添加部门</span></a></li>
            <li class="line">line</li>
            <li><a title="添加岗位"    href="<?php echo OP_DOMAIN; ?>authority/addgroupmem" target="dialog"  class="icon"><span>添加岗位</span></a></li>
            <li class="line">line</li>
            <li><a title="删除岗位"    href="<?php echo OP_DOMAIN; ?>authority/grouplist" target="navtab"  class="icon"><span>删除岗位</span></a></li>
            <li class="line">line</li>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="115">
        <thead>
            <tr>
                <th width="15%">部门</th>
                <th width="15%">岗位</th>                
                <th width="15%">类型</th>
                <th width="20%">成员</th>  
                <th width="35%">操作</th>                
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>

				<?php if(!empty($value['list'])):?>
                    <?php foreach($value['list'] AS $k=>$v):?>
                        <?php if(!empty($v['member'])):?>
                            <?php foreach($v['member'] AS $user):?>
                                <tr>
                                
                                <td><?php echo $value['inner_group'] ;?></td>
                                <?php if($oldname == $v['name']){?>
                                <td> -- </td>
                                <?php }else{?>
                                <td><a href = '<?php echo OP_DOMAIN; ?>/authority/set/<?php echo $user['group_id'];?>' target="navTab"><?php echo $v['name'] ;?></a></td>
                                <?php }?>
                                <td><?php echo $user['realname'] ;?></td>
                                <td><?php echo $value['inner_group'] ;?></td>
                                <?php $oldname = $v['name']; ?>
                                <td>
                                    <?php if($editable==1){?>
	                                    <?php if($user['group_id'] != 1){?>
	                                        <a href="<?php echo OP_DOMAIN;?>authority/editUserGroup/<?php echo $user['user_id'];?>" target="dialog" title="分组设置">分组设置</a>
	                                        &nbsp;|&nbsp;
	                                        <a href="<?php echo OP_DOMAIN;?>authority/delGroupMember?gid=<?php echo $user['group_id']?>&mid=<?php echo $user['user_id']?>" target="ajaxTodo" title="您真的要删除用户吗?">删除</a>
	                                    <?php }?>
                                     <?php }?>
                                </td>
                                </tr>
                            <?php endforeach;?>
				        <?php endif;?>
                    <?php endforeach;?>
				<?php endif;?>
			 
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</div>


