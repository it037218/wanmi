<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/version">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($features)){?>
     <input type="hidden" name="features" value="<?php echo $features; ?>" />
     <input type="hidden" value="search_features" name="op">
     <?php }?>
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/version" method="post">
            <li><span>信息名称</</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($features)?$features:'请输入搜索内容'?>"  id="features" name="features"></li>
            <li><input type="hidden" value="search_features" name="op"><button type="submit" >检索</button></li>
            </form>
            <?php if($editable==1){?>
            <li><a title="新增版本信息"    href="<?php echo OP_DOMAIN; ?>version/addVersion" target="dialog" class="icon"><span>新增版本信息</span></a></li>
            <li class="line">line</li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="5%">id</th>
                <th width="20%">版本号</th>
                <th width="20%">版本特征</th>
                <th width="20%">上线时间</th>
                <th width="35%">操作</th>
            </tr>
        </thead>
        <tbody>
         <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <tr>
                <td><?php echo $value['vid'] ;?></td>
                <td><?php echo $value['number'] ;?></td>
                <td><?php echo $value['features'] ;?></td>
                <td><?php echo $value['linetime'] ;?></td>
                <td>
                <?php if($editable==1){?>
                    <a href="<?php echo OP_DOMAIN?>/version/editVersion/<?php echo $value['vid']?>" target="dialog">修改</a>
                    <a href="<?php echo OP_DOMAIN?>/version/delVersion/<?php echo $value['vid']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
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
                <option value="30" <?php echo $numPerPage == 30 ? 'selected' : ''; ?>>30</option>
                <option value="50" <?php echo $numPerPage == 50 ? 'selected' : ''; ?>>50</option>
            </select>
            <span>条，共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>


