
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
        <!--  
            <li><a title="添加类型"   href="<?php echo OP_DOMAIN; ?>ltype/addLtype" target="dialog"  class="icon"><span>添加类型</span></a></li>
            <li class="line">line</li>
            -->
        </ul>
    </div>
	<table class="list" width="100%" layoutH="115">
        <thead>
            <tr>
                <th width="5%">类型id</th>
                <th width="10%">类型名称</th>
                <th width="10%">创建时间</th>
                <th width="10%">权重</th>
                <th width="25%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo $value['ptid'] ;?></td>
                    <td><?php echo $value['name'] ;?></td>
                    <td><?php echo date('Y-m-d H:i:s',$value['ctime']);?></td>
                    <td><?php echo $value['rank'] ;?></td>
                    <td>
                    	<?php if($editable==1){?>
                        <a href="<?php echo OP_DOMAIN;?>ltype/editLtype/<?php echo $value['ptid']?>" target="dialog">编辑</a>
                        <!--  <a href="<?php echo OP_DOMAIN;?>ltype/delLtype/<?php echo $value['ptid']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>-->
                   		 <?php }?>
                    </td>
                </tr>
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</div>


