<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/backmoney/alreadyback">
	<input type="hidden" name="pageNum" value="<?php echo isset($pageNum) ? $pageNum : 0; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo isset($numPerPage) ? $numPerPage : 0; ?>" />
    <?php if(isset($stime)){?>
      <input type="hidden" name="stime" value="<?php echo $stime; ?>" />
     <?php }?>
     <?php if(isset($etime)){?>
      <input type="hidden" name="etime" value="<?php echo $etime; ?>"/>
     <?php }?>
     <?php if(isset($searchcorname)){?>
      <input type="hidden" name="searchcorname" value="<?php echo $searchcorname; ?>"/>
     <?php }?>
     <input type="hidden" value="search" name="op">
</form>

<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
           <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN; ?>/backmoney/alreadyback" method="post">
            <li><span>公司名称</span></li>
            <li><input type="text"  value="<?php echo isset($searchcorname)?$searchcorname:''?>"  id="searchcorname" name="searchcorname"></li>
            <li><span>合同编号</span></li>
            <li><input type="text"  value="<?php echo isset($searchcon_number)?$searchcon_number:''?>"  id="searchcon_number" name="searchcon_number"></li>
            <li><span>日期</span></li>
            <li><input name="stime" readonly="true"  class="date"  value="<?php echo isset($stime)?$stime:''?>"/>&nbsp;&nbsp;至</li>
            <li><input name="etime" readonly="true" class="date"" value="<?php echo isset($etime)?$etime:''?>"/></li>
            <li><input type="hidden" value="search" name="op"><button type="submit">检索</button></li>
            </form>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
		<thead>
			<tr>
				<th width="8%">公司名称</th>
				<th width="8%">合同编号</th>
				<th width="3%">保证金比例</th>
				<th width="6%">合同总金额</th>
				<th width="6%">合同起息日</th>
				<th width="7%">合作方到期时间</th>
				<th width="3%">合作方期限</th>
				<th width="6%">打款时间</th>
				<th width="3%">打款期限</th>
				<th width="6%">打款计算本息</th>
				<th width="6%">定期利息</th>
				<th width="6%">活期利息</th>
				<th width="5%">打款计算服务费</th>
				<th width="9%">审核状态</th>
				<!-- <th width="5%">服务费</th> -->
				<th width="7%">操作</th>
			</tr>
		</thead>
		<tbody>
	    <?php $oldname = ''; $total_con_money=0;$total_remitmoney=0;$total_backmoney=0;$total_profit=0;$total_remitbenxi = 0; $total_service=0;$allProductTotalProfit=0;$totalLongmoneyProfit=0;?>
	    
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <?php 
            	$tiqian = '';
            	if($value['con_status']==3){
            		$tiqian='-提前回款';
            	}
                switch ($value['status']){
                    case 0: $status = '未审核';break;
                    case 1: $status = '审核中';break;
                    case 2: $status = '审核退回';break;
                    case 3: $status = '审核通过'.$tiqian;break;
                    default: $status = '----'; break;
                }
                $total_con_money = $total_con_money+$value['con_money'];
                $total_remitmoney = $total_remitmoney+$value['remitmoney'];
                $total_backmoney = $total_backmoney+$value['backmoney'];
                $total_profit = $total_profit+($value['backmoney']-$value['remitmoney']);           
                $dingqibenxi = $value['money']-$value['stockmoney']+round($value['totalProfit'],2);
                $allProductTotalProfit = $allProductTotalProfit+$value['totalProfit'];
                $remitbenxi=0;
                $longmoneyProfit = 0;
                if(!empty($value['remittime'])){
                    $remitdays=diff_days(date("Y-m-d",$value['remittime']), $value['repaymenttime']);
                    $remitbenxi = $remitdays*$value['con_money']*$value['con_income']/36000+$value['con_money'];
                    if(strtotime($value['repaymenttime'])>strtotime('2017-07-12')){
	                    $longmoneyProfit = $remitdays*$value['stockmoney']*$longProductIncome/36500;
                    }
                }//借款天数*借款金额/3600+借款金额     ；借款天数=还款日期-起息日期
                $total_remitbenxi = $total_remitbenxi+$remitbenxi;
                $totalLongmoneyProfit = $totalLongmoneyProfit+$longmoneyProfit;
                $dktime=strtotime(empty($value['remittime'])?'':date("Y-m-d",$value['remittime']));
                $jxtime=strtotime($value['cietime']);
                $fxtime=strtotime($value['interesttime']);
                $qdzqtime=round(($jxtime-$fxtime)/3600/24);//签订合同周期=还款时间-付息时间
                $hetongqixian=round(($jxtime-$dktime)/3600/24);//签订合同周期=还款时间-打款时间
             
              
             ?> 
			<tr target="sid_user" rel="1">
				<td><?php echo $value['corname'];?></td>
				<td><?php echo $value['con_number'];?></td>
				<td><?php echo $value['con_bzjbl'];?>%</td>
				<td><?php echo $value['con_money'];?></td>
				<td><?php echo $value['interesttime'];?></td>
				<td><?php echo $value['cietime'];?></td><!-- //合作方到期时间 -->
				<td><?php echo $qdzqtime+1;?></td> <!-- 合同周期 -->
                <td><?php echo empty($value['remittime'])?'':date("Y-m-d",$value['remittime'])?></td><!--//打款时间 -->
				<td><?php echo $hetongqixian+1;?></td> <!-- 合同实际周期  -->
			    <td><?php echo round($remitbenxi,2);?></td>
			   	<td><?php echo round($value['totalProfit'],2);?></td>
			    <td><?php echo round($longmoneyProfit,2);?></td>
			    <td><?php $remitservice = round(bcsub($remitbenxi,$dingqibenxi,2)-$value['stockmoney']-$longmoneyProfit,2); $total_service = $total_service+$remitservice; echo $remitservice;?></td>
			    <td><?php echo $status;?></td>
				<td>
				    <a title="回款详情" target="dialog" href="<?php echo OP_DOMAIN;?>backmoney/detail/<?php echo $value['bid']?>">查看</a>
				    <?php if($editable==1){?>
				    &nbsp;&nbsp;|&nbsp;&nbsp;
				    <a title="修改回款详情" target="dialog" href="<?php echo OP_DOMAIN;?>backmoney/updateback/<?php echo $value['bid']?>">修改</a>
					<?php if($value['status'] == !3){?>
					&nbsp;&nbsp;|&nbsp;&nbsp;
					<a title="设为已回款" target="dialog" href="<?php echo OP_DOMAIN;?>backmoney/setback/<?php echo $value['bid']?>">设为已回款</a>
					<?php }?>
					<?php }?>
				</td>
			</tr>
            <?php endforeach;?>
		<?php endif;?>
		      <tr>
		          <td>合计：</td>
		          <td></td>
		          <td></td>
		          <td><?php echo $total_con_money;?></td>
		          <td></td>
		          <td></td>
		          <td></td>
		          <td></td>
		          <td></td>
		          <td><?php echo round($total_remitbenxi,2);?></td>
		          <td><?php echo round($allProductTotalProfit,2);?></td>
		          <td><?php echo round($totalLongmoneyProfit,2);?></td>
		          <td><?php echo round($total_service,2);?></td>
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
<?php 
function diff_days($start, $end){
    list($a_year, $a_month, $a_day) = explode('-', $start);
    list($b_year, $b_month, $b_day) = explode('-', $end);
    $a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
    $b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
    return abs(($b_new-$a_new)/86400+1);
}
?>