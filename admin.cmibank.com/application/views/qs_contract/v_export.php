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
                <th>产品名称</th>
                <th>产品金额</th>
                <th>债权利率</th>
                <th>债务利率</th>
                <th>已售金额</th>
                <th>客户起息日</th>
                <th>合作方起息日</th>
                <th>截止日期</th>
                <th>客户期限</th>
                <th>合作方期限</th>
                <th>产品状态</th>
                <th>是否上传</th>
            </tr>
        </thead>
        <tbody>";
        if(!empty($list)){
        	$total_money=0;$total_sellmoney=0;
            foreach($list AS $key => $value){
            	$total_money = $total_money+$value['money'];$total_sellmoney = $total_sellmoney+$value['sellmoney'];
            	switch ($value['status']){
	                case 0: $status = '末上架';break;
	                case 1: $status = '已上架';break;
	                case 2: $status = '(售罄)已下架';break;
	                case 3: $status = '售罄';break;
	                case 4: $status = '停售';break;
	                case 5: $status = '回款';break;
	                case 6: $status = '已还款';break;
	                case 7: $status = '(售罄)无人购买';break;
	                default: $status = '末知状态'; break;
                }
                switch ($value['is_upload']){
                    case 0: $is_upload = '未真实上传';break;
                    case 1: $is_upload = '';break;
                    default: $is_upload = '末知状态'; break;
                }
                echo "<tr>";
                echo "<td>".$contract['corname']."</td>";
                echo "<td>".$contract['con_number']."</td>";
                echo "<td>".$value['pname']."</td>";
                echo "<td>".$value['money']."</td>";
                echo "<td>".$value['income']."</td>";
                echo "<td>".$value['income']."</td>";
                echo "<td>".$value['sellmoney']."</td>";
                echo "<td>".$value['uistime']."</td>";
                echo "<td>".$value['cistime']."</td>";
                echo "<td>".$value['uietime']."</td>";
                echo "<td>".diff_days($value['uietime'], $value['uistime'])."</td>";
                echo "<td>".diff_days($value['cietime'], $value['cistime'])."</td>";
                echo "<td>".$status."</td>";
                echo "<td>".$is_upload."</td>";
                echo "</tr>";
            }
            echo "<tr> <td>总计：</td> <td></td> <td></td> <td>".$total_money."</td><td></td> <td></td> <td>".$total_sellmoney."</td></tr>";
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
