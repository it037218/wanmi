
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
         <?php if($editable==1){?>
            <li><a title="添加栏目类型"    href="<?php echo OP_DOMAIN; ?>authority/addgrouptype" target="dialog"  class="icon"><span>添加栏目类型</span></a></li>
            <li class="line">line</li>
		<?php }?>
        </ul>
    </div>
    <table class="list" width="100%" layoutH="115">
        <tr>
            <th width="10%">序号</th>
            <th width="20%">栏目名</th>
            <th width="30%">操作</th>
        </tr>
        <?php if (!empty($list)): ?>
            <?php foreach ($list AS $key=>$value): ?>
            <tr id="column_<?php echo $value['id']; ?>">
                <td><?php echo $key+1;?></td>
                <td><?php echo $value['name']; ?></td>
                 <?php if($editable==1){?>
                <td><a href="<?php echo OP_DOMAIN;?>authority/editGroupType/<?php echo $value['id']; ?>" target="dialog"  title="编辑">编辑</a>
                &nbsp;|&nbsp;
                <a href="<?php echo OP_DOMAIN;?>authority/delGroupType?cid=<?php echo $value['id']?>" target="ajaxTodo" title="您真的要删除吗?" >删除</a>
               <?php }?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

</html>