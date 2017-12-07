<?php
header ( "Content-type:application/vnd.ms-excel" );
header ( "Content-Disposition:filename=每日数据".date('Ymd').".xls" );
	echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<table>
        <thead>
            <tr>
                <th>日期</th>
                <th>累计充值</th>
                <th>累计取现</th>
                <th>累计定期利息</th>
                <th>累计活期利息</th>
                <th>累计定期还款利息</th>
				<th>累计活期还款利息</th>
				<th>累计手续费收入</th>
                <th>累计活动支出</th>
                <th>累计红包支出</th>
                <th>累计抵用券支出</th>
                <th>累计被邀请奖励支出</th>
                <th>累计好友邀请支出</th>
                <th>累计好友佣金支出</th>
                <th>累计买送活动支出</th>
                <th>累计补偿</th>
				<th>累计体验金利息</th>
                <th>用户余额</th>
                <th>充值</th>
                <th>取现</th>
                <th>当日平台余额</th>
                <th>定期购买金额(日志)</th>
                <th>定期购买金额(用户)</th>
                <th>定期购买金额(产品)</th>
                <th>定期利息</th>
                <th>定期还款</th>
                <th>定期还款利息</th>
				<th>活期还款</th>
                <th>活期还款利息</th>
                <th>用户定期总额</th>
                <th>活期总额(计算时)</th>
                <th>活期算息总额(利息结算累计)</th>
                <th>活期利息</th>
                <th>活期产品售出金额</th>
                <th>活期购买</th>
                <th>活期转出</th>
                <th>当日活动总支出</th>
                <th>被邀请奖励</th>
                <th>邀请好友奖励</th>
                <th>好友佣金奖励</th>
                <th>活动奖励</th>
                <th>手续费</th>
                <th>红包</th>
                <th>抵用券</th>
                <th>补偿</th>
				<th>体验金利息</th>
				<th>当日订单尚未出款</th>
                <th>以前订单出款</th>
				<th>当日失败订单未处理</th>
                <th>以前失败订单当日处理</th>
            </tr>
        </thead>
        <tbody>";
        if(!empty($list)){$total = $sum_list['sum_invite_reward']+$sum_list['sum_invite_user_reward']+$sum_list['sum_activity_reward']+$sum_list['sum_i_first_buy']+$sum_list['sum_hongbao']+$sum_list['sum_coupon']+$sum_list['sum_buchang']+$sum_list['sum_exp_profit'];
            foreach($list as $value){
                echo "<tr>";
                echo "<td>".$value['odate']."</td>";
                echo "<td>".$sum_list['sum_pay']."</td>"; $sum_list['sum_pay'] -= $value['pay'];
                echo "<td>".$sum_list['sum_withdraw']."</td>"; $sum_list['sum_withdraw'] -= $value['withdraw'];
                echo "<td>".$sum_list['sum_p_profit']."</td>"; $sum_list['sum_p_profit'] -= $value['p_profit'];
                echo "<td>".$sum_list['sum_l_profit']."</td>"; $sum_list['sum_l_profit'] -= $value['l_profit'];
                echo "<td>".$sum_list['sum_repayment_profit']."</td>"; $sum_list['sum_repayment_profit'] -= $value['repayment_profit'];
                echo "<td>".$sum_list['sum_long_repayment_profit']."</td>"; $sum_list['sum_long_repayment_profit'] -= $value['long_repayment_profit'];
				echo "<td>".$sum_list['sum_sxf']."</td>"; $sum_list['sum_sxf']= bcsub($sum_list['sum_sxf'],$value['sxf'],2);
                echo "<td>".$total."</td>"; 
                    	$total = $total- (float)$value['i_first_buy']-(float)$value['invite_reward']-(float)$value['invite_user_reward']-(float)$value['activity_reward']-(float)$value['hongbao']-(float)$value['coupon']-(float)$value['buchang']-(float)$value['exp_profit'];

				echo "<td>".$sum_list['sum_hongbao']."</td>"; $sum_list['sum_hongbao']= bcsub($sum_list['sum_hongbao'],$value['hongbao'],2);
					
				echo "<td>".$sum_list['sum_coupon']."</td>"; $sum_list['sum_coupon'] -= (float)$value['coupon'];
				echo "<td>".$sum_list['sum_i_first_buy']."</td>"; $sum_list['sum_i_first_buy'] -= (float)$value['i_first_buy'];
                echo "<td>".$sum_list['sum_invite_reward']."</td>"; $sum_list['sum_invite_reward'] -= (float)$value['invite_reward'];
                echo "<td>".$sum_list['sum_invite_user_reward']."</td>";  $sum_list['sum_invite_user_reward'] -= (float)$value['invite_user_reward'];
                echo "<td>".$sum_list['sum_activity_reward']."</td>"; $sum_list['sum_activity_reward']  -= (float)$value['activity_reward'];
                echo "<td>".$sum_list['sum_buchang']."</td>"; $sum_list['sum_buchang']  -= (float)$value['buchang'];
                echo "<td>".$sum_list['sum_exp_profit']."</td>"; $sum_list['sum_exp_profit']  -= (float)$value['exp_profit'];
                echo "<td>".$value['balance']."</td>";
                echo "<td>".$value['pay']."</td>";
                echo "<td>".$value['withdraw']."</td>";
                echo "<td>".($value['pay'] - $value['withdraw'])."</td>";
                echo "<td>".$value['p_buy_log']."</td>";
                echo "<td>".$value['p_userbuy']."</td>";
                echo "<td>".$value['p_product_sellmoney']."</td>";
                echo "<td>".$value['p_profit']."</td>";
                echo "<td>".$value['repayment']."</td>";
                echo "<td>".$value['repayment_profit']."</td>";
                echo "<td>".$value['long_repayment']."</td>";
                echo "<td>".$value['long_repayment_profit']."</td>";
                echo "<td>".$value['p_all_userbuy']."</td>";
                echo "<td>".$value['longmoney']."</td>";
                echo "<td>".$value['real_longmoney']."</td>";
                echo "<td>".$value['l_profit']."</td>";
                echo "<td>".$value['lp_sellout']."</td>";
                echo "<td>".$value['lp_buy']."</td>";
                echo "<td>".$value['ltob']."</td>";
                echo "<td>".($value['invite_reward'] + $value['invite_user_reward'] + $value['activity_reward'] + $value['i_first_buy']+$value['hongbao']+$value['coupon']+$value['buchang']+$value['exp_profit'])."</td>";
                echo "<td>".$value['i_first_buy']."</td>";
                echo "<td>".$value['invite_reward']."</td>";
                echo "<td>".$value['invite_user_reward']."</td>";
                echo "<td>".$value['activity_reward']."</td>";
                echo "<td>".$value['sxf']."</td>";
                echo "<td>".$value['hongbao']."</td>";
                echo "<td>".$value['coupon']."</td>";
                echo "<td>".$value['buchang']."</td>";
                echo "<td>".$value['exp_profit']."</td>";
                echo "<td>".$value['notwithhold']."</td>";
                echo "<td>".$value['withhold']."</td>";
                echo "<td>".$value['fall_notwithdraw']."</td>";
                echo "<td>".$value['fall_withdraw']."</td>";
                echo "</tr>";
            }
		}
		echo "</tbody></table>";
?>