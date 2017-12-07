<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/couponstatistics">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <input type="hidden" value="searchconponbycondition" name="op">
    <input type="hidden" value="<?php echo isset($account)?$account:''?>" name="account">
    <input type="hidden" value="<?php echo isset($sendmoney)?$sendmoney:''?>" name="sendmoney">
    <input type="hidden" value="<?php echo $type; ?>" name="type">
    <input type="hidden" value="<?php echo $ptid; ?>" name="ptid">
    <input type="hidden" value="<?php echo $status; ?>" name="status">
    <input type="hidden" value="<?php echo isset($days)?$days:''?>" name="days">
    <input type="hidden" value="<?php echo isset($stime)?$stime:''?>" name="stime">
    <input type="hidden" value="<?php echo isset($etime)?$etime:''?>" name="etime">
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/couponstatistics" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					注册手机号：<input type="text"  value="<?php echo isset($account)?$account:''?>"  id="id_account" name="account">
				</td>
				<td>
					抵用券金额：<input type="text"  value="<?php echo isset($sendmoney)?$sendmoney:''?>"  id="id_sendmoney" name="sendmoney">
				</td>
				<td>
					获得类型：
					<select name="type" id="id_type">
						<option value="0">全部</option>
						<option value="1" <?php if($type == 1){ echo 'selected';}?>>注册赠送</option>
						<option value="2" <?php if($type == 2){ echo 'selected';}?>>绑卡赠送</option>
						<option value="3" <?php if($type == 3){ echo 'selected';}?>>购买赠送</option>
						<option value="4" <?php if($type == 4){ echo 'selected';}?>>首购赠送</option>
						<option value="5" <?php if($type == 5){ echo 'selected';}?>>系统赠送</option>
						<option value="6" <?php if($type == 6){ echo 'selected';}?>>积分兑换</option>
					</select>
				</td>
				<td>
					购买产品：
					<select name="ptid" id="id_ptid">
						<option value="0">全部</option>
						<option value="45" <?php if($ptid == 45){ echo 'selected';}?>>新人旺</option>
						<option value="41" <?php if($ptid == 41){ echo 'selected';}?>>1月旺</option>
						<option value="42" <?php if($ptid == 42){ echo 'selected';}?>>2月旺</option>
						<option value="40" <?php if($ptid == 40){ echo 'selected';}?>>3月旺</option>
						<option value="44" <?php if($ptid == 44){ echo 'selected';}?>>季度旺</option>
						<option value="43" <?php if($ptid == 43){ echo 'selected';}?>>6月旺</option>
						<option value="43" <?php if($ptid == 46){ echo 'selected';}?>>返利旺</option>
					</select>
				</td>
			</tr>
			<tr>
			<td>
					状态：
					<select name="status" id="id_status">
						<option value="0">全部</option>
						<option value="1" <?php if($status == 1){ echo 'selected';}?>>待使用</option>
						<option value="2" <?php if($status == 2){ echo 'selected';}?>>已使用</option>
						<option value="3" <?php if($status == 3){ echo 'selected';}?>>已过期</option>
					</select>
				</td>
				<td>
					剩余天数：<input type="text"  value="<?php echo isset($days)?$days:''?>"  id="id_days" name="days" class="digits">
				</td>
				<td colspan="2">
					时间：<input type="text"  value="<?php echo isset($stime)?$stime:''?>"  id="id_stime" name="stime" class="date">&nbsp;&nbsp;至 
           				 <input type="text"  value="<?php echo isset($etime)?$etime:''?>"  id="id_etime" name="etime" class="date">
				</td>
			</tr>
			<input type="hidden" value="searchconponbycondition" name="op">
		</table>
		<div class="subBar">
			<ul style="float:left;padding-top: 10px">
				<li><span>待使用抵用券：<?php echo $totalNotExpired['count'].'张    '.$totalNotExpired['totalmoney'].'元';?></span></li>
            	<li style="padding-left: 15px"><span>已使用抵用券：<?php echo $totalUsed['count'].'张    '.$totalUsed['totalmoney'].'元';?></span></li>
            	<li style="padding-left: 15px"><span>已过期抵用券：<?php echo $totalExpired['count'].'张    '.$totalExpired['totalmoney'].'元';?></span></li>
            	<li style="padding-left: 15px"><span>累计总抵用券：<?php echo $total['count'].'张    '.$total['totalmoney'].'元';?></span></li>
            	<li style="padding-left: 15px"><span>累计抵用券金额：<?php echo $couponsum['sum_sendmoney'].'元';?></span></li>
            	<li style="padding-left: 15px"><span>购买金额：<?php echo $couponsum['sum_buymoney'].'元';?></span></li>
			</ul>
			<ul>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">检索</button></div></div></li>
			</ul>
		</div>
	</div>
	</form>
</div>
<div class="pageContent">
	<table class="list" width="100%" layoutH="115">
        <thead>
            <tr>
                <th width="4%">id</th>
                <th width="6%">注册号码</th>
                <th width="4%">姓名</th>
                <th width="8%">获得时间</th>
                <th width="6%">使用时间</th>
                <th width="6%">过期时间</th>
                <th width="5%">抵用券金额</th>
                <th width="6%">抵用券有效期</th>
                <th width="3%">剩余天数</th>
                <th width="4%">状态</th>
                <th width="5%">获得方式</th>
                <th width="15%">可购买产品</th>
                <th width="3%">起购金额</th>
                <th width="5%">已购买产品</th>
                <th width="4%">购买金额</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo $value['id'] ;?></td>
                    <td><?php echo $value['account'] ;?></td>
                    <td><?php echo empty($names[$value['uid']])?'':$names[$value['uid']] ;?></td>
                    <td><?php echo date('Y-m-d H:i:s',$value['ctime']) ;?></td>
                    <td><?php if(!empty($value['utime'])){echo date('Y-m-d H:i:s',$value['utime']);}?></td>
                    <td><?php echo date('Y-m-d',$value['etime']);?></td>
                    <td><?php echo $value['sendmoney'] ;?></td>
                    <td><?php echo date('Y-m-d',$value['stime'])." -- ".date('Y-m-d',$value['etime']);?></td>
                    <td><?php echo empty($value['utime'])?(floor(($value['etime']-time())/86400)>=0?floor(($value['etime']-time())/86400):'-'):'-';?></td>
                    <td><?php
                    	if(empty($value['utime'])){
                    		if($value['stime']>time()){
                    			echo '即将可用';
                    		}else if (time()>$value['etime']){
                    			echo '已过期';
                    		}else{
                    			echo '待使用';
                    		}
                    	}else {
                    		echo '已使用';
                    	}
                    ?></td>
                    <td><?php 
                    	switch ($value['type']){
                    		case 1:echo "注册赠送";break;
                    		case 2:echo "绑卡赠送";break;
                    		case 3:echo "购买赠送";break;
                    		case 4:echo "首购赠送";break;
                    		case 5:echo "系统赠送";break;
                    		case 6:echo "积分兑换";break;
                    	}
                    ?></td>
                    <td><?php echo $value['pnames'] ;?></td>
                    <td><?php echo $value['minmoney'] ;?></td>
                    <td><?php echo empty($pnames[$value['pid']])?'':$pnames[$value['pid']] ;?></td>
                    <td><?php echo empty($value['buymoney'])?'':$value['buymoney'];?></td>
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
            <span>条，共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>


