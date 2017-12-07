
<div class="pageContent">

	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="5%">产品编号</th>
               
                <th width="25%">操作</th>               
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>                
                <td><?php echo $value['name'] ;?></td>
                <td>
                 <?php if($editable==1){?>
                    <a href="<?php echo OP_DOMAIN . $value['uri'];?>" target="ajaxTodo" title="确定要重建吗?">重建</a>
                    <?php }?>
                </td>
                </tr>
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
	
</div>


