<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/feedback">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageHeader">
    <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN; ?>/feedback" method="post">
    </form>
</div>

<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            
        </ul>
    </div>
   
    <table width="100%" class="list" layoutH="55">
        <tr>
            <th width="5%">序号</th>
            <th width="5%">手机号码</th>
            <th width="70%">意见内容</th>
            <th width="10%">时间</th>
            <th width="5%">状态</th>
            <th width="5%">操作</th>
        </tr>
       
        <?php foreach ($list AS $key => $value): ?>
            <?php 
                switch ($value['status']){
                    case 0 : $status = '未处理'; break;
                    case 1 : $status = '已处理'; break;
                    default: $status = '未知'; break;
                }
            ?>
            <tr>
                <td><?php echo $value['id'];?></td>
                <td><?php echo $value['phone'];?></td>
                <td><?php echo $value['content'];?></td>
                <td><?php echo date('Y-m-d H:i:s',$value['ctime']);?></td>
                <td><?php echo $status;?></td>
                <td>
                	<?php if($editable==1){?>
                    <?php if($value['status']==0){?>
                    <a href="<?php echo OP_DOMAIN;?>/feedback/Handle/<?php echo $value['id'];?>" target="ajaxTodo" title="真的处理了么？">处理</a>
                    <?php }?>
                    <?php }?>
                </td>
            </tr>
        <?php endforeach; ?>
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



        
