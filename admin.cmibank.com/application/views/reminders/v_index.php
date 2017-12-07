<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/reminders">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/reminders" method="post">
            <li><span>信息名称</</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索订单号') this.value='';" onblur="if(this.value=='') this.value='请输入搜索订单号';" value="<?php echo isset($aboutustitle)?$aboutustitle:'请输入搜索订单号'?>"  id="aboutustitle" name="aboutustitle"></li>
            <li><input type="hidden" value="search_aboutustitle" name="op"><button type="submit" >检索</button></li>
            </form>
            <li><a title="打款到银行卡"    href="<?php echo OP_DOMAIN; ?>reminders/remit" target="navtab" class="icon"><span>打款到银行卡</span></a></li>
            <li class="line">line</li>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th>Id</th>
                <th width="10%">操作用户</th>
                <th width="10%">订单Id</th>
                <th width="10%">总行名称</th>
                <th width="10%">地区名称</th>
                <th width="10%">银行卡号</th>
                <th width="10%">账户名称</th>
                <th width="10%">打款金额</th>
                <th width="10%">状态</th>
                <th width="10%">操作时间</th>
                <th width="30%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <tr>
                <td><?php echo $key+1?></td>
                <td><?php echo $value['username']?></td>
                <td><?php echo $value['order_id']?></td>
                <td><?php echo $value['bankno']?></td>
                <td><?php echo $value['city_name']?></td>
                <td><?php echo $value['account_no']?></td>
                <td><?php echo $value['accntnm']?></td>
                <td><?php echo $value['money']?></td>
                <td><?php echo $value['status']?></td>
                <td><?php echo date('Y-m-d H:i:s', $value['created_time'])?></td>
                <td>
                <a href="<?php echo OP_DOMAIN?>/reminders/del/<?php echo $value['order_id']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
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


