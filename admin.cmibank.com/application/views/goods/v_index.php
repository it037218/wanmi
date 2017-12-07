<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>goods">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">
  <div class="panelBar">
        <ul class="toolBar">
            <?php if($editable==1){?>
            <li class="line">line</li>
            <li><a title="添加商品"   href="<?php echo OP_DOMAIN; ?>goods/addgoods" target="dialog"  class="icon"><span>添加商品</span></a></li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="10%">名称</th>
                <th width="5%">类型</th>
                <th width="15%">简述</th>
                <th width="3%">库存</th>
                <th width="3%">已售</th>
                <th width="3%">销售积分</th>
                <th width="3%">原始积分</th>
                <th width="3%">图片</th>
                <th width="3%">排序</th>
                <th width="5%">状态</th>
                <th width="8%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo $value['name'] ;?></td>
                    <td><?php 
                    		if($value['type']==1){
                    			echo '体验金';
                    		}else if($value['type']==2){
                    			echo '抵用券';
                    		}else if($value['type']==3){
                    			echo '邀请红包';
                    		}else if($value['type']==4){
                    			echo '实物';
                    		}
                    ?></td>
                    <td><?php echo empty($value['wp'])?'':$value['wp'] ;?></td>
                    <td><?php echo $value['stock']-$value['sold'] ;?></td>
                    <td><?php echo $value['sold'] ;?></td>
                    <td><?php echo $value['jifeng'] ;?></td>
                    <td><?php echo $value['yuanjifeng'] ;?></td>
                    <td><a href="<?php echo $value['img']; ?>" target="_bank">预览</a></td>
                    <td><?php echo $value['rank'];?></td>
                    <td>
                   	 <?php if($editable==1){?>
                   	 	<?php if($value['status']==0){?>
                   	 	未发布
                   	 	<?php }else if($value['status']==1){?>
                   	 			已发布
                   	 	<?php }else{?>
                   	 	已下架
                   	 	<?php }?>
                   	<?php }?>
                    </td>
                    <td>
                   	 <?php if($editable==1){?>
                   	 	<?php if($value['status']==0){?>
                   	 	<a href="<?php echo OP_DOMAIN;?>goods/onLine/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要发布吗?">发布</a>&nbsp&nbsp|&nbsp&nbsp
                   	 	<a href="<?php echo OP_DOMAIN;?>goods/editGoods/<?php echo $value['id']?>" target="dialog">编辑</a>&nbsp&nbsp|&nbsp&nbsp
                   	 	<a href="<?php echo OP_DOMAIN;?>goods/delGoods/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
                   	 	<?php }else if($value['status']==1){?>
                   	 		<a href="<?php echo OP_DOMAIN;?>goods/editGoods/<?php echo $value['id']?>" target="dialog">编辑</a>&nbsp&nbsp|&nbsp&nbsp
                   	 		<a href="<?php echo OP_DOMAIN;?>goods/downLine/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要下架吗?">下架</a>
                   	 	<?php }else if($value['status']==2){?>
                   	 	<a href="<?php echo OP_DOMAIN;?>goods/onLine/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要上架吗?">上架</a>&nbsp&nbsp|&nbsp&nbsp
                   	 	<a href="<?php echo OP_DOMAIN;?>goods/editGoods/<?php echo $value['id']?>" target="dialog">编辑</a>&nbsp&nbsp|&nbsp&nbsp
                   	 	<a href="<?php echo OP_DOMAIN;?>goods/delGoods/<?php echo $value['id']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
                   	 	<?php }?>
                   	<?php }?>
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


