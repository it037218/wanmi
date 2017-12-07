<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN;?>/repayment/productUserList/<?php echo $pid ?>/<?php echo $createtime; ?>">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">

<table>
	<tr style="height: 40px">
		<td style="width: 200px">
		   总计金额:<?php echo $count_money . '<br />'; ?>
	            总计利息:<?php echo $count_profit . '<br />'; ?>
		</td><td>
		产品售出金额:<?php echo $productDetail['sellmoney'] . '<br />'; ?>
	            售出金额计算利息:<?php echo $productDetail['sellmoney']*$productDetail['income']*diff_days($productDetail['uistime'],$productDetail['uietime'])/36500 . '<br />'; ?>
		</td>
	</tr>
</table>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="7%">产品名称</th>
                <th width="5%">用户姓名</th>
                <th width="8%">手机号码</th>
                <th width="10%">身份证号码</th>
                <th width="3%">本金</th>
                <th width="3%">利息</th>
                <th width="3%">本息</th>
                <th width="5%">到期时间</th>
                <th width="5%">状态</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                <td><?php echo $productDetail['pname'];?></td>
                <td><?php echo $user_identity[$value['uid']]['realname']; ?></td>
                <td><?php echo $user_identity[$value['uid']]['phone']; ?></td>
                <td><?php echo $user_identity[$value['uid']]['idCard'];?></td>
                <td><?php echo $value['money'];?></td>
                <td><?php echo $value['profit'];?></td>
                <td><?php echo $value['money'] + $value['profit'] ;?></td>
                <td><?php echo date('Y-m-d', $value['ctime']);?></td>
                <td>
				<?php if(isset($value['status']) && $value['status'] == 0){?>
				                    审核中
                <?php }else if(isset($value['status']) && $value['status'] == 1){?>
				                    还款中
				<?php }else{?>
				                    已还款
				<?php }?>
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
<?php 
function diff_days($start, $end){
    list($a_year, $a_month, $a_day) = explode('-', $start);
    list($b_year, $b_month, $b_day) = explode('-', $end);
    $a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
    $b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
    return abs(($a_new-$b_new)/86400)+1;
}
?>

