<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/backcontract">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
        <!-- 
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/aboutus" method="post">
            <li><span>信息名称</</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($aboutustitle)?$aboutustitle:'请输入搜索内容'?>"  id="aboutustitle" name="aboutustitle"></li>
            <li><input type="hidden" value="search_aboutustitle" name="op"><button type="submit" >检索</button></li>
            </form>
             -->
            <li><a title="添加回库单" href="<?php echo OP_DOMAIN; ?>backcontract/addbackcontract" target="dialog" class="icon"><span>添加回库单</span></a></li>
            <li class="line">line</li>
            
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="5%">bid</th>
                <th width="20%">产品名字</th>
                <th width="20%">编号</th>
                <th width="20%">状态</th>
                <th width="20%">创建时间</th>
                <th width="30%">操作</th>
            </tr>
        </thead>
        <tbody>
         <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <?php switch ($value['status']){
                case 1: $status = '已经回库';break;
                case 2: $status = '时间不够';break;
                default: $status = '正常'; break;
            }?>
            <tr>
                <td><?php echo $value['bid'] ;?></td>
                <td><?php echo $value['pname'] ;?></td>
                <td><?php echo $value['number'] ;?></td>
                <td><?php echo $status;?></td>
                <td><?php echo date('Y-m-d',$value['ctime']);?></td>
                <td>
                <?php if($value['status'] == 0 ){?>
                    <a href="<?php echo OP_DOMAIN?>/backcontract/confirmback/<?php echo $value['pid']?>/<?php echo $value['bid']?>" target="ajaxTodo" title="你确定回库么">确定回库</a>
                    <a href="<?php echo OP_DOMAIN?>/backcontract/editbackcontract/<?php echo $value['bid']?>" target="dialog">修改</a>
                <?php }?>    
                    <a href="<?php echo OP_DOMAIN?>/backcontract/delbackcontract/<?php echo $value['bid']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
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
                <option value="30" <?php echo $numPerPage == 30 ? 'selected' : ''; ?>>30</option>
                <option value="50" <?php echo $numPerPage == 50 ? 'selected' : ''; ?>>50</option>
            </select>
            <span>条，共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>


