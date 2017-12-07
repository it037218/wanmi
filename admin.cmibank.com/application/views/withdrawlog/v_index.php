<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN?>withdrawlog">
     <input type="hidden" name="pageNum" value="<?php echo isset($pageNum) ? $pageNum : 0; ?>" />
   	 <input type="hidden" name="numPerPage" value="<?php echo isset($numPerPage) ? $numPerPage : 0; ?>" />
   	 <?php if(isset($phone)){?>
     <input type="hidden" name="phone" value="<?php echo $phone; ?>" />
     <?php }?>
     <?php if(isset($uid)){?>
      <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
     <?php }?>
     <?php if(isset($failed)){?>
      <input type="hidden" name="failed" value="1" />
     <?php }?>
     <?php if(isset($ssucctime)){?>
      <input type="hidden" name="ssucctime" value="<?php echo $ssucctime; ?>" />
      <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($esucctime)){?>
      <input type="hidden" name="esucctime" value="<?php echo $esucctime; ?>"/>
      <input type="hidden" value="search" name="op">
     <?php }?>
     <input type="hidden" value="search" name="op">
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>withdrawlog" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					手机号码：<input name="phone" id="phone" value="<?php echo isset($phone)?$phone:''?>"/>
				</td>
				<td>
					订单号码：<input name="orderid" id="orderid" value="<?php echo isset($orderid)?$orderid:''?>"/>
				</td>
				<td>
					日期：<input name="ssucctime" readonly="true"  class="date"  value="<?php echo isset($ssucctime) ? $ssucctime : "";?>"  />&nbsp;&nbsp;至
            			 <input name="esucctime" readonly="true" class="date"  value="<?php echo isset($esucctime) ? $esucctime : "";?>"  />
				</td>
				<td><label style="width: 100%"><input type="checkbox" name="failed" value="1" <?php echo isset($failed) ? 'checked' : "";?>/>失败</label></td>
				<td><input type="hidden" value="search" name="op"><button type="submit">检索</button></td>
				<td>
				<?php if($editable==1){?>
					<?php if(empty($restrict)){?>
						<a href="<?php echo OP_DOMAIN?>/withdrawlog/stopWithdraw" target="ajaxTodo" title="您真暂停取现?">暂停取现</a></td>
					<?php }else{?>
						<a href="<?php echo OP_DOMAIN?>/withdrawlog/startWithdraw" target="ajaxTodo" title="您真的要开启取现?">开启取现</a></td>
					<?php }?>
				<?php }?>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <li><span>总笔数：</span></li>
            <li style="padding-top:7px"><?php echo $count; ?></li>
            <li><span>取现总金额：</</span></li>
            <li style="padding-top:7px"><?php echo isset($sum_money)?$sum_money:0; ?></li>
            <li><span>金运通：</</span></li>
            <li style="padding-top:7px"><?php echo isset($sum_money_jyt)?$sum_money_jyt:0; ?></li>
            <li><span>宝付：</</span></li>
            <li style="padding-top:7px"><?php echo isset($sum_money_baofoo)?$sum_money_baofoo:0; ?></li>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="95">
        <thead>
            <tr>
                <th width="4%">订单id</th>
                <th width="4%">uid</th>
                <th width="20%">用户订单号</th>
                <th width="5%">用户名</th>
                <th width="5%">状态码</th>
                <th width="5%">状态</th>
                <th width="6%">用户日志id </th>
                <th width="5%">金额</th>
                <th width="8%">取现时间</th>
                <th width="8%">收支明细</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo $value['id'] ;?></td> 
                    <td><?php echo $value['uid'] ;?></td> 
                    <td><?php echo $value['orderid'] ;?></td>
                    <td><?php echo empty($names[$value['uid']])?'':$names[$value['uid']];?></td>
                    <td><?php echo $value['status_code'] ;?></td>
                    <td><?php echo $value['status']>0?($value['status']==1?'交易失败':'交易成功'):'处理中'; ?></td>
                    <td><?php echo $value['logid'] ;?></td>
                    <td><?php echo $value['money'] ;?></td>          
                    <td><?php echo date('Y-m-d H:i:s',$value['succtime']);?></td>
                    <td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getUserlogDetails/<?php echo $value['uid'];?>" target="navtab" title="用户收支明细">查看</a>
                    	<?php if($editable==1){?>
                    		<?php if(empty($value['status'])){?>
                    		&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo OP_DOMAIN?>/withdrawlog/returnmoney/<?php echo $value['orderid'] ;?>/<?php echo $value['uid'];?>" target="ajaxTodo" title="您真的要退款吗?">退款</a>
                    		<?php }else if($value['status']==1){?>
                    		&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo OP_DOMAIN?>/withdrawlog/handle/<?php echo $value['orderid'] ;?>/<?php echo $value['uid'];?>" target="ajaxTodo" title="您真的要处理吗?">处理</a>
                    		<?php }?>
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
                <option value="20" <?php echo $numPerPage == 20 ? 'selected' : ''; ?>>20</option>
                <option value="40" <?php echo $numPerPage == 40 ? 'selected' : ''; ?>>40</option>
            </select>
            <span>共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>


