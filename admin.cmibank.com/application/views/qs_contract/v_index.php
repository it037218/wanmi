<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/qs_contract">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($searchnumber)){?>
     <input type="hidden" name="searchnumber" value="<?php echo $searchnumber; ?>" />
     <?php }?>
     <?php if(isset($searchcorname)){?>
     <input type="hidden" name="searchcorname" value="<?php echo $searchcorname; ?>" />
     <?php }?>
     <?php if(isset($star)){?>
     <input type="hidden" name="star" value="<?php echo $star; ?>" />
     <?php }?>
     
     <?php if(isset($end)){?>
     <input type="hidden" name="end" value="<?php echo $end; ?>" />
     <?php }?>
       
     <?php if(isset($is_null)){?>
     <input type="hidden" name="is_null" value="<?php echo $is_null; ?>" />
     <?php }?> 
     <?php if(isset($weidakuang)){?>
     <input type="hidden" name="is_weidakuang" value="<?php echo $weidakuang; ?>" />
     <?php }?> 
     <input type="hidden" value="search" name="op">
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/qs_contract" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					公司名称：<input type="text" value="<?php echo isset($searchcorname)?$searchcorname:''?>"  id="searchcorname" name="searchcorname">
				</td>
				<td>
					合同编号：<input type="text" value="<?php echo isset($searchnumber)?$searchnumber:''?>"  id="searchnumber" name="searchnumber">
				</td>
				<td>
					<input type="checkbox" name="is_null" value="1" <?php echo isset($is_null) ? 'checked' : "";?>>库存为零
				</td>
			</tr>
			<tr>
				<td colspan="2">
					起息日期：<input type="text" value="<?php echo isset($star)?$star:''?>"  id="star" name="star" class="date">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;至&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" value="<?php echo isset($end)?$end:''?>"  id="end" name="end" class="date">
				</td>
				<td>
					<input type="checkbox" name="is_weidakuang" value="1" <?php echo isset($weidakuang) ? 'checked' : "";?>>没有打款
				</td>
				<td>
					<input type="hidden" value="search" name="op"><button type="submit" >检索</button>
				</td>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <li><span>合同金额总计：</span></li>
            <li style="padding-top:7px"><?php echo isset($total) ? $total : 0;?>元</li>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="120">
        <thead>
            <tr>
                <th width="20%">公司名称</th>
                <th width="14%">合同编号</th>
                <th width="5%">合同金额</th>
                <th width="5%">定期使用金额</th>
                <th width="5%">活期使用金额</th>
                <th width="5%">库存金额</th>
                <th width="3%">回款利率</th>
                <th width="3%">期限</th>
                <th width="7%">开始时间</th>
                <th width="7%">结束时间</th>
                <th width="7%">定期产品状态</th>
                <th width="10%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <?php 
               $stock_money = isset($count_stock_money[$value['cid']]) ? $count_stock_money[$value['cid']] : 0;
               $shengyu_money = $value['con_money']-$value['money'];
            ?>
                <tr>
                <td><?php echo $value['corname'];?></td>
                <td><a href="<?php echo OP_DOMAIN?>/qs_contract/showproduct/<?php echo $value['cid'];?>" target="navtab" title="产品详情" >
                <?php echo $value['con_number'];?>
                </a>
                </td>
                <td><?php echo $value['con_money'];?></td>
                <td><?php if(($value['money']-$stock_money)<0){echo "0";}else{echo $value['money']-$stock_money;}?></td>
                <td><?php echo $stock_money;?></td>
                <td><?php echo $shengyu_money;?></td>
                <td><?php echo $value['con_income'];?></td>
                <td><?php echo diff_days($value['interesttime'], $value['repaymenttime']);?></td>
                <td><?php echo $value['interesttime'];?></td>
                <td><?php echo $value['repaymenttime'];?></td>
                <td><?php
                		if($shengyu_money==$value['con_money']){
                			echo "新合同";
                		}else{
	                        if(isset($rtnstatus[$value['cid']])){
	                            foreach ($rtnstatus[$value['cid']] as $val){
	                                echo $val.'未下架<br/>';
	                            }    
	                        }else{
	                            echo "无在售产品";
	                        }
                		}
                    ?>
                </td>
                <td>
                <?php if($editable==1){?>
				<?php if(isset($rtnstatus[$value['cid']])):?>
				    <a href="<?php echo OP_DOMAIN?>/qs_contract/downtoline/<?php echo $value['cid'];?>" target="ajaxTodo" title="是否确定售罄">停止销售</a>
				<?php endif;?>
				<?php if($value['status'] == 2){?>
				<a href="<?php echo OP_DOMAIN?>/qs_contract/uptoline/<?php echo $value['cid'];?>" target="ajaxTodo" title="确认开启销售">开启</a>
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
    return abs(($b_new-$a_new)/86400+1);
}
?>
