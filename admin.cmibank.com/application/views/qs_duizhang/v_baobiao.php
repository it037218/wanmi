<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN;?>/qs_duizhang/baobiao">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
     <?php if(isset($corid)){?>
     	<input type="hidden" name="corid" value="<?php echo $corid; ?>" />
     <?php }?>
      <?php if(isset($type)){?>
     	<input type="hidden" name="type" value="<?php echo $type; ?>" />
     <?php }?>
     <?php if(isset($con_number)){?>
     	<input type="hidden" name="con_number" value="<?php echo $con_number; ?>" />
     <?php }?>
     <?php if(isset($corname)){?>
     	<input type="hidden" name="corname" value="<?php echo $corname; ?>" />
     <?php }?>
     <?php if(isset($stime)){?>
     	<input type="hidden" name="stime" value="<?php echo $stime; ?>" />
     <?php }?>
     <?php if(isset($etime)){?>
     	<input type="hidden" name="etime" value="<?php echo $etime; ?>" />
     <?php }?>
     <?php if(isset($dkstime)){?>
     	<input type="hidden" name="dkstime" value="<?php echo $dkstime; ?>" />
     <?php }?>
     <?php if(isset($dketime)){?>
     	<input type="hidden" name="dketime" value="<?php echo $dketime; ?>" />
     <?php }?>
     <?php if(isset($sjtime)){?>
     	<input type="hidden" name="sjtime" value="<?php echo $sjtime; ?>" />
     <?php }?>
     <?php if(isset($ejtime)){?>
     	<input type="hidden" name="ejtime" value="<?php echo $ejtime; ?>" />
     <?php }?>
     <input type="hidden" value="search" name="op">
</form>
<script type="text/javascript">
$("#export").on("click", function(){
	$('#exportForm').attr('action',"<?php echo OP_DOMAIN?>/qs_duizhang/export");
	$('#exportForm').attr('target',"_blank");
	$('#exportForm').attr('onsubmit',"");
	$("#exportForm").submit();
	});
