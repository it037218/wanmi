
<div class="pageContent">
	<table class="list" width="100%" layoutH="115">
        <thead>
            <tr>
                <th width="5%">产品id</th>
                <th width="10%">产品名称</th>
                <th width="10%">产品类型</th>
                <th width="10%">权重</th>
                <th width="10%">推荐类型</th>
                <th width="10%">添加时间</th>
                <th width="25%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                <td><?php echo $value['pid'] ;?></td>
                <td><?php echo $value['pname'] ;?></td>
                <td><?php echo $value['ptype'] == 0 ? '不定期' : '长期' ;?></td>
                <td><?php echo $value['rank'] ;?></td>                
                <td><?php echo $value['rtype'] == 1 ? '普通推荐' : '其它' ;?></td>
                <td><?php echo date('Y-m-d H:i:s', $value['addtime']) ;?></td>
                <td>
                    <a href="<?php echo OP_DOMAIN;?>recommend/delrecommend/<?php echo $value['pid']?>/<?php echo $value['ptype']; ?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
                </td>
                </tr>
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</div>


