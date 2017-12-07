<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN;?>/qs_duizhang">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
     <?php if(isset($type)){?>
     	<input type="hidden" name="type" value="<?php echo $type; ?>" />
     <?php }?>
     <?php if(isset($con_number)){?>
     	<input type="hidden" name="con_number" value="<?php echo $con_number; ?>" />
     <?php }?>
     <input type="hidden" value="search" name="op">
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/qs_duizhang" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>公司名称:<input type="text"  value="<?php echo isset($corname)?$corname:''?>"  id="id_corname" name="corname"></td>
				<td>合作方起息时间:<input name="stime" readonly="true"  class="date" value="<?php echo isset($stime)?$stime:''?>"/>&nbsp;&nbsp;至
            	    <input name="etime" readonly="true" class="date"  value="<?php echo isset($etime)?$etime:''?>"/>
            	</td>
				<td>类型:
					<select name="type" id="id_type">
						<option value="0" <?php if($type == 0){ echo 'selected';}?>>全部</option>
						<option value="2" <?php if($type == 2){ echo 'selected';}?>>未回款</option>
						<option value="3" <?php if($type == 3){ echo 'selected';}?>>已回款</option>
                	</select>
				</td>
			</tr>
			<tr>
				<td>合同编号:<input type="text"  value="<?php echo isset($con_number)?$con_number:''?>"  id="id_con_number" name="con_number"></td>
				<td>合作方截止时间:<input name="sjtime" readonly="true"  class="date" value="<?php echo isset($sjtime)?$sjtime:''?>"/>&nbsp;&nbsp;至
            	    <input name="ejtime" readonly="true" class="date"  value="<?php echo isset($ejtime)?$ejtime:''?>"/>
            	</td>
            	<td><input type="hidden" value="search" name="op"><button type="submit">检索</button></td>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
	<div class="panelBar" style="height:20px;padding-top: 7px">
		总计合同金额：<?php echo $sum_money['total_con_money']?>&nbsp;&nbsp;总计采购金额：<?php echo $sum_money['sum_stockmoney']?>&nbsp;&nbsp;总计定期使用金额：<?php echo $sum_money['sum_money']-$sum_money['sum_stockmoney']?>
	</div>
	<table class="list" width="100%" layoutH="115">
        <thead>
            <tr>
                <th width="11%">公司名称</th>
                <th width="8%">合同编号</th>
                <th width="5%">合同金额</th>
                <th width="4%">合作方期限</th>
                <th width="3%">打款期限</th>
                <th width="3%">合作方利率</th>
                <th width="6%">合作方本息</th>
                <th width="6%">打款计算本息</th>
                <th width="6%">活期使用资金</th>
                <th width="5%">定期使用资金</th>
                <th width="4%">定期本加息</th>
                <th width="4%">定期利息</th>
                <th width="4%">总服务费</th>
                <th width="4%">打款计算服务费</th>
                <th width="6%">打款时间</th>
                <th width="6%">合作方起息日</th>
                <th width="6%">合作方截止日</th>
                <th width="6%">采购日期</th>
                <th width="3%">状态</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)){?>
            <?php foreach($list AS $key => $value){$benxi = diff_days($value['interesttime'], $value['repaymenttime'])*$value['con_money']*$value['con_income']/36000+$value['con_money'];
            	$dingqibenxi = $value['money']-$value['stockmoney']+round($value['totalProfit'],2);
            	$remitdays = '';
            	$remitbenxi=0;
            	if(!empty($value['remittime'])){
            		$remitdays=diff_days(date("Y-m-d",$value['remittime']), $value['repaymenttime']);
            		$remitbenxi = $remitdays*$value['con_money']*$value['con_income']/36000+$value['con_money'];
            	}
            	?>
            	<?php if($value['backstatus']==3){?>
            	<tr style="color: red">
            	<?php }else{ ?>
                <tr>
            	<?php }?>
                <td><?php echo $value['corname']?></td>
                <td><?php echo $value['con_number']?></td>
                <td><?php echo $value['con_money']?></td>
                <td><?php echo diff_days($value['interesttime'], $value['repaymenttime']);?></td>
                <td><?php echo $remitdays;?></td>
                <td><?php echo $value['con_income']?></td>
                <td><?php echo round($benxi,2)?></td>
                <td><?php echo round($remitbenxi,2);?></td>
                <td><?php echo $value['stockmoney']?></td>
                <td><?php echo $value['money']-$value['stockmoney']?></td>
                <td><?php echo round($dingqibenxi,2);?></td>
                <td><?php echo round($value['totalProfit'],2);?></td>
                <td><?php echo bcsub($benxi,$dingqibenxi,2)-$value['stockmoney'];?></td>
                <td><?php echo bcsub($remitbenxi,$dingqibenxi,2)-$value['stockmoney'];?></td>
                <td><?php echo empty($value['remittime'])?'':date("Y-m-d",$value['remittime'])?></td>
                <td><?php echo $value['interesttime']?></td>
                <td><?php echo $value['repaymenttime']?></td>
                <td><?php echo empty($value['stocktime'])?'':date("Y-m-d H:i:s",$value['stocktime'])?></td>
                <td><?php echo $value['backstatus']==3?'已回款':'未回款';?></td>
                </tr>
            <?php }?>
		<?php }?>
		</tbody>
	</table>
		<div class="panelBar" style="display:<?php echo isset($none) ? $none : 'black';?>">
        <div class="pages">
            <span>显示</span>
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
                <option value="20" <?php echo $numPerPage == 20 ? 'selected' : ''; ?>>20</option>
                <option value="40" <?php echo $numPerPage == 40 ? 'selected' : ''; ?>>40</option>
            </select>
            <span>条，共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>
<?php 
function diff_days($start, $end){
    list($a_year, $a_month, $a_day) = explode('-', $start);
    list($b_year, $b_month, $b_day) = explode('-', $end);
    $a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
    $b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
    return abs(($b_new-$a_new)/86400+1);
}
?>
