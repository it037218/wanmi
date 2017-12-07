<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/aboutus">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($aboutustitle)){?>
     <input type="hidden" name="aboutustitle" value="<?php echo $aboutustitle; ?>" />
     <input type="hidden" value="search_aboutustitle" name="op">
     <?php }?>
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/aboutus" method="post">
            <li><span>信息名称</</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($aboutustitle)?$aboutustitle:'请输入搜索内容'?>"  id="aboutustitle" name="aboutustitle"></li>
            <li><input type="hidden" value="search_aboutustitle" name="op"><button type="submit" >检索</button></li>
            </form>
            <?php if($editable==1){?>
            <li><a title="添加"    href="<?php echo OP_DOMAIN; ?>aboutus/addAboutus" target="dialog" class="icon"><span>添加</span></a></li>
            <li class="line">line</li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="5%">id</th>
                <th width="20%">信息名称</th>
                <th width="20%">信息内容</th>
                <th width="20%">添加时间</th>
                <th width="35%">操作</th>
            </tr>
        </thead>
        <tbody>
         <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <tr>
                <td><?php echo $value['aid'] ;?></td>
                <td><?php echo $value['title'] ;?></td>
                <td><?php echo $value['content'] ;?></td>
                <td><?php echo date('Y-m-d',$value['ctime']);?></td>
                <td>
                <?php if($editable==1){?>
                    <a href="<?php echo OP_DOMAIN?>/aboutus/editAboutus/<?php echo $value['aid']?>" target="dialog">修改</a>
                    <a href="<?php echo OP_DOMAIN?>/aboutus/delAboutus/<?php echo $value['aid']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
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


