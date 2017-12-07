<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/expmoneystatistics">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <input type="hidden" value="searchexpmoneybycondition" name="op">
    <input type="hidden" value="<?php echo isset($account)?$account:''?>" name="account">
    <input type="hidden" value="<?php echo isset($money)?$money:''?>" name="money">
    <input type="hidden" value="<?php echo $status; ?>" name="status">
    <input type="hidden" value="<?php echo $type; ?>" name="type">
    <input type="hidden" value="<?php echo isset($days)?$days:''?>" name="days">
    <input type="hidden" value="<?php echo isset($stime)?$stime:''?>" name="stime">
    <input type="hidden" value="<?php echo isset($etime)?$etime:''?>" name="etime">
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/expmoneystatistics" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					注册手机号：<input type="text"  value="<?php echo isset($account)?$account:''?>"  id="id_account" name="account">
				</td>
				<td>
					体验金金额：<input type="text"  value="<?php echo isset($money)?$money:''?>"  id="id_money" name="money">
				</td>
				<td>
					获得方式：
					<select name="type" id="id_type">
						<option value="0" <?php if($type == 0){ echo 'selected';}?>>全部</option>
						<option value="1" <?php if($type == 1){ echo 'selected';}?>>新手注册</option>
						<option value="3" <?php if($type == 3){ echo 'selected';}?>>系统赠送</option>
						<option value="4" <?php if($type == 4){ echo 'selected';}?>>积分兑换</option>
					</select>
				</td>
			</tr>
			<tr>
			<td>
					状态：
					<select name="status" id="id_status">
						<option value="4">全部</option>
						<option value="0" <?php if($status == 0){ echo 'selected';}?>>待使用</option>
						<option value="1" <?php if($status == 1){ echo 'selected';}?>>使用中</option>
						<option value="2" <?php if($status == 2){ echo 'selected';}?>>已收回</option>
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
			<input type="hidden" value="searchexpmoneybycondition" name="op">
		</table>
		<div class="subBar">
			<ul style="float:left;padding-top: 10px">
				<li><span>待使用体验金券：<?php echo $totalNotExpired['count'].'张    '.$totalNotExpired['totalmoney'].'元';?></span></li>
            	<li style="padding-left: 15px"><span>使用中体验金：<?php echo $totalUsing['count'].'张    '.$totalUsing['totalmoney'].'元';?></span></li>
            	<li style="padding-left: 15px"><span>已收回体验金：<?php echo $totalBacked['count'].'张    '.$totalBacked['totalmoney'].'元';?></span></li>
            	<li style="padding-left: 15px"><span>已过期体验金：<?php echo $totalExpired['count'].'张    '.$totalExpired['totalmoney'].'元';?></span></li>
            	<li style="padding-left: 15px"><span>体验金累计收益：<?php echo $expmoneysum.'元';?></span></li>
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
                <th width="4%">注册号码</th>
                <th width="3%">姓名</th>
                <th width="6%">体验金名称</th>
                <th width="8%">获得时间</th>
                <th width="6%">使用时间</th>
                <th width="4%">过期时间</th>
                <th width="3%">体验金金额</th>
                <th width="6%">体验金有效期</th>
                <th width="2%">剩余天数</th>
                <th width="3%">状态</th>
                <th width="4%">获得方式</th>
                <th width="3%">体验金收益</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo $value['id'] ;?></td>
                    <td><?php echo empty($accounts[$value['uid']])?'':$accounts[$value['uid']] ;?></td>
                    <td><?php echo empty($names[$value['uid']])?'':$names[$value['uid']] ;?></td>
                    <td><?php echo $value['name'] ;?></td>
                    <td><?php echo date('Y-m-d H:i:s',$value['ctime']) ;?></td>
                    <td><?php if(!empty($value['utime'])){echo date('Y-m-d H:i:s',$value['utime']);}?></td>
                    <td><?php echo date('Y-m-d',$value['etime']);?></td>
                    <td><?php echo $value['money'] ;?></td>
                    <td><?php echo date('Y-m-d',$value['ctime'])." -- ".date('Y-m-d',$value['etime']);?></td>
                    <td><?php echo floor(($value['etime']-time())/86400)>0?floor(($value['etime']-time())/86400):'-';?></td>
                    <td><?php
                    	if($value['status']==2){
                    		echo '已收回';
                    	}elseif ($value['status']==1){
                    		echo '使用中';
                    	}else{
                    		if (time()>$value['etime']){
                    			echo '已过期';
                    		}else{
                    			echo '待使用';
                    		}
                    	}
                    ?></td>
                    <td><?php 
                    	if($value['type']==1){
                    		echo '新手注册';
                    	}else if($value['type']==2){
                    		echo '购买获得';
                    	}else if($value['type']==3){
                    		echo '系统赠送';
                    	}else if($value['type']==4){
                    		echo '积分兑换';
                    	}
                    ?></td>
                    <td><?php echo empty($value['profit'])?'':$value['profit'];?></td>
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


