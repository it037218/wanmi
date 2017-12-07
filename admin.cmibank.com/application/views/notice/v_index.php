<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/notice">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/notice" method="post">
            <li><span>信息名称</</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($aboutustitle)?$aboutustitle:'请输入搜索内容'?>"  id="aboutustitle" name="aboutustitle"></li>
            <li><input type="hidden" value="search_aboutustitle" name="op"><button type="submit" >检索</button></li>
            </form>
            <?php if($editable==1){?>
            <li><a title="添加产品"    href="<?php echo OP_DOMAIN; ?>notice/addNotice" target="navtab" class="icon"><span>新增广告消息</span></a></li>
            <li class="line">line</li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="10%">id</th>
                <th width="10%">公告主题</th>
                <th width="10%">公告内容</th>
                <th width="10%">发布日期</th>
                <th width="10%">预约发布时间</th>
                <th width="10%">发布状态</th>
                <th width="10%">发布类型</th>
                <th width="30%">操作</th>
            </tr>
        </thead>
        <tbody>
         <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <?php 
            switch ($value['status']){
                case 0: $status = '待发布';break;
                case 1: $status = '已发布';break;
                default: $status = '末知状态'; break;
            }
            switch ($value['type']){
                case 0: $type = '广播';break;
                case 1: $type = '注册用户';break;
                case 2: $type = '非注册用户';break;
                case 3: $type = '所有交易用户';break;
                case 4: $type = '第一次交易用户';break;
                case 5: $type = '第二次交易用户';break;
                default: $type = '末知状态'; break;
            }
            ?>
            <tr>
                <td><?php echo $value['nid']?></td>
                <td><?php echo $value['title']?></td>
                <td><?php echo $value['content']?></td>
                <td><?php echo ($value['status']!=0) ? date('Y-m-d H:i:s',$value['onlinetime']) :'--';?></td>
                <td><?php echo $value['yugaotime']?></td>
                <td><?php echo $status;?></td>
                <td><?php echo $type;?></td>
                <td>
                <?php if($editable==1){?>
                <?php if($value['status'] ==0){?>
                   <a href="<?php echo OP_DOMAIN?>/notice/uptoline/<?php echo $value['nid']?>" target="ajaxTodo" title="确定要发布">发布</a>
                <?php }?>
                    <a href="<?php echo OP_DOMAIN?>/notice/editNotice/<?php echo $value['nid']?>" target="dialog">修改</a>
                    <a href="<?php echo OP_DOMAIN?>/notice/delNotice/<?php echo $value['nid']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
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


