<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/userinfomanage/">
    <?php if(isset($searchtitle)){?>
     <input type="hidden" name="searchtitle" value="<?php echo $searchtitle;?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/userinfomanage/" method="post">
            <li>
              <select name="searchtype">
				<option value="1">用户姓名</option>
				<option value="2">手机号码</option>
				<option value="3">身份证号</option>
			</select>
            </li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchtitle)?$searchtitle:'请输入搜索内容'?>"  id="searchtitle" name="searchtitle"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="4%">用户id</th>
                <th width="6%">用户名称</th>
                <th width="9%">身份证号</th>
                <th width="6%">总资产</th>
                <th width="6%">账户余额</th>
                <th width="6%">定期投资总额</th>
                <th width="5%">活期总资产</th>
                <th width="4%">昨日收益</th>
                <th width="4%">昨日定期收益</th>
                <th width="4%">昨日活期收益</th>
                <th width="4%">昨日体验金收益</th>
                <th width="5%">累计收益</th>
                <th width="5%">累计定期收益</th>
                <th width="5%">累计活期收益</th>
                <th width="4%">累计体验金收益</th>
                <th width="5%">当前定期收益</th>
                <th width="4%">当前体验金收益</th>
                <th width="5%">在投体验金</th>
				<th width="5%">现金抵用券</th>
                <th width="5%">活动收益</th>
            </tr>
        </thead>
        <tbody>
        <!-- --- $banklist['01050000']['name']);-->
         <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
           <?php
            $count_expmoney = isset($expmoney_log_list[$value['uid']]) ? $expmoney_log_list[$value['uid']] :'0';
            $balance = isset($balance_list[$value['uid']]) ? $balance_list[$value['uid']] :'0';
            $count_product_money = isset($userproduct_list[$value['uid']]['count_money']) ? $userproduct_list[$value['uid']]['count_money'] : '0' ;
            $longmoney = isset($longmoney_list[$value['uid']]) ? $longmoney_list[$value['uid']] :'0';
            $yesy_ulp_profit = isset($longulpprofitlog_list[$value['uid']][mktime(0,0,0)]['profit']) ? $longulpprofitlog_list[$value['uid']][mktime(0,0,0)]['profit'] : '0' ;
            $count_ulp_profit = isset($longulpprofitlog_list[$value['uid']]['count_profit']) ? $longulpprofitlog_list[$value['uid']]['count_profit'] : '0' ;
	        $count_profit =$up_profit_log[$value['uid']]['count_profit']+$count_ulp_profit+$countexprofit;
	        $yest_profit=$up_profit_log[$value['uid']]['yest_profit']+$yesy_ulp_profit+$yestexprofit;
	        $now_up_profit = isset($up_profit_log[$value['uid']]['now_profit']) ? $up_profit_log[$value['uid']]['now_profit'] : '0';
	        $account_money=$count_product_money+$balance+$longmoney+$now_up_profit+$nowProfit;
            ?>
            <tr>
                <td><?php echo $value['uid'];?></td>
                <td><?php echo $value['realname'] ;?></td>
                <td><?php echo $value['idCard'];?></td>
                <!-- 总资产--><td><?php echo $account_money;?></td>
                <!-- 账户余额 --><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getUserlogDetails/<?php echo $value['uid'];?>" target="navtab" title="用户收支明细"><?php echo round($balance,2);?></a></td>
                <!-- 定期投资总额--><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getProductDetails/<?php echo $value['uid'];?>/0" target="navtab" title="定期用户购买记录"><?php echo $count_product_money;?></a></td>
                <!-- 活期总资产 --><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getlongproductDetails/<?php echo $value['uid']?>" target="navtab" title="用户活期收支明细"><?php echo round($longmoney,2);?></a></td>
                <!-- 昨日收益 --><td><?php echo round($yest_profit,2);?></td>
                <!-- 昨日定期收益--><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getproductprofitDetails/<?php echo $value['uid'];?>" target="navtab" title="定期用户收益明细"><?php echo $up_profit_log[$value['uid']]['yest_profit'];?></a></td>
                <!-- 昨日活期收益--><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getlongproductDetails/<?php echo $value['uid']?>" target="navtab" title="用户活期收支明细"><?php echo round($yesy_ulp_profit,2); ?></a></td>
                <!-- 昨日体验金收益--><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getExpProfitDetails/<?php echo $value['uid'];?>" target="navtab" title="用户体验金收益明细"><?php echo isset($yestexprofit) ? $yestexprofit : 0 ;?></a></td>
                <!-- 累计收益 --><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getproductprofitDetails/<?php echo $value['uid'];?>" target="navtab" title="定期用户收益明细"><?php echo round($count_profit,2);?></a></td>
                <!-- 累计定期收益 --><td><?php echo $up_profit_log[$value['uid']]['count_profit'];?></td>
                <!-- 累计活期收益 --><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getlongproductDetails/<?php echo $value['uid']?>" target="navtab" title="用户活期收支明细"><?php echo $count_ulp_profit;?></a></td>
                <!-- 累计体验金收益 --><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getExpProfitDetails/<?php echo $value['uid'];?>" target="navtab" title="用户体验金收益明细"><?php echo isset($countexprofit) ? $countexprofit : 0 ;?></a></td>
                <!-- 当前定期收益 --><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getproductprofitDetails/<?php echo $value['uid'];?>" target="navtab" title="定期用户收益明细"><?php echo $now_up_profit;?></a></td>
                <!-- 当前体验金收益 --><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getExpProfitDetails/<?php echo $value['uid'];?>" target="navtab" title="用户体验金收益明细"><?php echo $nowProfit?></a></td>
                <!-- 当前体可投体验金 --><td><a href="<?php echo OP_DOMAIN?>/expmoneystatistics/getuserexpmoneyDetails/<?php echo $value['uid']?>" target="navtab" title="用户体验金列表"><?php echo $expmoney?></a></td>
                <!-- 现金抵用 --><td><a href="<?php echo OP_DOMAIN?>/couponstatistics/getusercouponDetails/<?php echo $value['uid'];?>" target="navtab" title="现金抵用券"><?php echo $couponcount;?></a></td>
                <!--活动收益--><td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getlistactive/<?php echo $value['uid']; ?>" target="navtab" title="活动收益" ><?php echo $jiangli;?></a></td>
            </tr>
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</div>


