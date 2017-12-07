<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/bireport">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" /> 
    <input type="hidden" name="stime" value="<?php echo $stime; ?>" />
    <input type="hidden" name="etime" value="<?php echo $etime; ?>" />
    <input type="hidden" name="type" value="<?php echo $type; ?>" />
    <input type="hidden" value="search" name="op">
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>bireport" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					渠道：<select name="type" id="id_type">
						<option value="0" 'selected'>全部</option>
						<option value="kh" <?php if($type == 'kh'){ echo 'selected';}?>>酷滑</option>
						<option value="hsp" <?php if($type == 'hsp'){ echo 'selected';}?>>惠锁屏</option>
						<option value="ldw" <?php if($type == 'ldw'){ echo 'selected';}?>>乐豆玩</option>
						<option value="jrtt" <?php if($type == 'jrtt'){ echo 'selected';}?>>今日头条1</option>
						<option value="jrtt2" <?php if($type == 'jrtt2'){ echo 'selected';}?>>今日头条2</option>
						<option value="jrtt3" <?php if($type == 'jrtt3'){ echo 'selected';}?>>今日头条3</option>
						<option value="appStore" <?php if($type == 'appStore'){ echo 'selected';}?>>appStore</option>
						<option value="cmibank" <?php if($type == 'cmibank'){ echo 'selected';}?>>cmibank</option>
						<option value="luckybag" <?php if($type == 'luckybag'){ echo 'selected';}?>>红包</option>
						<option value="invite" <?php if($type == 'invite'){ echo 'selected';}?>>邀请</option>
						<option value="db" <?php if($type == 'db'){ echo 'selected';}?>>兑吧</option>
						<option value="hbsp" <?php if($type == 'hbsp'){ echo 'selected';}?>>红包锁屏</option>
						</select>
				</td>
				<td>
					日期：<input name="stime" readonly="true"  class="date"  value="<?php echo isset($stime) ? $stime : "";?>"  />&nbsp;&nbsp;至
            			 <input name="etime" readonly="true" class="date"  value="<?php echo isset($etime) ? $etime : "";?>"  />
				</td>
				<td><input type="hidden" value="search" name="op"><button type="submit">检索</button></td>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
	<table class="list" width="100%" layoutH="65">
        <thead>
            <tr>
                <th width="8%">日期</th>
                <th width="3%">渠道</th>
                <th width="3%">激活数</th>
                <th width="4%">注册数</th>
                <th width='4%'>当日新增绑卡用户</th>
                <th width='4%'>当日新增交易用户</th>
                <th width="4%">当日新增交易</th>
                <th width="4%">老用户转化交易人数</th>
                <th width="4%">老用户转化交易</th>
                <th width="4%">新增交易汇总</th>
                <th width="5%">当日新增交易/注册</th>
                <th width="5%">当日新增交易/绑卡</th>
                <th width="4%">当日新增绑卡/注册</th>
                <th width="4%">新用户新增交易金额</th>
                <th width="4%">新用户新增定期交易金额</th>
                <th width="4%">新用户新增活期交易金额</th>
                <th width="4%">老用户转化交易金额</th>
                <th width="4%">当日购买用户数</th> 
                <th width="5%">当日定期交易金额</th>
                <th width="5%">当日活期交易金额</th>     
                <th width="4%">当日总交易金额</th>
                <th width="3%">当日申购笔数</th>
                <th width="5%">ARPU</th>
                <th width="3%">复购率</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)){?>
            <?php foreach($list as $value){?>
                <tr>
                    <td><?php echo $value['cdate'];?></td>
                    <td><?php echo $value['plat'];?></td>
                    <td><?php echo "0";?></td>
                    <?php if(empty($value['register'])){?>
	                    <td><?php echo $value['register'];?></td>
                    <?php }else{?>
                    	<td><a href="<?php echo OP_DOMAIN?>/useridentity/getSomeUsers/1/<?php echo $value['cdate'];?>/<?php echo $value['plat'];?>" target="navtab" title="用户基本信息管理"><?php echo $value['register'];?></a></td>
                    <?php }?>
                    <?php if(empty($value['bangkashu'])){?>
	                    <td><?php echo $value['bangkashu'];?></td>
                    <?php }else{?>
                    	<td><a href="<?php echo OP_DOMAIN?>/useridentity/getSomeUsers/2/<?php echo $value['cdate'];?>/<?php echo $value['plat'];?>" target="navtab" title="用户基本信息管理"><?php echo $value['bangkashu'];?></a></td>
                    <?php }?>
                    <td><?php echo $value['newuid'];?></td>
                    <td><?php echo $value['daydeal'];?></td>
                    <td><?php echo $value['oldnum'];?></td><!--老用户转化交易人数 -->
                    <td><?php echo $value['olddeal'];?></td><!--老用户转化交易 -->
                    <td><?php echo $value['countdeal'];?></td><!--新增交易汇总 -->
                    <td><?php echo floor($value['deal_reg']).'%';?></td>
                    <td><?php echo floor($value['daydeal_bangka']).'%';?></td>
                    <td><?php echo floor($value['bangka_reg']).'%';?></td>
                    <td><?php echo $value['dealmoney'];?></td><!--新用户新增交易金额 -->
                    <td><?php echo $value['dealmoney_d'];?></td><!--新用户新增定期交易金额 -->
                    <td><?php echo $value['dealmoney_h'];?></td><!--新用户新增活期交易金额  -->
                    <td><?php echo $value['oldmoney'];?></td><!--老用户转化交易金额 -->
                    <?php if(empty($value['daybuyuser'])){?><!--当日购买用户数 -->
	                    <td><?php echo $value['daybuyuser'];?></td>
                    <?php }else{?>
                    	<td><a href="<?php echo OP_DOMAIN?>/useridentity/getSomeUsers/3/<?php echo $value['cdate'];?>/<?php echo $value['plat'];?>" target="navtab" title="用户基本信息管理"><?php echo $value['daybuyuser'];?></a></td>
                    <?php }?>
                    <?php if(empty($value['daymoney_d'])){?><!--当日定期交易金额 -->
	                    <td><?php echo $value['daymoney_d'];?></td>
                    <?php }else{?>
                    	<td><a href="<?php echo OP_DOMAIN?>/useridentity/getBuyList/1/<?php echo $value['cdate'];?>/<?php echo $value['plat'];?>" target="navtab" title="用户基本信息管理"><?php echo $value['daymoney_d'];?></a></td>
                    <?php }?>
                    <?php if(empty($value['daymoney_h'])){?><!--当日活期交易金额 -->
	                    <td><?php echo $value['daymoney_h'];?></td>
                    <?php }else{?>
                    	<td><a href="<?php echo OP_DOMAIN?>/useridentity/getBuyList/2/<?php echo $value['cdate'];?>/<?php echo $value['plat'];?>" target="navtab" title="用户基本信息管理"><?php echo $value['daymoney_h'];?></a></td>
                    <?php }?>
                    <?php if(empty($value['daymoney'])){?><!--当日总交易金额 -->
	                    <td><?php echo $value['daymoney'];?></td>
                    <?php }else{?>
                    	<td><a href="<?php echo OP_DOMAIN?>/useridentity/getBuyList/3/<?php echo $value['cdate'];?>/<?php echo $value['plat'];?>" target="navtab" title="用户基本信息管理"><?php echo $value['daymoney'];?></a></td>
                    <?php }?>
                    <td><?php echo $value['daynumber'];?></td><!--当日申购笔数 -->
                    <td><?php echo floor($value['arpu']); ?></td>
                    <td><?php echo round($value['fugoulv'],2).'%';?></td>
                </tr>
            <?php }?>
		<?php }?>
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

