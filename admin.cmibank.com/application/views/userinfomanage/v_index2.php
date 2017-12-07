<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/userinfomanage">
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
                <th width="9%">身份证号</th>
                <th width="6%">总资产</th>
                <th width="6%">账户余额</th>
                <th width="6%">定期投资总额</th>
                <th width="5%">活期总资产</th>
                <th width="5%">昨日收益</th>
                <th width="5%">昨日定期收益</th>
                <th width="5%">昨日活期收益</th>
                <th width="6%">昨日体验金收益</th>
                <th width="5%">累计收益</th>
                <th width="5%">累计定期收益</th>
                <th width="5%">累计活期收益</th>
                <th width="5%">累计体验金收益</th>
                <th width="5%">当前定期收益</th>
                <th width="5%">当前体验金收益</th>
                <th width="5%">可投体验金</th>
            </tr>
        </thead>
        <tbody>
        <!-- --- $banklist['01050000']['name']);-->
         <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <tr>
            
            </tr>
            <?php endforeach;?>
		<?php endif;?>
		    <td>所有统计：</td>
            <td></td>
            <td></td>  
		    <!-- 总资产--><td><?php echo $Tota_productmoney+$Tota_balance+$totalnow_profit+$Tota_longmoney+$totalnowexp;?></td>
		    
            <!-- 账户余额 --><td><?php echo $Tota_balance;?></td></td>
            
            <!-- 定期投资总额--><td><?php echo $Tota_productmoney;?></td>
            
            <!-- 活期总资产 --><td><?php echo $Tota_longmoney;?></td>
            
            <!-- 昨日收益 --><td><?php echo $TotalYest_up_proift+$total_yesy_ulp_profit+$TotalYestExpproift;?></td>
            <!-- 昨日定期收益--><td><?php echo $TotalYest_up_proift;?></td>
            <!-- 昨日活期收益--><td><?php echo $total_yesy_ulp_profit;?></td>
            <!-- 昨日体验金收益--><td><?php echo $TotalYestExpproift;?></td>
            
            <!-- 累计收益 --><td><?php echo $total_up_profit+$Total_ulp_profit+$totalexpprofit;?></td>
            <!-- 累计定期收益 --><td><?php echo $total_up_profit;?></td>
            <!-- 累计活期收益 --><td><?php echo $Total_ulp_profit;?></td>
            <!-- 累计体验金收益 --><td><?php echo $totalexpprofit;?></td>
            
            <!-- 当前定期收益 --><td><?php echo $totalnow_profit;?></td>
            <!-- 当前体验金收益 --><td><?php echo $totalnowexp;?></td>
            
            <!-- 当前体可投体验金 --><td><?php ?></td>
		</tbody>
	</table>
</div>


