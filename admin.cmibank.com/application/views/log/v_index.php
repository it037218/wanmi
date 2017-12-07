<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>log">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($title)){?>
        <input type="hidden" name="search_keyword" value="<?php echo $title; ?>" />
    <?php }?>
</form>
<div class="pageHeader">
    <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN; ?>/log" method="post">
        <div class="searchBar">
            <table class="searchContent">   
                <tr>
                    <td>
                        <input type="hidden" name="op" value="search" />
                        <input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($title)?$title:'请输入搜索内容'?>" id="search_keyword" name="search_keyword">
                    </td>
                </tr>
            </table>
            <div class="subBar">
                <ul>
                    <li><div class="buttonActive"><div class="buttonContent"><input type="hidden" value="search" name="op"><button type="submit">检索</button></div></div></li>
                </ul>
            </div>
        </div>
    </form>
</div>

<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
         <?php if($editable==1){?>
            <li><a title="删除操作记录" target="dialog" href="<?php echo OP_DOMAIN; ?>log/delete" class="icon"><span>删除操作记录</span></a></li>
            <li class="line">line</li>
            <?php }?>
        </ul>
    </div>
   
    <table width="100%" class="list" layoutH="115">
        <tr>
            <th width="10%">序号</th>
            <th width="15%">操作模块<?php echo $orderby == 'model' ? $asc == 'DESC' ? '(↓)' : '(↑)'  : ''; ?></th>
            <th width="10%">操作ID</th>
            <th width="20%">操作动作</th>
            <th width="15%">操作人</th>
            <th width="15%">操作IP</th>
            <th width="15%">操作时间</th>
        </tr>
       
        <?php foreach ($list AS $key => $value): ?>
            <tr>
                <td><?php echo $value['id']; ?></td>
                <td><?php echo $value['model']; ?></td>
                <td><?php echo $value['action_id']; ?></td>
                <td><?php echo $value['action']; ?></td>
                <td><?php echo $value['username']; ?></td>
                <td><?php echo $value['ip']; ?></td>
                <td><?php echo date('Y-m-d H:i:s',$value['do_time']); ?></td>
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



        
