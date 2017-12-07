<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/userinfomanage/getlongproductDetails/<?php echo $uid?>">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/userinfomanage/getlongproductDetails/<?php echo $uid?>" method="post">
            <li>
              <select class="combox" name="searchtype">
				<option value="1">用户姓名</option>
				<option value="2">身份证号</option>
			</select>
            </li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchtitle)?$searchtitle:'请输入搜索内容'?>"  id="searchtitle" name="searchtitle"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <li class="line">line</li>
            
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="6%">交易流水号</th>
                <th width="6%">日期</th>
                <th width="8%">类型</th>
                <th width="6%">收入</th>
                <th width="6%">支出</th>
                <th width="6%">账户余额</th>
                <th width="6%">备注</th>
            </tr>
        </thead>
        <tbody>
         <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <tr>
                <td><?php echo $value['orderid'];?></td>
                <td><?php echo date('Y-m-d H:i:s',$value['ctime']);?></td>
                <td><?php echo $value['pname'];?></td>
                <td><?php echo $value['out'];?></td>
                <td><?php echo $value['in']?></td>
                <td><?php echo $value['balance']?></td>
                <td>----------</td>
                
            </tr>
            <?php endforeach;?> 
		<?php endif;?>
		    <tr>
		      <td>购买金额 :<?php echo $buymoney?></td>
        	  <td>昨日收益:<?php echo isset($profit[mktime(0,0,0)]['yestprofit']) ? $profit[mktime(0,0,0)]['yestprofit'] :'0';?></td>
              <td>累计收益:<?php echo $profit['countprofit'];?></td>
              <td>累计收入:<?php echo $count_in;?></td>
              <td>累计支出:<?php echo $count_out;?></td>
		    </tr>
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


