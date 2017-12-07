<form id="pagerForm"  method="post" action="<?php echo OP_DOMAIN;?>userinfomanage/getUserlogDetails/">
        <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
        <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
        <input type="hidden" name="phone" value="<?php echo isset($phone)?$phone:''?>"/>
        <input type="hidden" value="search" name="op">
</form>

 <script type="text/javascript">
	function validate(){
		var phone = $('#phone').val();
	    	if(!phone){
	    		alertMsg.error('请输入电话号码');
	    		return false;
	        }else{
				$('#pageForm').submit();
	     	}          	
	}

		
	function pass(){
		alertMsg.confirm("确定审核通过吗?", {
			okCall: function(){
				url = "<?php echo OP_DOMAIN;?>withdrawshenghe/weehourshenghe/<?php echo $logid; ?>";
				$.post(url, '', DWZ.ajaxDone, "json");
			}
		});
	}
	function notpass(){
		alertMsg.confirm("确定审核退回吗?", {
			okCall: function(){
				url = "<?php echo OP_DOMAIN;?>withdrawshenghe/weehourshenghetuihui/<?php echo $logid; ?>";
				$.post(url, '', DWZ.ajaxDone, "json");
			}
		});
	}
</script> 

<div class="pageHeader">
	<form  id="pageForm"  onsubmit='return navTabSearch(this); 'action='<?php echo OP_DOMAIN;?>userinfomanage/getUserlogDetails/' method='post'    class="pageForm required-validate">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
				手机号码:<input name="phone" id="phone" class="required" value="<?php echo isset($phone)?$phone:''?>"   />
				</td>
				<td>
					交易类型:
				<select name="type">
                    <option value="0">全部</option>
                    <option value="1" <?php if($type == 1){ echo 'selected';}?>>收入</option>
                    <option value="2" <?php if($type == 2){ echo 'selected';}?>>支出</option>
                </select>
				</td>
				<td>时间:<input name="stime" readonly="true"  class="date" value="<?php echo isset($stime)?$stime:''?>"/>&nbsp;&nbsp;至
           				<input name="etime" readonly="true" class="date" value="<?php echo isset($etime)?$etime:''?>"/>
           		</td>
           		<td><input type="hidden" value="search" name="op"><button type="button" onclick="validate()">检索</button></td>
           		<?php if(isset($orderid)){?>
           		<td><a href="<?php echo OP_DOMAIN;?>withdrawshenghe/weehourshenghe/$orderid" target="ajaxTodo" title="确定审核通过吗?">审核</a></td>
           		<td><a href="<?php echo OP_DOMAIN;?>withdrawshenghe/weehourshenghetuihui/<?php echo $_order['id']; ?>" target="ajaxTodo" title="确定审核退回吗?">审核退回</a></td>
           		<?php }?>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
	<div class="panelBar" style="height:45px;padding-top: 7px">
		<table>
		<tr>
		<td width="80%">
			余额：<?php echo $balance?>&nbsp;&nbsp;&nbsp;定期总额：<?php echo $product_money?>&nbsp;&nbsp;&nbsp;活期总额：<?php echo $longmoney?>&nbsp;&nbsp;&nbsp;总计：<?php echo $balance+$product_money+$longmoney?><br>
			充值：<?php echo $pay_money?>&nbsp;&nbsp;&nbsp;活动奖励：<?php echo $activity_money?>&nbsp;&nbsp;&nbsp;邀请奖励：<?php echo $invite_reward_money?>&nbsp;&nbsp;&nbsp;活期利息：<?php echo $lprofit?>&nbsp;&nbsp;&nbsp;定期利息：<?php echo $sum_product_profit?>&nbsp;&nbsp;&nbsp;总计：<?php echo $pay_money+$activity_money+$invite_reward_money+$lprofit+$sum_product_profit?><br>
			取现：<?php echo $withdraw_money?>&nbsp;&nbsp;&nbsp;对账差额：<?php echo $diff?>
		</td>
		<td  style="text-align: right">
		<?php if(isset($logid)){?>
		<button type="button" id="btn_pass" onclick="pass()">审核通过</button>
		<button type="button" id="btn_not" onclick="notpass()">审核退回</button>
		<?php }?>
		</td>
		</tr>
		</table>
    </div>
	<table class="list" width="100%" layoutH="125">
        <thead>
            <tr>
                <th width="5%">id</th>
                <th width="5%">用户id</th>
                <th width="15%">交易流水号</th>
                <th width="10%">日期</th>
                <th width="10%">类型</th>
                <th width="5%">收入</th>
                <th width="5%">支出</th>
                <th width="5%">账户余额（元）</th>
                <th width="10%">action</th>
            </tr>
        </thead>
        <tbody>
        <!-- --- $banklist['01050000']['name']);-->
         <?php $oldname = ''; 
         $count_money=$count_yest_profit=$count_profit='';
         ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <?php 
            	$type="";
            	switch ($value['action']){
            		case 0:$type="充值";break;
            		case 4:$type="定期还款";break;
            		case 1:$type="购买定期";break;
            		case 11:$type="购买活期";break;
            		case 2:$type="取现";break;
            		case 13:$type="活期转余额";break;
            		case 5:$type="活动赠送";break;
            		case 6:$type="邀请奖励";break;
            		case 10:$type="充值失败";break;
            		case 20:$type="取现失败";break;
            		case 21:$type="取现退回";break;
            	}
            ?>
            <tr>
                <td><?php echo $value['id'];?></td>
                <td><?php echo $value['uid'];?></td>
                <td><?php echo $value['orderid'];?></td>
                <td><?php echo date('Y-m-d H:i:s',$value['ctime'])?></td>
                <td><?php echo $value['pname'];?></td>
                <td><?php echo $value['in'];?></td>
                <td><?php echo $value['out']?></td>
                <td><?php echo $value['balance']?></td>
                <td><?php echo $type?></td>
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
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>"numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>


