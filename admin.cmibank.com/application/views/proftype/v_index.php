
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
        <?php if($editable==1){?>
            <li><a title="添加类型"   href="<?php echo OP_DOMAIN; ?>proftype/addproftype" target="dialog"  class="icon"><span>添加类型</span></a></li>
            <li class="line">line</li>
        <?php }?>    
        </ul>
    </div>
	<table class="list" width="100%" layoutH="115">
        <thead>
            <tr>
                <th width="25%">业务类型</th>
                <th width="25%">业务名称</th>
                <th width="25%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo $value['proftype'] ;?></td>
                    <td><?php echo $value['profname'] ;?></td>
                    <td>
                    <?php if($editable==1){?>
                        <a href="<?php echo OP_DOMAIN;?>proftype/editproftype/<?php echo $value['profid']?>" target="dialog">编辑</a>
                        <a href="<?php echo OP_DOMAIN;?>proftype/delproftype/<?php echo $value['profid']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
                    <?php }?>
                    </td>
                </tr>
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</div>


