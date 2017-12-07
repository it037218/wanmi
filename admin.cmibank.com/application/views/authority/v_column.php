<div id="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
        <?php if($editable==1){?>
            <li><a title="添加栏目"    href="<?php echo OP_DOMAIN; ?>authority/addColumn" target="dialog"  class="icon"><span>添加栏目</span></a></li>
            <li class="line">line</li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="115">
        <thead>
            <tr>
                <th width="20%">栏目名</th>
                <th width="20%">所属分组</th>
                <th width="40%">URL</th>
                <th width="10%">状态</th>
                <th width="10%">操作</th>    
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($list)): ?>
                <?php foreach ($list AS $value): ?>
                <tr>
                    <td><?php echo $value['name']; ?></td>
                    <td><?php echo $value['group_name']; ?></td>
                    <td><?php echo $value['url']; ?></td>
                    <td><?php echo $value['status']==1 ? '打开' : '关闭'; ?></td>
                    <td>
                    <?php if($editable==1){?>
                        <a href="<?php echo OP_DOMAIN;?>authority/editColumn/<?php echo $value['id']; ?>" target="dialog" title="编辑" >编辑</a>
                        &nbsp;|&nbsp;
                        <a href="<?php echo OP_DOMAIN;?>authority/delColumn?cid=<?php echo $value['id']; ?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
                    <?php }?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>        
    </table>
</div>
