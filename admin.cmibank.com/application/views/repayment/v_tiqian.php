<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/tiqianrepayment">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
     <?php if(isset($corname)){?>
     <input type="hidden" name="corname" value="<?php echo $corname; ?>" />
     <?php }?> 
     <?php if(isset($connumber)){?>
     <input type="hidden" name="connumber" value="<?php echo $connumber; ?>" />
     <?php }?> 
     <input type="hidden" value="search" name="op">
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/tiqianrepayment" method="post">
                <li><span>公司名称</span></li>
                <li><input type="text"  value="<?php echo isset($corname)?$corname:''?>"  id="corname" name="corname"></li>
                <li class="line">line</li>
                <li><span>合同编号</span></li>
                <li><input type="text"  value="<?php echo isset($connumber)?$connumber:''?>"  id="connumber" name="connumber"></li>
                <input type="hidden" value="search" name="op">
                <li><button type="submit" >检索</button></li>
            </form>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="8%">公司名称</th>
                <th width="8%">合同编号</th>
                <th width="8%">合同金额</th>
                <th width="8%">定期使用金额</th>
                <th width="8%">活期使用金额</th>
                <th width="8%">库存金额</th>
                <th width="8%">回款利率</th>
                <th width="8%">期限</th>
                <th width="8%">开始时间</th>
                <th width="8%">结束时间</th>
                <th width="8%">定期产品状态</th>
                <th width="20%">操作</th>
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
                        if(isset($rtnstatus[$value['cid']])){
                            foreach ($rtnstatus[$value['cid']] as $val){
                                echo $val.'未下架<br/>';
                            }    
                        }else{
                            echo "无再售产品";
                        }
                    ?>
                </td>
                <td>
                <?php if($editable==1){?>
					    <a href="<?php echo OP_DOMAIN?>/tiqianrepayment/editcontract/<?php echo $value['cid'];?>" target="navtab" title="设置提前还款">提前还款</a>
				<?php }?>
                </td>
                </tr>
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
	<div class="panelBar" style="<?php if(!empty($is_null) && $is_null == 1){echo 'display:none';}else{echo 'display:block';}?>";>
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
<?php 
function diff_days($start, $end){
    list($a_year, $a_month, $a_day) = explode('-', $start);
    list($b_year, $b_month, $b_day) = explode('-', $end);
    $a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
    $b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
    return abs(($b_new-$a_new)/86400+1);
}
?>
