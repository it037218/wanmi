<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/userinfomanage">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($searchtitle)){?>
     <input type="hidden" name="searchtitle" value="<?php echo $searchtitle; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/userinfomanage" method="post">
            <li>
              <select class="combox" name="searchtype">
				<option value="1">用户姓名</option>
				<option value="3">身份证号</option>
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
                <th width="6%">用户id</th>
                <th width="6%">用户名称</th>
                <th width="8%">身份证号</th>
                <th width="6%">总资产</th>
                <th width="6%">账户余额</th>
                <th width="6%">定期投资总额</th>
                <th width="6%">活期总资产</th>
                <th width="6%">昨日收益</th>
                <th width="6%">昨日定期收益</th>
                <th width="7%">昨日活期收益</th>
                <th width="6%">累计收益</th>
                <th width="6%">累计定期收益</th>
                <th width="7%">累计活期收益</th>
                <th width="8%">当前定期收益</th>
            </tr>
        </thead>
        <tbody>
        <!-- --- $banklist['01050000']['name']);-->
         <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
           <?php
            $balance = isset($balance_list[$value['uid']]) ? $balance_list[$value['uid']] :'0';
            $count_product_money = isset($userproduct_list[$value['uid']]['count_money']) ? $userproduct_list[$value['uid']]['count_money'] : '0' ;
            $longmoney = isset($longmoney_list[$value['uid']]) ? $longmoney_list[$value['uid']] :'0';
            $yesy_ulp_profit = isset($longulpprofitlog_list[$value['uid']][mktime(0,0,0)]['profit']) ? $longulpprofitlog_list[$value['uid']][mktime(0,0,0)]['profit'] : '0' ;
            $count_ulp_profit = isset($longulpprofitlog_list[$value['uid']]['count_profit']) ? $longulpprofitlog_list[$value['uid']]['count_profit'] : '0' ;
	        $count_profit =$up_profit_log[$value['uid']]['count_profit']+$count_ulp_profit;
	        $yest_profit=$up_profit_log[$value['uid']]['yest_profit']+$yesy_ulp_profit;
	        $now_up_profit = isset($up_profit_log[$value['uid']]['now_profit']) ? $up_profit_log[$value['uid']]['now_profit'] : '0';
	        $account_money=$count_product_money+$balance+$longmoney+$now_up_profit;
            ?>
            <tr>
                <td><?php echo $value['uid'];?></td>
                <td><?php echo $value['realname'] ;?></td>
                <td><?php echo $value['idCard'];?></td>
                <!-- 总资产--><td><?php echo $account_money;?></td>
                <!-- 账户余额 --><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getUserlogDetails/<?php echo $value['uid'];?>" target="navtab" title="用户收支明细"><?php echo round($balance,2);?></a></td>
                <!-- 定期投资总额--><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getProductDetails/<?php echo $value['uid'];?>/0" target="navtab" title="定期用户购买记录"><?php echo $count_product_money;?></a></td>
                <!-- 活期总资产 --><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getlongproductDetails/<?php echo $value['uid']?>"><?php echo round($longmoney,2);?></a></td>
                <!-- 昨日收益 --><td><?php echo round($yest_profit,2);?></td>
                <!-- 昨日定期收益--><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getproductprofitDetails/<?php echo $value['uid'];?>" target="navtab" title="定期用户收益明细"><?php echo $up_profit_log[$value['uid']]['yest_profit'];?></a></td>
                <!-- 昨日活期收益--><td><?php echo round($yesy_ulp_profit,2); ?></td>
                <!-- 累计收益 --><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getproductprofitDetails/<?php echo $value['uid'];?>" target="navtab" title="定期用户收益明细"><?php echo round($count_profit,2);?></a></td>
                <!-- 累计定期收益 --><td><?php echo $up_profit_log[$value['uid']]['count_profit'];?></td>
                <!-- 累计活期收益 --><td><?php echo $count_ulp_profit;?></td>
                <!-- 当前定期收益 --><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getproductprofitDetails/<?php echo $value['uid'];?>" target="navtab" title="定期用户收益明细"><?php echo $now_up_profit;?></a></td>
                
            </tr>
            <?php endforeach;?>
		<?php endif;?>
		   <td colspan="3">统计数据</td>
		   <td><?php echo $balance_list['count']+$total_product_money['count']+$longmoney_list['count']+$totalnow_profit['count']?></td>
		   <td><?php echo $balance_list['count']?></td>
		   <td><?php echo $total_product_money['count'];?></td>
		   <td><?php echo $longmoney_list['count']?></td>
		   <td><?php echo $TotalYest_up_proift+$total_yesy_ulp_profit;?></td>
		   <td><?php echo $TotalYest_up_proift;?></td>
		   <td><?php echo $total_yesy_ulp_profit; ?></td>
		   <td><?php echo $total_up_profit+$Total_ulp_profit?></td>
		   <td><?php echo $total_up_profit?></td>
		   <td><?php echo $Total_ulp_profit?></td>
		   <td><?php echo $totalnow_profit['count'];?></td>
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


