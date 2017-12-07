<div class="pageContent">
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="10%">订单ID</th>
                <th width="10%">平台订单ID</th>
                <th width="10%">产品ID</th>
                <th width="10%">产品名称</th>
                <th width="5%">订单金额</th>
                <th width="30%">明细</th>
                <th width="10%">创建时间</th>
                <th width="25%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                <td><?php echo $value['orderid'];?></td>
                <td><?php echo $value['trxid'] ;?></td>
                <td><?php echo $value['pid'] ;?></td>
                <td><?php echo $productList[$value['pid']]['pname'] ;?></td>
                <td><?php echo $value['b_money'] ;?></td>
                <td><?php echo $value['divdetails'] ?></td>
                <td><?php echo date('Y-m-d H:i:s', $value['ctime']) ;?></td>
                <td>
				<?php if($value['status'] == 0){?>
                    <a href="<?php echo OP_DOMAIN;?>repayment/repaymen_do/<?php echo $value['orderid'];?>" target="ajaxtodo" title="查看">确认还款</a>
				<?php }else{?>
				    <a href="<?php echo OP_DOMAIN;?>repayment/repaymen_reback/<?php echo $value['orderid'];?>" target="navtab" title="查看">退款</a>
				<?php }?>
                </td>
                </tr>
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</div>
<?php 
function diff_days($start, $end){
    list($a_year, $a_month, $a_day) = explode('-', $start);
    list($b_year, $b_month, $b_day) = explode('-', $end);
    $a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
    $b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
    return abs(($a_new-$b_new)/86400);
}
?>

