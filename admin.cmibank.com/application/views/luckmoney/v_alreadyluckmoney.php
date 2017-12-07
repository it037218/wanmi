<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN?>/luckmoney_list/getAlreadyLuckMoney/<?php echo $lmid ?>">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($aboutustitle)){?>
     <input type="hidden" name="searchphone" value="<?php echo $searchphone; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
</form>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/luckmoney_list/getAlreadyLuckMoney/<?php echo $lmid ?>">
            <li><span>手机号码</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchphone)?$searchphone:'请输入搜索内容'?>"  id="searchphone" name="searchphone"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
        </ul>
    </div>
    <table class="list" width="100%" layoutH="115">
        <thead>
        <tr>
			<th width="20%">红包名称</th>
            <th width="20%">抢单号</th>
			<th width="20%">用户手机号</th>
			<th width="20%">抢购金额</th>
			<th width="20%">抢红包时间</th>
		</tr>
		</thead>
		<?php if(!empty($list)):?>
		<?php foreach($list AS $key=>$value):?>
		<tr>
            <td><?php echo $lname;?></td>
            <td><?php echo $value['orderid']?></td>
            <td><?php echo $value['account'];?></td>
            <td><?php echo $value['money']?></td>
            <td><?php echo date('Y-m-d H:i:s',$value['ctime'])?></td>
		</tr>
		<?php endforeach;?>
        <?php endif;?>
  
    
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
