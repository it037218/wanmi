<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/contract/bond">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($status)){?>
     <input type="hidden" name="status" value="<?php echo $status; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
    <?php if(isset($searchcorname)){?>
     <input type="hidden" name="searchcorname" value="<?php echo $searchcorname; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($searchcon_number)){?>
     <input type="hidden" name="searchcon_number" value="<?php echo $searchcon_number; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($searchrepaymentstime)){?>
     <input type="hidden" name="searchrepaymentstime" value="<?php echo $searchrepaymentstime; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($searchrepaymentetime)){?>
     <input type="hidden" name="searchrepaymentetime" value="<?php echo $searchrepaymentetime; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($intereststime)){?>
     <input type="hidden" name="intereststime" value="<?php echo $intereststime; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($interestetime)){?>
     <input type="hidden" name="interestetime" value="<?php echo $interestetime; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($remitstime)){?>
     <input type="hidden" name="remitstime" value="<?php echo $remitstime; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($remitetime)){?>
     <input type="hidden" name="remitetime" value="<?php echo $remitetime; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/contract/bond" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					公司名称：<input type="text" value="<?php echo isset($searchcorname)?$searchcorname:''?>"  id="searchcorname" name="searchcorname">
				</td>
				<td>
				合同起息日：<input type="text" value="<?php echo isset($intereststime)?$intereststime:''?>"  id="intereststime" name="intereststime" class="date">&nbsp;&nbsp;至
						<input type="text" value="<?php echo isset($interestetime)?$interestetime:''?>"  id="interestetime" name="interestetime" class="date">
				</td>
				<td>
					状态：<select name="status" id="id_status">
						<option value="0">全部</option>
						<option value="1" <?php if($status == 1){ echo 'selected';}?>>未收取</option>
						<option value="2" <?php if($status == 2){ echo 'selected';}?>>已收取</option>
						<option value="4" <?php if($status == 4){ echo 'selected';}?>>未退还</option>
						<option value="3" <?php if($status == 3){ echo 'selected';}?>>已退还</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					合同编号：<input type="text" value="<?php echo isset($searchcon_number)?$searchcon_number:''?>"  id="searchcon_number" name="searchcon_number">
				</td>
				<td>
				合同还款日：<input type="text" value="<?php echo isset($searchrepaymentstime)?$searchrepaymentstime:''?>"  id="searchrepaymentstime" name="searchrepaymentstime" class="date">&nbsp;&nbsp;至
						<input type="text" value="<?php echo isset($searchrepaymentetime)?$searchrepaymentetime:''?>"  id="searchrepaymentetime" name="searchrepaymentetime" class="date">
				</td>
				<td>
				打款日：<input type="text" value="<?php echo isset($remitstime)?$remitstime:''?>"  id="remitstime" name="remitstime" class="date">&nbsp;&nbsp;至
						<input type="text" value="<?php echo isset($remitetime)?$remitetime:''?>"  id="remitetime" name="remitetime" class="date">
				</td>
				<td><input type="hidden" value="search" name="op"><button type="submit" >检索</button></td>
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
        </ul>
    </div>
	<table class="list" width="100%" layoutH="120">
        <thead>
            <tr>
                <th width="12%">公司名称</th>
                <th width="10%">合同编号</th>
                <th width="5%">合同金额</th>
                <th width="5%">保证金比率</th>
                <th width="5%">保证金金额</th>
                <th width="6%">应打款金额</th>
                <th width="6%">已经打款金额</th>
                <th width="5%">实际金额</th>
                <th width="6%">起息时间</th>
                <th width="6%">截止时间</th>
                <th width="6%">打款时间</th>
                <th width="4%">期限</th>
                <th width="5%">状态</th>
                <th width="15%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <?php if($value['returnbzjimg'] == ''){?>
	                	<?php if($value['bzjimg'] == ''){?>
	                	<tr style="color:red">
						<?php } else {?>
						<?php if(strtotime($value['repaymenttime'])>NOW){?>
								<tr>
							<?php }else{?>
								<tr style="color:red">
							<?php }?>
						<?php } ?>
					<?php } else {?>
						<tr style="color:#06F406">
					<?php } ?>
                <td><?php echo $value['corname'] ;?></td>
                <td><?php echo $value['con_number'] ?></td>
                <td><?php echo $value['real_money'] ;?></td>
                <td><?php echo $value['con_bzjbl'];?>%</td>
                <td><?php echo $value['real_money']*$value['con_bzjbl']/100 ;?></td>
                <td><?php echo $value['real_money'];?></td>
                <td><?php echo $value['con_dkje'] ;?></td>
                <td><?php echo $value['real_money']?></td>
                <td><?php echo $value['interesttime']?></td>
                <td><?php echo $value['repaymenttime']?></td>
                <td><?php echo date('Y-m-d',$value['remittime']);?></td>
                <td><?php echo diff_days($value['interesttime'],$value['repaymenttime']).'天'; ?></td>
                <td>
                <?php if($value['returnbzjimg'] == ''){?>
	                	<?php if($value['bzjimg'] == ''){?>
	                	未收取
						<?php } else {?>
							<?php if(strtotime($value['repaymenttime'])>NOW){?>
								已收取
							<?php }else{?>
								未退还	
							<?php }?>
						<?php } ?>
					<?php } else {?>
						已退还
					<?php } ?>
                </td>
                <td>
                 <a href="<?php echo OP_DOMAIN;?>contract/getBonddetail/<?php echo $value['cid']?>" target="dialog" title="查看">查看</a>
                 <?php if($editable==1){?>
                 <?php if($value['bzjimg'] == ''){?>
					&nbsp;&nbsp;|&nbsp;&nbsp;
					<a title="上传凭证" target="dialog" href="<?php echo OP_DOMAIN;?>contract/uploagimg/<?php echo $value['cid']?>">上传凭证</a>
					<?php } else {?>
					&nbsp;&nbsp;|&nbsp;&nbsp;
					<a title="修改凭证" target="dialog" href="<?php echo OP_DOMAIN;?>contract/uploagimg/<?php echo $value['cid']?>">修改凭证</a>
					<?php } ?>
				 <?php if($value['returnbzjimg'] == ''){?>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a title="上传归还凭证" target="dialog" href="<?php echo OP_DOMAIN;?>contract/uploagreturnimg/<?php echo $value['cid']?>">上传归还凭证</a>
				<?php } else {?>
					&nbsp;&nbsp;|&nbsp;&nbsp;
				<a title="修改归还凭证" target="dialog" href="<?php echo OP_DOMAIN;?>contract/uploagreturnimg/<?php echo $value['cid']?>">修改归还凭证</a>
					<?php } ?>
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
                <option value="30" <?php echo $numPerPage == 30 ? 'selected' : ''; ?>>30</option>
                <option value="50" <?php echo $numPerPage == 50 ? 'selected' : ''; ?>>50</option>
            </select>
            <span>条，共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>
<?php 
function diff_days($start, $end){
    list($a_year, $a_month, $a_day) = explode('-', $start);
    list($b_year, $b_month, $b_day) = explode('-', $end);
    $a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
    $b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
    return abs(($a_new-$b_new)/86400) + 1;
}
?>

