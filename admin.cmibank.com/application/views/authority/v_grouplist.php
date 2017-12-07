
<div class="pageContent">

    <table class="list" width="100%" layoutH="115">
        <tr>
            <th width='10%'>类型</th>
            <th width="10%">小组</th>
            <th width="20%">岗位</th>
            <th width="30%">操作</th>
        </tr>
        <?php if (!empty($group)): ?>
            <?php foreach ($group AS $key=>$value): ?>
            <tr id="column_<?php echo $value['id']; ?>">
                <td width='10%'><?php echo $value['type'];?></td>
                <td><?php echo $value['inner_group'];?></td>
                <td><?php echo $value['name']; ?></td>
                <td>
                <a href="<?php echo OP_DOMAIN;?>authority/delGroupSub/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要删除吗?" >删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

</html>