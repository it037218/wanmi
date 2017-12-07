<div class="pageContent">
    <div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <li class="line">line</li>
            <li><span>产品名称</span></li>
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/qs_contract/showproduct" method="post">
             <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchpname)?$searchpname:'请输入搜索内容'?>"  id="searchpname" name="searchpname"></li>
             <li><input type="hidden" value="<?php echo $cid;?>" name="cid" id ="cid"><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
             </form>
             <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/qs_contract/showproduct" method="post">
             <li><span>起息日期</span></li>
             <li><input type="text" onfocus="if(this.value=='请输入开始日期') this.value='';" onblur="if(this.value=='') this.value='请输入开始日期';" value="<?php echo isset($searchstart)?"请输入开始日期":'请输入开始日期'?>"  id="searchstart" name="searchstart" class="date">&nbsp;&nbsp;至</li>
             <li><input type="text" onfocus="if(this.value=='请输入结束日期') this.value='';" onblur="if(this.value=='') this.value='请输入结束日期';" value="<?php echo isset($searchend)?'请输入结束日期':'请输入结束日期'?>"  id="searchend" name="searchend" class="date"></li>
             <li><input type="hidden" value="<?php echo $cid;?>" name="cid" id ="cid"><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
             <li style="margin-left:30px"><a style="padding-top:5px" href="<?php echo OP_DOMAIN;?>/qs_contract/export/<?php echo $cid;?>" target="_blank">导出</a></li>
             </form>
            <li class="line">line</li>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="11%">公司名称</th>
                <th width="7%">合同编号</th>
                <th width="10%">产品名称</th>
                <th width="6%">产品金额</th>
                <th width="5%">债权利率</th>
                <th width="5%">债务利率</th>
                <th width="5%">已售金额</th>
                <th width="7%">客户起息日</th>
                <th width="7%">合作方起息日</th>
                <th width="9%">截止日期</th>
                <th width="7%">客户期限</th>
                <th width="7%">合作方期限</th>
                <th width="7%">产品状态</th>
                <th width="7%">是否上传</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)){$total_money=0;$total_sellmoney=0;$total_profit=0;?>
            <?php foreach($list AS $key => $value){
            		$total_money = $total_money+$value['money'];
            		$total_sellmoney = $total_sellmoney+$value['sellmoney'];
            		$udiff=diff_days($value['uietime'], $value['uistime']);
            		$cdiff=diff_days($value['cietime'], $value['cistime']);
            		if($value['sellmoney']>0){
	            		$total_profit=$total_profit+$value['income']/100/365 * $value['sellmoney'] * $udiff;
            		}
            ?>
            <?php switch ($value['status']){
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
               ?>
                <tr>
                <td><?php echo $contract['corname'];?></td>
                <td><?php echo $contract['con_number'];?></td>
                <td><?php echo $value['pname'];?></td>
                <td><?php echo $value['money'];?></td>
                <td><?php echo $contract['con_income'];?></td>
                <td><?php echo $value['income'];?></td>
                <td><?php echo $value['sellmoney'];?></td>
                <td><?php echo $value['uistime'];?></td>
                <td><?php echo $value['cistime'];?></td>
                <td><?php echo $value['uietime'];?></td>
                <td><?php echo $udiff ;?></td>
                <td><?php echo $cdiff ;?></td>
                <td><?php echo $status;?>
                <td><a href="<?php echo OP_DOMAIN?>/remit/editremit/<?php echo $value['pid'];?>" target="navtab" title="编辑打款凭条"><?php echo $is_upload;?></a>
                </td>
                <td>
                
				<?php if(isset($rtnstatus[$value['cid']])):?>
				    <a href="<?php echo OP_DOMAIN?>/qs_contract/downtoline/<?php echo $value['cid'];?>" target="ajaxTodo" title="是否确定售罄">停止销售</a>
 				&nbsp;&nbsp;|&nbsp;&nbsp;
 				    
 				   <?php else:?>
 				   
				<?php endif;?>
                </td>
                </tr>
            <?php }?>
            <tr>
            	<td>累计已售金额：</td><td><?php echo $total_sellmoney;?></td>
            	<td></td>
            	<td>累计用户利息：</td><td><?php echo round($total_profit,2);?></td>
            	<td></td>
            	<td>累计用户本息：</td><td><?php echo round($total_sellmoney+$total_profit,2);?></td>
            </tr>
		<?php }?>
		</tbody>
	</table>
	
</div>
<?php 
function diff_days($start, $end){
    list($a_year, $a_month, $a_day) = explode('-', $start);
    list($b_year, $b_month, $b_day) = explode('-', $end);
    $a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
    $b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
    $d = abs(($b_new-$a_new)/86400)+1;
    return $d;
}
?>
