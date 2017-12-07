<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/business">
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
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="9%">日期</th>
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
         <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <tr>
            <td><?php echo $value['odate']?></td>
            <td><?php echo $value['Tota_productmoney']+$value['Tota_balance']+$value['totalnow_profit']+$value['Tota_longmoney']+$value['totalnowexp']?></td>
            <td><?php echo $value['Tota_balance']?></td>
            <td><?php echo $value['Tota_productmoney']?></td>
            <td><?php echo $value['Tota_longmoney']?></td>
            <td><?php echo $value['TotalYest_up_proift']+$value['total_yesy_ulp_profit']+$value['TotalYestExpproift'];?></td>
            <td><?php echo $value['TotalYest_up_proift']?></td>
            <td><?php echo $value['total_yesy_ulp_profit']?></td>
            <td><?php echo $value['TotalYestExpproift']?></td>
            <td><?php echo $value['total_up_profit']+$value['Total_ulp_profit']+$value['totalexpprofit']?></td>
            <td><?php echo $value['total_up_profit']?></td>
            <td><?php echo $value['Total_ulp_profit']?></td>
            <td><?php echo $value['totalexpprofit']?></td>
            <td><?php echo $value['totalnow_profit']?></td>
            <td><?php echo $value['totalnowexp']?></td>
            <td><?php echo $value['totalexpmoney']?></td>
            </tr>
            <?php endforeach;?>
		<?php endif;?>


		</tbody>
	</table>
	<div class="panelBar" style="<?php if(!empty($is_null) && $is_null == 1){echo 'display:none';}else{echo 'display:block';}?>";>
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


