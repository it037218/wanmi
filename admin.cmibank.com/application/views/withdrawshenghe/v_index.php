<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>withdrawshenghe" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					类型：<select name="status" id="id_status">
						<option value="3" >全部</option>
						<option value="0" <?php if($status == 0){ echo 'selected';}?>>未审核</option>
						<option value="1" <?php if($status == 1){ echo 'selected';}?>>已审核</option>
						<option value="2" <?php if($status == 2){ echo 'selected';}?>>已退回</option>
						</select>
				</td>
				<td>
					申请日期：<input name="sqstime" readonly="true"  class="date"  value="<?php echo isset($sqstime) ? $sqstime : "";?>"  />&nbsp;&nbsp;至
            			 <input name="sqetime" readonly="true" class="date"  value="<?php echo isset($sqetime) ? $sqetime : "";?>"  />
				</td>
				<td>
					出款日期：<input name="ckstime" readonly="true"  class="date"  value="<?php echo isset($ckstime) ? $ckstime : "";?>"  />&nbsp;&nbsp;至
            			 <input name="cketime" readonly="true" class="date"  value="<?php echo isset($cketime) ? $cketime : "";?>"  />
				</td>
				<td><input type="hidden" value="search" name="op"><button type="submit">检索</button></td>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <li><span>总计：</span></li>
            <li style="padding-top:7px"><?php echo isset($total) ? $total : 0;?>元</li>
            <li style="padding-left:20px"><span>笔数：</span></li>
            <li style="padding-top:7px"><?php echo count($weehour_order);?></li>
            <li style="padding-left:20px"><span>已审核总金额：</span></li>
            <li style="padding-top:7px"><?php echo isset($totalToBe) ? $totalToBe : 0;?>元</li>
            <li style="padding-left:20px"><span>金运通金额：</span></li>
            <li style="padding-top:7px"><?php echo isset($totalToBeJYT) ? $totalToBeJYT : 0;?>元</li>
            <li style="padding-left:20px"><span>宝付金额：</span></li>
            <li style="padding-top:7px"><?php echo isset($totalToBeBaofoo) ? $totalToBeBaofoo : 0;?>元</li>
            <li style='padding-left:70px;'><span>当前默认渠道：</</span></li>
            <li style="padding-top:7px;color:red"><?php echo empty($defaultWithdraw)?'金运通':'宝付'; ?></li>
            <li style="padding-top:7px;padding-left:20px">
            <?php if(empty($defaultWithdraw)){?>
            	<a href="<?php echo OP_DOMAIN?>/withdrawshenghe/withdrawToBaofoo" target="ajaxTodo" title="您真要切换至宝付?">切换至宝付</a>
            <?php }else{?>
            	<a href="<?php echo OP_DOMAIN?>/withdrawshenghe/withdrawToJYT" target="ajaxTodo" title="您真要切换至金运通?">切换至金运通</a>
            <?php }?>
            </li>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="85">
        <thead>
            <tr>
                <th width="2%">id</th>
                <th width="4%">uid</th>
                <th width="5%">姓名</th>
                <th width="6%">电话号码</th>
                <th width="6%">金额</th>
                <th width="6%">取现后余额</th>
                <th width="7%">申请时间</th>
                <th width="7%">出款时间</th>
                <th width="8%">出款订单号</th>
                <th width="2%">状态</th>
                <th width="4%">当前渠道</th>
                <th width="4%">用户日志</th>
                <th width="5%">审核状态</th>
                <th width="10%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($weehour_order as $_order){?>
        <?php if(in_array($_order['uid'],$yanshi)){?>
        	<tr style='color:red'>
        <?php }else{?>
	        <tr>
        <?php }?>
            <td><?php echo $_order['id']; ?></td>
            <td><?php echo $_order['uid']; ?></td>
            <td><?php echo empty($names[$_order['uid']])?'':$names[$_order['uid']];?></td>
            <td><?php echo empty($phones[$_order['uid']])?'':$phones[$_order['uid']];?></td>
            <td><?php echo $_order['money']; ?></td>
            <td><?php echo $_order['balance']; ?></td>
            <td><?php echo date('Y-m-d H:i:s',$_order['ctime']); ?></td>
            <td><?php echo empty($_order['utime'])?'':date('Y-m-d H:i:s',$_order['utime']); ?></td>
            <td><?php echo $_order['orderid']; ?></td>
            <td><?php echo $_order['status']; ?></td>
            <td><?php echo empty($_order['plat'])?'金运通':'宝付'; ?></td>
            <td><?php echo $_order['logid']; ?></td>
            <td><?php echo $_order['shenghe']==1 ? '<font>已审核</font>' : ($_order['shenghe'] ? '<font color="slateblue">已退回</font>' : '<font color="red">未审核</font>'); ?></td>
            <td>
            <?php if($editable==1){?>
	            <?php if($_order['shenghe'] == 0){?>
	            	<a href="<?php echo OP_DOMAIN;?>userinfomanage/getUserlogDetails/<?php echo $_order['uid']?>/<?php echo $_order['id']; ?>" target="navtab" title="审核">审核</a>
	            	<?php if(!in_array($_order['uid'],$yanshi)){?>
		            	&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo OP_DOMAIN;?>withdrawshenghe/setYanshi/<?php echo $_order['uid']?>" target="ajaxTodo" title="您真要延迟审核?">延迟审核</a>
        			<?php }else{?>
        				&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo OP_DOMAIN;?>withdrawshenghe/removeYanshi/<?php echo $_order['uid']?>" target="ajaxTodo" title="您真取消延迟审核?">取消延迟</a>
        			<?php }?>
	            <?php }else if($_order['shenghe'] == 1){?>
	            	<a href="<?php echo OP_DOMAIN?>userinfomanage/getUserlogDetails/<?php echo $_order['uid']?>" target="navtab">查看账户</a>
	            	<?php if($_order['status'] == 0){?>
	            	&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo OP_DOMAIN;?>withdrawshenghe/cancel/<?php echo $_order['id']; ?>" target="ajaxTodo" title="您真要取消审核?">取消审核</a>
	            	<?php }?>
	            <?php }else if($_order['shenghe'] == 2){?>
	            	<a href="<?php echo OP_DOMAIN;?>userinfomanage/getUserlogDetails/<?php echo $_order['uid']?>/<?php echo $_order['id']; ?>" target="navtab" title="重新审核">重新审核</a>
	            <?php }?>
	            <?php if($_order['status'] == 0){?>
	            	<?php if(empty($_order['plat'])){?>
	            		<?php if($_order['money']<50000){?>
	            			&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo OP_DOMAIN;?>withdrawshenghe/changePlat/<?php echo $_order['id']?>" target="ajaxTodo" title="您真要切换至宝付吗?">宝付</a>
	            		<?php }else if($bankcode[$_order['uid']]=='ICBC'||$bankcode[$_order['uid']]=='ABC'||$bankcode[$_order['uid']]=='CMBCHINA'||$bankcode[$_order['uid']]=='CCB'||$bankcode[$_order['uid']]=='CIB'){?>
	            			&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo OP_DOMAIN;?>withdrawshenghe/changePlat/<?php echo $_order['id']?>" target="ajaxTodo" title="您真要切换至宝付吗?">宝付</a>
	            		<?php }?>
	            	<?php }else{?>
	            		&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo OP_DOMAIN;?>withdrawshenghe/changePlat/<?php echo $_order['id']?>" target="ajaxTodo" title="您真要切换至金运通?">金运通</a>
	            		<?php }?>
            	<?php }?>
            <?php }?>
            </td>
        </tr>
        <?php }?>
		</tbody>
	</table>
	
</div>


