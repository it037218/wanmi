<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/contract">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    
    <?php if(isset($corname)){?>
     <input type="hidden" name="searchcorname" value="<?php echo $corname; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     
     <?php if(isset($con_number)){?>
     <input type="hidden" name="searchcorname2" value="<?php echo $con_number; ?>" />
     <input type="hidden" value="searchinteresttime" name="op">
     <?php }?>
     
     <?php if(isset($repaymenttime_star)){?>
     <input type="hidden" name="searchrepaymenttime_star" value="<?php echo $repaymenttime_star;?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($repaymenttime_end)){?>
     <input type="hidden" name="searchrepaymenttime_end" value="<?php echo $repaymenttime_end; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     
     <?php if(isset($interesttime_star)){?>
     <input type="hidden" name="searchinteresttime_star" value="<?php echo $interesttime_star; ?>" />
     <input type="hidden" value="searchinteresttime" name="op">
     <?php }?>
     <?php if(isset($searchmortgagor)){?>
     <input type="hidden" name="searchmortgagor" value="<?php echo $searchmortgagor; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($interesttime_end)){?>
     <input type="hidden" name="searchinteresttime_end" value="<?php echo $interesttime_end; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($shenghe)){?>
     <input type="hidden" name="shenghe" value="<?php echo $shenghe; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/contract" method="post">
	<div class="searchBar">
		<table class="searchContent">
		<tr>
            <td> <span>公司名称：</span></td>
            <td><input type="text" value="<?php echo isset($corname)?$corname:''?>"  id="searchcorname" name="searchcorname"></td>
            <td><span>合同还款日：</span></td>
            <td><input type="text" value="<?php echo isset($repaymenttime_star)?$repaymenttime_star:''?>"  id="searchrepaymenttime_star" name="searchrepaymenttime_star" class="date">&nbsp;&nbsp;至</td>
            <td><input type="text" value="<?php echo isset($repaymenttime_end)?$repaymenttime_end:''?>"  id="searchrepaymenttime_end" name="searchrepaymenttime_end" class="date"></td>
        	<td><span>状态：</span></td>
        	<td><select name="shenghe">
						<option value="0" >全部</option>
						<option value="1" <?php if($shenghe == 1){ echo 'selected';}?>>已审核</option>
						<option value="2" <?php if($shenghe == 2){ echo 'selected';}?>>未审核</option>
						</select>
			</td>
        </tr>         
        <tr>
              <td><span>合同编号：</span></td>
              <td><input type="text" value="<?php echo isset($con_number)?$con_number:''?>"  id="searchcon_number" name="searchcon_number"></td>
              <td><span>起息日：</span></td>
              <td><input type="text" value="<?php echo isset($interesttime_star)?$interesttime_star:''?>"  id="searchinteresttime_star" name="searchinteresttime_star" class="date">&nbsp;&nbsp;至</td>
              <td><input type="text" value="<?php echo isset($interesttime_end)?$interesttime_end:''?>"  id="searchinteresttime_end" name="searchinteresttime_end" class="date"></td>
              <td> <span>真实借款人：</span></td>
              <td><input type="text" value="<?php echo isset($searchmortgagor)?$searchmortgagor:''?>"  id="searchmortgagor" name="searchmortgagor"></td>
              <td><input type="hidden" value="search" name="op"><button type="submit"   style='margin-left: 14px;' >检索</button>
              <td><?php if($editable==1){?><a title="新建合同" href="<?php echo OP_DOMAIN;?>contract/addcontract" target="navtab"  class="icon"><span>添加合同</span></a><?php }?></td>
        </table>
    </div>
    </form>
</div>
<div class="pageHeader">
	<div> 总计合同金额：<?php echo $sum_money['sum_con_money']?>元 &nbsp&nbsp&nbsp&nbsp总计下分金额：<?php echo $sum_money['sum_money']?>元&nbsp&nbsp&nbsp&nbsp总计可售金额：<?php echo $sum_money['sum_con_money']-$sum_money['sum_money']?>元
    </div>
	<table class="list" width="160%" layoutH="105">
        <thead>
            <tr>
                <th width="10%">公司名称</th>
                <th width="10%">合同编号</th>
                <th width="4%">合同实际金额</th>
                <th width="4%">可售金额（+回库）</th>
                <th width="4%">下分金额</th>
                <th width="3%">合同利率</th>
                <th width="3%">保证金比例</th>
                <th width="5%">合同起息时间</th>
                <th width="5%">合同回款时间</th>
                <th width="3%">合同期限</th>
                <th width="4%">上传日期</th>
                <th width="2%">项目图片</th>
                <th width="2%">资金图片</th>
                <th width="6%">用户电子模板</th>
                <th width="3%">债权人</th>
                <th width="4%">身份证号/营业执照号</th>
                <th width="3%">债权人印章</th>
                <th width="8%">担保法人</th>
                <th width="3%">担保人</th>
                <th width="3%">真实借款人</th>
                <th width="15%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)){?>
            <?php //var_dump($list);exit;?>
            <?php foreach($list AS $key=>$value){?>
                <tr>
                <td><?php echo $value['corname'] ;?></td>
                <td><?php echo $value['con_number'] ?></td>
                <td><?php echo $value['real_money'] ;?></td>
                <td><?php echo $value['con_money']-$value['money'] ;?></td>
                <td><?php echo $value['money'] ;?></td>
                <td><?php echo $value['con_income'] ;?>%</td>
                <td><?php echo $value['con_bzjbl'] ;?>%</td>
                <td><?php echo $value['interesttime'];?></td>
                <td><?php echo $value['repaymenttime'];?></td>
                <td><?php echo diff_days($value['interesttime'],$value['repaymenttime']).'天';?></td>
                <td><?php echo date('Y-m-d H:i:s',$value['ctime']);?></td>
                <td><a href="<?php echo $value['object_img']; ?>" target="_bank">预览</a></td>
                <td><a href="<?php echo $value['capital_img']; ?>" target="_bank">预览</a></td>
                <td><?php echo $usercontract[$value['ucid']]; ?></td>
                    <td><?php echo $value['creditor']; ?></td>

                    <td><?php echo $value['identity']; ?></td>

                    <td><?php if($value['seal']!=''){ echo "<a href='".$value['seal']."' target='_blank'>查看</a>"; }?></td>

                    <td><?php echo $value['guar_corp']; ?></td>

                    <td><?php echo $value['guarantee']; ?></td>
					
					<td><?php echo $value['mortgagor']; ?></td>

                <td>
					<a href="<?php echo OP_DOMAIN;?>contract/showcontractdetail/<?php echo $value['cid'];?>" target="navtab" title="合同详情">查看</a>
                <?php if($editable==1){?>
	                <?php if($value['shenghe'] == 0){?>
	                	&nbsp;&nbsp;|&nbsp;&nbsp; <a href="<?php echo OP_DOMAIN;?>contract/editcontract/<?php echo $value['cid'];?>" target="navtab" title="合同编辑">基本编辑</a>
	                <?php if($value['money'] == 0){?>
	                    &nbsp;&nbsp;|&nbsp;&nbsp; <a href="<?php echo OP_DOMAIN;?>contract/delcontract/<?php echo $value['cid']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
	                <?php }?>
	                	&nbsp;&nbsp;|&nbsp;&nbsp; <a href="<?php echo OP_DOMAIN;?>contract/shenghe/<?php echo $value['cid']?>" target="navtab" title="合同审核">审核</a>
	                <?php }else{?>
	                	<?php if($uid==1 ||$uid==27){?>
	                	&nbsp;&nbsp;|&nbsp;&nbsp; <a href="<?php echo OP_DOMAIN;?>contract/shengheBack/<?php echo $value['cid']?>" target="ajaxTodo" title="您真的要审核退回吗?">审核退回</a>
	                	<?php }?>
	                <?php }?>
                <?php }?>
                </td>
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
<?php 
function diff_days($start, $end){
    list($a_year, $a_month, $a_day) = explode('-', $start);
    list($b_year, $b_month, $b_day) = explode('-', $end);
    $a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
    $b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
    return abs(($a_new-$b_new)/86400) + 1;
}
?>

