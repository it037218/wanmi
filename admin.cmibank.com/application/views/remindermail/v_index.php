<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN;?>remindermail">
	<input type="hidden" name="pageNum" value="<?php echo isset($pageNum) ? $pageNum : 0; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo isset($numPerPage) ? $numPerPage : 0; ?>" />
</form>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN;?>remindermail" method="post">
            <li><span>公司名称</</span></li>
            <li><input type="text" value="<?php echo isset($searchcorname)?$searchcorname:''?>"  id="searchcorname" name="searchcorname"></li>
            <li><span>合同编号</</span></li>
            <li><input type="text"  value="<?php echo isset($searchcon_number)?$searchcon_number:''?>"  id="searchcon_number" name="searchcon_number"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit">检索</button></li>
            </form>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
		<thead>
			<tr>
				<th width="10%">公司名称</th>
				<th width="12%">合同编号</th>
				<th width="4%">保证金比例</th>
				<th width="3%">打款期限(天)</th>
				<th width="6%">合同总金额</th>
				<th width="4%">业务方利率</th>
				<th width="6%">实际回款本金</th>
				<th width="6%">实际回款利息</th>
				<th width="6%">实际回款本息</th>
				<th width="7%">合作方到期时间</th>
				<th width="6%">打款时间</th>
				<th width="6%">打款计算本息</th>
				<th width="6%">打款计算用户本息（定+活）</th>
				<th width="6%">打款计算服务费</th>
				<th width="5%">审核状态</th>
				<th width="15%">操作</th>
			</tr>
		</thead>
		<tbody>
	    <?php $oldname = ''; ?>
        <?php if(!empty($list)){?>
            <?php foreach($list AS $key=>$value){?>
            <?php 
                switch ($value['status']){
                    case 0: $status = '未审核';break;
                    case 1: $status = '审核中';break;
                    case 2: $status = '审核退回';break;
                    case 3: $status = '审核通过';break;
                    default: $status = '----'; break;
                }
                $dingqibenxi = $value['money']-$value['stockmoney']+round($value['totalProfit'],2);
                $remitbenxi=0;
                $stocklixi = 0;
                if(!empty($value['remittime'])){
                	$remitdays=diff_days(date("Y-m-d",$value['remittime']), $value['repaymenttime']);
                	$remitbenxi = $remitdays*$value['con_money']*$value['con_income']/36000+$value['con_money'];
                	$stocklixi=empty($value['stockmoney'])?'0':round($remitdays*$value['stockmoney']*9/36500,2);
                }
            ?> 
			<tr>
				<td><?php echo $value['corname'];?></td>
				<td><?php echo $value['con_number'];?></td>
				<td><?php echo $value['con_bzjbl'].'%';?></td>
				<td><?php echo diff_days(date("Y-m-d",$value['remittime']), $value['repaymenttime']);?></td>
				<td><?php echo $value['con_money'];?></td>
				<td><?php echo $value['con_income'].'%';?></td>
				<td><?php echo $value['remitmoney'];?></td>
				<td><?php echo $value['backmoney']-$value['remitmoney'];?></td>
				<td><?php echo $value['backmoney'];?></td>
				<td><?php echo $value['cietime'];?></td>
				<td><?php echo empty($value['remittime'])?'':date("Y-m-d",$value['remittime'])?></td>
				<td><?php echo round($remitbenxi,2);?></td>
				<td><?php echo round($value['money']+$value['totalProfit']+$stocklixi,2);?></td>
				<td><?php echo round(bcsub($remitbenxi,$dingqibenxi,2)-$value['stockmoney']-$stocklixi,2);?></td>
				<td><?php echo $status;?></td>
				<td>
				    <a title="回款详情" target="dialog" href="<?php echo OP_DOMAIN;?>backmoney/detail/<?php echo $value['bid']?>">查看</a>
				    <?php if($editable==1){?>
					&nbsp;&nbsp;
					<?php if($value['ismail'] == 0){?>
					|&nbsp;&nbsp;
					<a title="发送邮件" target="navtab" href="<?php echo OP_DOMAIN;?>remindermail/infoemial/<?php echo $value['bid']?>">发送邮件</a>
					|&nbsp;&nbsp;
					<a title="设为已催款" target="ajaxTodo" href="<?php echo OP_DOMAIN;?>remindermail/issendmail/<?php echo $value['bid']?>">设为已催款</a>
                    <?php }?>
                    <?php }?>
				</td>
			</tr>
            <?php }?>
		<?php }?>  
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