<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/qs_meiri">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($searchnumber)){?>
     <input type="hidden" name="searchnumber" value="<?php echo $searchnumber; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>    
</form>
<div class="pageContent">
	<table class="list" width="150%" layoutH="55">
        <thead>
            <tr>
                <th width="3%" color='red'>日期</br><a href="<?php echo OP_DOMAIN;?>/qs_meiri/export" target="_blank">导出</a></th>
                <th width="3%">累计充值</br><a href="<?php echo OP_DOMAIN;?>/qs_meiri/exportAll" target="_blank">导出全部</a></th>
                <th width="3%">累计取现</th>
                <th width="3%">累计定期利息</th>
                <th width="3%">累计活期利息</th>
                <th width="3%">累计定期还款利息</th>
		<th width="3%">累计活期还款利息</th>
                <th width="3%">累计手续费收入</th>
                <th width="3%">累计活动支出</th>
                <th width="3%">累计红包支出</th>
                <th width="3%">累计抵用券支出</th>
                <th width="3%">累计被邀请奖励支出</th>
                <th width="3%">累计好友邀请支出</th>
                <th width="3%">累计好友佣金支出</th>
                <th width="3%">累计买送活动支出</th>
                <th width="3%">累计补偿</th>
                <th width="3%">累计体验金利息</th>
                <th width="3%">累计邀请红包</th>
                <th width="3%">累计积分奖励</th>
                <th width="3%">用户余额</th>
                <th width="3%">充值</th>
                <th width="3%">金运通充值</th>
                <th width="3%">宝付充值</th>
                <th width="3%">富友充值</th>
                <th width="3%">取现</th>
                <th width="3%">当日平台余额</th>
                <th width="3%">定期购买金额(日志)</th>
                <th width="3%">定期购买金额(用户)</th>
                <th width="3%">定期购买金额(产品)</th>
                <th width="3%">定期利息</th>
                <th width="3%">定期还款</th>
                <th width="3%">定期还款利息</th>
                <th width="3%">活期还款</th>
                <th width="3%">活期还款利息</th>
                <th width="3%">用户定期总额</th>
                <th width="3%">活期总额(计算时)</th>
                <th width="3%">活期算息总额(利息结算累计)</th>
                <th width="3%">活期利息</th>
                <th width="3%">活期产品售出金额</th>
                <th width="3%">活期购买</th>
                <th width="3%">活期转出</th>
                <th width="5%">当日活动总支出</th>
                <th width="5%">被邀请奖励</th>
                <th width="5%">邀请好友奖励</th>
                <th width="5%">好友佣金奖励</th>
                <th width="5%">活动奖励</th>
                <th width="5%">手续费</th>
                <th width="5%">红包</th>
                <th width="5%">抵用券</th>
                <th width="5%">补偿</th>
                <th width="5%">体验金利息</th>
                <th width="5%">当日订单尚未出款</th>
                <th width="5%">以前订单当日出款</th>
                <th width="5%">当日失败订单未处理</th>
                <th width="5%">以前失败订单当日处理</th>
                <th width="5%">邀请红包</th>
                <th width="5%">积分奖励</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)):$total = $sum_list['sum_invite_reward']+$sum_list['sum_invite_user_reward']+$sum_list['sum_activity_reward']+$sum_list['sum_i_first_buy']+$sum_list['sum_hongbao']+$sum_list['sum_coupon']+$sum_list['sum_buchang']+$sum_list['sum_exp_profit']+$sum_list['sum_luckybag']+$sum_list['sum_jifeng'];?>
            <?php foreach($list as $value){?>
                <tr>
                    <td><?php echo $value['odate'];?></td>
                    <td><?php echo $sum_list['sum_pay']; $sum_list['sum_pay'] -= $value['pay'];?></td>
                    <td><?php echo $sum_list['sum_withdraw']; $sum_list['sum_withdraw'] -= $value['withdraw']; ?></td>
                    <td><?php echo $sum_list['sum_p_profit']; $sum_list['sum_p_profit'] -= $value['p_profit']; ?></td>
                    <td><?php echo $sum_list['sum_l_profit']; $sum_list['sum_l_profit'] -= $value['l_profit']; ?></td>
                    <td><?php echo $sum_list['sum_repayment_profit']; $sum_list['sum_repayment_profit'] -= $value['repayment_profit']; ?></td>
		    <td><?php echo $sum_list['sum_long_repayment_profit']; $sum_list['sum_long_repayment_profit'] -= $value['long_repayment_profit']; ?></td>
					<td><?php echo $sum_list['sum_sxf']; $sum_list['sum_sxf']= bcsub($sum_list['sum_sxf'],$value['sxf'],2); ?></td>
                    <td><?php echo $total; 
                    	$total = $total- (float)$value['i_first_buy']-(float)$value['invite_reward']-(float)$value['invite_user_reward']-(float)$value['activity_reward']-(float)$value['hongbao']-(float)$value['coupon']-(float)$value['buchang']-(float)$value['exp_profit']-(float)$value['luckybag']-(float)$value['jifeng']; ?></td>

					<td><?php echo $sum_list['sum_hongbao']; $sum_list['sum_hongbao']= bcsub($sum_list['sum_hongbao'],$value['hongbao'],2); ?></td>
					
					 <td><?php echo $sum_list['sum_coupon']; $sum_list['sum_coupon'] -= (float)$value['coupon']; ?></td>
					<td><?php echo $sum_list['sum_i_first_buy']; $sum_list['sum_i_first_buy'] -= (float)$value['i_first_buy']; ?></td>
                    <td><?php echo $sum_list['sum_invite_reward']; $sum_list['sum_invite_reward'] -= (float)$value['invite_reward']; ?></td>
                    <td><?php echo $sum_list['sum_invite_user_reward'];  $sum_list['sum_invite_user_reward'] -= (float)$value['invite_user_reward']; ?></td>
                    <td><?php echo $sum_list['sum_activity_reward']; $sum_list['sum_activity_reward']  -= (float)$value['activity_reward']; ?></td>
                    <td><?php echo $sum_list['sum_buchang']; $sum_list['sum_buchang']  -= (float)$value['buchang']; ?></td>
                    <td><?php echo $sum_list['sum_exp_profit']; $sum_list['sum_exp_profit']  -= (float)$value['exp_profit']; ?></td>
                    <td><?php echo $sum_list['sum_luckybag']; $sum_list['sum_luckybag']  -= (float)$value['luckybag']; ?></td>
                    <td><?php echo $sum_list['sum_jifeng']; $sum_list['sum_jifeng']  -= (float)$value['jifeng']; ?></td>
                    <td><?php echo $value['balance'];?></td>
                    <td><?php echo $value['pay'];?></td>
                    <td><?php echo $value['pay_jyt'];?></td>
                    <td><?php echo $value['pay_baofoo'];?></td>
                    <td><?php echo $value['pay_fuiou'];?></td>
                    <td><?php echo $value['withdraw'];?></td>
                    <td><?php echo $value['pay'] - $value['withdraw'];?></td>
                    <td><?php echo $value['p_buy_log'];?></td>
                    <td><?php echo $value['p_userbuy'];?></td>
                    <td><?php echo $value['p_product_sellmoney']; ?></td>
                    <td><?php echo $value['p_profit']; ?></td>
                    <td><?php echo $value['repayment'];?></td>
                    <td><?php echo $value['repayment_profit'];?></td>
		    <td title="活期还款"><?php echo $value['long_repayment'];?></td>
                    <td title="活期还款利息"><?php echo $value['long_repayment_profit'];?></td>
                    
                    <td><?php echo $value['p_all_userbuy'];?></td>
                    <td><?php echo $value['longmoney'];?></td>
                    <td><?php echo $value['real_longmoney'];?></td>
                    <td><?php echo $value['l_profit'];?></td>
                    <td><?php echo $value['lp_sellout'];?></td>
                    <td><?php echo $value['lp_buy'];?></td>
                    <td><?php echo $value['ltob'];?></td>
                    <td><?php echo $value['invite_reward'] + $value['invite_user_reward'] + $value['activity_reward'] + $value['i_first_buy']+$value['hongbao']+$value['coupon']+$value['buchang']+$value['exp_profit']+$value['luckybag']+$value['jifeng'];?></td>
                    <td><?php echo $value['i_first_buy'];?></td>
                    <td><?php echo $value['invite_reward'];?></td>
                    <td><?php echo $value['invite_user_reward'];?></td>
                    <td><?php echo $value['activity_reward'];?></td>
                    <td><?php echo $value['sxf'];?></td>
                    <td><?php echo $value['hongbao'];?></td>
                    <td><?php echo $value['coupon'];?></td>
                    <td><?php echo $value['buchang'];?></td>
                    <td><?php echo $value['exp_profit'];?></td>
                    <td><?php echo $value['notwithhold'];?></td>
                    <td><?php echo $value['withhold'];?></td>
                    <td><?php echo $value['fall_notwithdraw'];?></td>
                    <td><?php echo $value['fall_withdraw'];?></td>
                    <td><?php echo $value['luckybag'];?></td>
                    <td><?php echo $value['jifeng'];?></td>
                </tr>
            <?php }?>
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
    return abs(($b_new-$a_new)/86400+1);
}
?>