</script>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/qs_duizhang/baobiao" method="post" id='exportForm' target="_blank">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>公司名称:<input type="text"  value="<?php echo isset($corname)?$corname:''?>"  id="id_corname" name="corname"></td>
				<td>合作方起息时间:<input name="stime" readonly="true"  class="date" value="<?php echo isset($stime)?$stime:''?>"/>&nbsp;&nbsp;至
            	    <input name="etime" readonly="true" class="date"  value="<?php echo isset($etime)?$etime:''?>"/>
            	</td>
            	<td colspan="2">打款时间:<input name="dkstime" readonly="true"  class="date" value="<?php echo isset($dkstime)?$dkstime:''?>"/>&nbsp;&nbsp;至
            	    <input name="dketime" readonly="true" class="date"  value="<?php echo isset($dketime)?$dketime:''?>"/>
            	</td>
			</tr>
			<tr>
				<td>合同编号:<input type="text"  value="<?php echo isset($con_number)?$con_number:''?>"  id="id_con_number" name="con_number"></td>
				<td>合作方截止时间:<input name="sjtime" readonly="true"  class="date" value="<?php echo isset($sjtime)?$sjtime:''?>"/>&nbsp;&nbsp;至
            	    <input name="ejtime" readonly="true" class="date"  value="<?php echo isset($ejtime)?$ejtime:''?>"/>
            	</td>
				<td>类型:
					<select name="type" id="id_type">
						<option value="0" <?php if($type == 0){ echo 'selected';}?>>全部</option>
						<option value="2" <?php if($type == 2){ echo 'selected';}?>>未回款</option>
						<option value="3" <?php if($type == 3){ echo 'selected';}?>>已回款</option>
                	</select>
                	公司:
					<select name="corid">
						<option value="0" <?php if($corid == 0){ echo 'selected';}?>>全部</option>
						<option value="2" <?php if($corid == 2){ echo 'selected';}?>>尔业</option>
						<option value="3" <?php if($corid == 3){ echo 'selected';}?>>倾信</option>
						<option value="4" <?php if($corid == 4){ echo 'selected';}?>>中啸</option>
						<option value="5" <?php if($corid == 5){ echo 'selected';}?>>车宏</option>
						<option value="6" <?php if($corid == 6){ echo 'selected';}?>>多币宝</option>
						<option value="7" <?php if($corid == 7){ echo 'selected';}?>>霖欣</option>
                	</select>
				</td>
            	<td><input type="hidden" value="search" name="op"><button type="submit">检索</button></td>
            	<td><input type="submit" id='export' value="导出"></td>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
	<div class="panelBar" style="height:20px;padding-top: 7px">
		合同总金额：<?php echo $sum_money['total_con_money']?>&nbsp;&nbsp;
		采购总金额：<?php echo $sum_money['sum_stockmoney']?>&nbsp;&nbsp;
		定期总金额：<?php echo $sum_money['sum_money']-$sum_money['sum_stockmoney']?>
		回款利息总额：<?php echo round($totalRemitlixi,2)?>
		利息总额（活+定）：<?php echo round($totallixi+$totalStocklixi,2)?>
		利息总额（活期）：<?php echo round($totalStocklixi,2)?>
		<?php if($editable==1){?>&nbsp;&nbsp;服务费总计：<?php echo $totalServiceWithoutLong?><?php }?>
	</div>
	<table class="list" width="100%" layoutH="115">
        <thead>
            <tr><th width="2%">序号</th>
            	<th width="2%">id</th>
                <th width="11%">公司名称</th>
                <th width="8%">合同编号</th>
                <th width="5%">合同金额</th>
                <th width="2%">债权利率</th>
                <th width="3%">打款期限</th>
                <th width="6%">打款日</th>
                <th width="6%">到期日</th>
                <th width="2%">回款合计</th>
                <th width="3%">回款利息</th>
                <th width="3%">用户本息（定+活）</th>
                <th width="5%">定本</th>
                <th width="6%">定息</th>
                <th width="6%">活本</th>
                <th width="5%">活息</th>
                <th width="4%">应付客户利息合计</th>
                <th width="4%">服务费收入</th>
                <th width="4%">状态</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)){?>
            <?php foreach($list AS $key => $value){$benxi = diff_days($value['interesttime'], $value['repaymenttime'])*$value['con_money']*$value['con_income']/36000+$value['con_money'];
            	$dingqibenxi = $value['money']-$value['stockmoney']+round($value['totalProfit'],2);
            	
            	$remitdays = '';
            	$remitlixi=0;
            	$remitbenxi=0;
            	$stocklixi = 0;
            	$remit=true;
            	if(!empty($value['remittime'])){
            		$remitdays=diff_days(date("Y-m-d",$value['remittime']), $value['repaymenttime']);
            		$remitlixi = $remitdays*$value['con_money']*$value['con_income']/36000;
            		$remitbenxi = $remitlixi+$value['con_money'];
            		$stocklixi=empty($value['stockmoney'])?'0':round($remitdays*$value['stockmoney']*9/36500,2);
            	}else{
            		$remit=false;
            	}
            	?>
            	<?php if($value['backstatus']==3){?>
            	<tr style="color: red">
            	<?php }else{ ?>
                <tr>
            	<?php }?>
            	<td><?php echo $key+1?></td>
            	<td><?php echo $value['cid']?></td>
                <td><?php echo $value['corname']?></td>
                <td><?php echo $value['con_number']?></td>
                <td><?php echo $value['con_money']?></td>
                <td><?php echo $value['con_income']?></td>
                <td><?php echo $remitdays;?></td>
                <td><?php echo empty($value['remittime'])?'':date("Y-m-d",$value['remittime'])?></td>
                <td><?php echo $value['repaymenttime']?></td>
                <td><?php echo round($remitbenxi,2)?></td>
                <td><?php echo round($remitlixi,2);?></td>
                <td><?php echo round($value['money']+$value['totalProfit']+$stocklixi,2);?></td>
                <td><?php echo $value['money']-$value['stockmoney']?></td>
                <td><?php echo round($value['totalProfit'],2);?></td>
                <td><?php echo $value['stockmoney']?></td>
                <td><?php echo $stocklixi;?></td>
                <td><?php echo $stocklixi+round($value['totalProfit'],2);?></td>
                <td><?php echo $remit?round(bcsub($remitbenxi,$dingqibenxi,2)-$value['stockmoney']-$stocklixi,2):0;?></td>
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
