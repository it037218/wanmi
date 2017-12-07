<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/stockproduct">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($searchnumber)){?>
     <input type="hidden" name="searchnumber" value="<?php echo $searchnumber; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>    
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
        <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/stockproduct" method="post">
            <li><span>是否采购</span></li>
            <li><input type="checkbox" name="is_stock" value="1"></li>
            <li><span>合同编号</span></li>
             <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchnumber)?$searchnumber:'请输入搜索内容'?>"  id="searchnumber" name="searchnumber"></li>
             <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
             </form>
            <li class="line">line</li>
            
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="18%">合同编号</th>
                <th width="13%">合作方名称</th>
                <th width="7%">总金额</th>
                <th width="8%">定期使用金额</th>
                <th width="8%">活期使用金额</th>
                <th width="7%">库存金额</th>
                <th width="5%">回款利率</th>
                <th width="5%">期限</th>
                <th width="8%">定期产品状态</th>
                <th width="7%">截止日期</th>
                <th width="8%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <?php 
               $stock_money = isset($count_stock_money[$value['cid']]) ? $count_stock_money[$value['cid']] : 0;
               //$stockmoney = isset($stock_produc[$value['cid']]['stockmoney']) ?$stock_produc[$value['cid']]['stockmoney'] : '0';
               $shengyu_money = $value['con_money']-$value['money'];
            ?>
                <tr>
                <td><?php echo $value['con_number'];?></td>
                <td><?php echo $value['corname'];?></td>
                <td><?php echo $value['con_money'];?></td>
                <td><?php if(($value['money']-$stock_money)<0){echo "0";}else{echo $value['money']-$stock_money;}?></td>
                <td><?php echo $stock_money;?></td>
                <td><?php echo $shengyu_money;?></td>
                <td><?php echo $value['con_income'];?></td>
                <td><?php echo diff_days($value['interesttime'], $value['repaymenttime']) ;?>天</td>
                <td><?php
                            $able_stock=true;
                            if(isset($rtnstatus[$value['cid']])){
                                foreach ($rtnstatus[$value['cid']] as $val){
                                    echo $val.'未下架<br/>';
                                    $able_stock=false;
                                }    
                            }else{
                                echo "停售";
                            }
                            
                    ?>
                </td>
                <td><?php echo $value['repaymenttime']?></td>
                <td>
                <a href="<?php echo OP_DOMAIN?>/stockproduct/showproduct/<?php echo $value['cid'];?>" target="dialog" title="售罄">查看</a>
                   <?php if($shengyu_money != 0 && $able_stock){ ?>
                   <?php if($editable==1){?>
 				   &nbsp;&nbsp;|&nbsp;&nbsp;
 				   <a href="<?php echo OP_DOMAIN;?>/stockproduct/StockLongmoney/<?php echo $value['cid'];?>/<?php echo $shengyu_money?>/<?php echo $value['repaymenttime']?>" target="dialog" title="采购">采购</a>
 				   <?php  } ?>
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
    return abs(($b_new-$a_new)/86400+1);
}
?>
