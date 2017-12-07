<form id="pagerForm" method="post"
	action="<?php echo OP_DOMAIN; ?>/duibi">
	<input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
	<input type="hidden" name="numPerPage"
		value="<?php echo $numPerPage; ?>" />
    <?php if(isset($searchnumber)){?>
     <input type="hidden" name="searchnumber"
		value="<?php echo $searchnumber; ?>" /> <input type="hidden"
		value="search" name="op">
     <?php }?>    
</form>
<div class="pageContent">
	<div class="pageContent">
		<table class="list" width="100%" layoutH="55">
			<thead>
				<tr>
					<th width="6%" color='red'>日期</th>
					<th width="5%">当天计算余额</th>
					<th width="5%">整体账户余额</th>
					<th width="5%">用户余额</th>
					<th width="5%">整体对比误差</th>
					<th width="5%">当天对比误差</th>
					<th width="6%">累计充值</th>
					<th width="5%">累计取现</th>
					<th width="5%">累计活期利息</th>
					<th width="5%">累计已还定期利息</th>
					<th width="5%">累计运营开支</th>
					<th width="6%">累计补偿金</th>
					<th width="5%">累计手续费</th>
					<th width="5%">用户定期总额</th>
					<th width="5%">用户活期总额</th>
					<th width="5%">累计收入</th>
					<th width="5%">累计支出</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($list)){?>
            <?php $total_yunying = $sum_list['sum_invite_reward'] + $sum_list['sum_invite_user_reward'] + $sum_list['sum_activity_reward'] + $sum_list['sum_i_first_buy'] + $sum_list['sum_hongbao'] + $sum_list['sum_coupon'] + $sum_list['sum_buchang']+ $sum_list['sum_exp_profit']+ $sum_list['sum_luckybag']+ $sum_list['sum_jifeng'];
            // p_buy_log定期购买金额 活期产品售出金额lp_sellout 活期转出ltob invite_reward邀请好友奖励 invite_user_reward好友佣金奖励 activity_reward活动奖励 被邀请奖励i_first_buy 抵用券coupon
            foreach ($list as $index => $value) {
                $total_in = $sum_list['sum_pay'] + $sum_list['sum_l_profit'] + $sum_list['sum_repayment_profit'] + $total_yunying;   
                
                // 累计活动总支出 $total_in
               @$js_balance = $list[$index + 1]['balance'] + $value['pay'] - $value['withdraw'] - $value['p_buy_log'] - $value['lp_sellout'] + $value['ltob'] + $value['invite_reward'] + $value['invite_user_reward'] + $value['activity_reward'] + $value['i_first_buy'] + $value['hongbao'] + $value['coupon']+$value['repayment']+$value['repayment_profit']-$value['notwithhold']+$value['withhold']-$value['fall_notwithdraw']+$value['fall_withdraw']+$value['buchang']+$value['luckybag']+$value['jifeng']+$value['exp_profit']-$value['sxf'];
               $yunying= (float)$value['i_first_buy']+(float)$value['invite_reward']+(float)$value['invite_user_reward']+(float)$value['activity_reward']+(float)$value['hongbao']+(float)$value['coupon']+(float)$value['buchang']+(float)$value['exp_profit']+(float)$value['hongbao']+(float)$value['luckybag']+(float)$value['jifeng'];
                $zhanghu_balance = bcsub($total_in,($sum_list['sum_withdraw']+$value['p_all_userbuy']+$value['longmoney']+$value['notwithhold']+$value['fall_notwithdraw']+$sum_list['sum_sxf']),2);
                ?>    
                <tr>
					<td><?php echo $value['odate'];?></td>

					<td><?php echo $js_balance;?></td>
					<td><?php echo $zhanghu_balance;?></td>
					<!-- 账户余额 -->
					<td><?php echo $value['balance'];?></td>
					<!-- 用户余额 -->
					<td><?php echo bcsub($value['balance'],$zhanghu_balance,2);?></td>
					<td><?php echo bcsub($value['balance'],$js_balance,2);?></td>
					<!-- 误差对比 -->
				
					 <td><?php echo $sum_list['sum_pay']; $sum_list['sum_pay'] -= $value['pay'];?></td>
					<!-- 累计充值 -->
					<td><?php echo $sum_list['sum_withdraw'];  ?></td>
					<!-- 累计取现 -->

					<td><?php echo $sum_list['sum_l_profit']; $sum_list['sum_l_profit'] -= $value['l_profit'];?> </td>
					<!-- 昨天的累计活期利息=总活期利息-今天的活期利息 l_profit活期利息--->
					<td><?php echo $sum_list['sum_repayment_profit'];$sum_list['sum_repayment_profit']=bcsub($sum_list['sum_repayment_profit'] ,$value['repayment_profit'],2);?></td>
					<!-- //累计已还定期利息 -->

					<td><?php echo $total_yunying; $total_yunying=$total_yunying-$yunying ;?></td>
					<!--  累计运营开支 =  取现+ 补偿总额+手续费+ 活期利息-->

				    <td><?php echo $sum_list['sum_buchang']; $sum_list['sum_buchang']-= (float)$value['buchang']; ?></td>
					<!-- 累计补偿金 -->
					 
					<td><?php echo $sum_list['sum_sxf']; $sum_list['sum_sxf']= bcsub($sum_list['sum_sxf'],$value['sxf'],2); ?></td>
					<!-- 	累计手续费 -->
					
					<!-- 累计收入 -->
					<td><?php echo $value['p_all_userbuy'];?></td>
					<td><?php echo $value['longmoney'];?></td>
					<td><?php echo $total_in;$total_in=$total_in-$value['pay']-$value['l_profit']-$value['repayment_profit']-$yunying-$value['sxf']?></td>
					<td><?php echo $sum_list['sum_withdraw']+$value['p_all_userbuy']+$value['longmoney']+$value['notwithhold']-$value['withhold'];$sum_list['sum_withdraw'] -= $value['withdraw']; ?></td>
				</tr>
            <?php }?>
		<?php }?>
		</tbody>
		</table>
		<div class="panelBar">
			<div class="pages">
				<span>显示</span> <select class="combox" name="numPerPage"
					onchange="navTabPageBreak({numPerPage:this.value})">
					<option value="30"
						<?php echo $numPerPage == 30 ? 'selected' : ''; ?>>30</option>
					<option value="50"
						<?php echo $numPerPage == 50 ? 'selected' : ''; ?>>50</option>
				</select> <span>条，共<?php echo $count; ?>条</span>
			</div>
			<div class="pagination" targetType="navTab"
				totalCount="<?php echo $count; ?>"
				numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10"
				currentPage="<?php echo $pageNum; ?>"></div>
		</div>
	</div>
<?php

function diff_days($start, $end)
{
    list ($a_year, $a_month, $a_day) = explode('-', $start);
    list ($b_year, $b_month, $b_day) = explode('-', $end);
    $a_new = mktime(0, 0, 0, $a_month, $a_day, $a_year);
    $b_new = mktime(0, 0, 0, $b_month, $b_day, $b_year);
    return abs(($b_new - $a_new) / 86400 + 1);
}
?>
