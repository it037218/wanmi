<?php
header ( "Content-type:application/vnd.ms-excel" );
header ( "Content-Disposition:filename=合同清算".date('Ymd').".xls" );
	echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
	<table>
        <thead>
            <tr>
                <th>公司名称</th>
                <th>合同编号</th>
                <th>合同金额</th>
                <th>债权利率</th>
                <th>打款期限</th>
                <th>打款日</th>
                <th>到期日</th>
                <th>回款合计</th>
                <th>回款利息</th>
			 	<th>用户本息（定+活）</th>
                <th>定本</th>
                <th>定息</th>
                <th>活本</th>
                <th>活息</th>
                <th>应付客户利息合计</th>
                <th>服务费收入</th>
                <th>状态</th>
            </tr>
        </thead>
        <tbody>";
        if(!empty($list)){
        	foreach($list AS $key => $value){
        		$benxi = diff_days($value['interesttime'], $value['repaymenttime'])*$value['con_money']*$value['con_income']/36000+$value['con_money'];
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
            	$remit_desc = empty($value['remittime'])?'':date("Y-m-d",$value['remittime']);
            	$service_all = $remit?round(bcsub($remitbenxi,$dingqibenxi,2)-$value['stockmoney'],2):0;
            	$service_pat = $remit?round(bcsub($remitbenxi,$dingqibenxi,2)-$value['stockmoney']-$stocklixi,2):0;
            	$status_desc = $value['backstatus']==3?'已回款':'未回款';
            	$temp_profit = round($value['money']-$value['stockmoney']+$value['totalProfit'],2);
            	$product_money = $value['money']-$value['stockmoney'];
            	$stockLixi_temp = $stocklixi+round($value['totalProfit'],2);
            	
                echo "<tr>";
                echo "<td>".$value['corname']."</td>";
                echo "<td>".$value['con_number']."</td>";
                echo "<td>".$value['con_money']."</td>";
                echo "<td>".$value['con_income']."</td>";
                echo "<td>".$remitdays."</td>";
                echo "<td>".$remit_desc."</td>";
                echo "<td>".$value['repaymenttime']."</td>";
                echo "<td>".round($remitbenxi,2)."</td>";
                echo "<td>".round($remitlixi,2)."</td>";
                echo "<td>".round($value['money']+$value['totalProfit']+$stocklixi,2)."</td>";
                echo "<td>".$product_money."</td>";
                echo "<td>".round($value['totalProfit'],2)."</td>";
                echo "<td>".$value['stockmoney']."</td>";
                echo "<td>".$stocklixi."</td>";
                echo "<td>".$stockLixi_temp."</td>";
                echo "<td>".$service_pat."</td>";
                echo "<td>".$status_desc."</td>";
                echo "</tr>";
            }
		}
		echo "</tbody></table>";
		function diff_days($start, $end){
			list($a_year, $a_month, $a_day) = explode('-', $start);
			list($b_year, $b_month, $b_day) = explode('-', $end);
			$a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
			$b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
			$d = abs(($b_new-$a_new)/86400)+1;
			return $d;
		}
?>
