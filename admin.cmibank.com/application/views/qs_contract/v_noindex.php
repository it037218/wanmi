<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/qs_contract">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($searchnumber)){?>
     <input type="hidden" name="searchnumber" value="<?php echo $searchnumber; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     
     <?php if(isset($star)){?>
     <input type="hidden" name="star" value="<?php echo $star; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     
     <?php if(isset($end)){?>
     <input type="hidden" name="end" value="<?php echo $end; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
       
     <?php if(isset($is_null)){?>
     <input type="hidden" name="is_null" value="<?php echo $is_null; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?> 
     <?php if(isset($is_upload)){?>
     <input type="hidden" name="is_upload" value="<?php echo $is_upload; ?>" />
     <input type="hidden" value="search_dk" name="op">
     <?php }?> 
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/qs_contract" method="post">
                <li><span>库存为零</span></li>
                <li><input type="checkbox" name="is_null" value="1"></li>
                <li><span>公司名称</span></li>
                <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchcorname)?$searchcorname:'请输入搜索内容'?>"  id="searchcorname" name="searchcorname"></li>
                <li class="line">line</li>
                <li><span>合同编号</span></li>
                <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchnumber)?$searchnumber:'请输入搜索内容'?>"  id="searchnumber" name="searchnumber"></li>
                <li><span>起息日期</</span></li>
                <li><input type="text" onfocus="if(this.value=='请输入开始日期') this.value='';" onblur="if(this.value=='') this.value='请输入开始日期';" value="<?php echo isset($star)?$star:'请输入开始日期'?>"  id="star" name="star" class="date">&nbsp;&nbsp;至</li>
                <li><input type="text" onfocus="if(this.value=='请输入结束日期') this.value='';" onblur="if(this.value=='') this.value='请输入结束日期';" value="<?php echo isset($end)?$end:'请输入结束日期'?>"  id="end" name="end" class="date"></li>
                <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/qs_contract" method="post">
                <li><span>没有打款</span></li>
                <li><input type="checkbox" name="is_upload" value="1" <?php if(isset($is_upload)){ echo "checked=checked";}?>></li>
                <li><input type="hidden" value="search_dk" name="op"><button type="submit" >检索</button></li>
            </form>
            <li class="line">line</li>
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
                <td><?php echo $value['money'];?></td>
                <td><?php echo $stock_money;?></td>
                <td><?php echo $shengyu_money;?></td>
                <td><?php echo $value['con_income'];?></td>
                <td><?php echo diff_days($value['interesttime'], $value['repaymenttime']);?>天</td>
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
				<?php if(isset($rtnstatus[$value['cid']])):?>
				    <a href="<?php echo OP_DOMAIN?>/qs_contract/downtoline/<?php echo $value['cid'];?>" target="ajaxTodo" title="是否确定售罄">停止销售</a>
 				&nbsp;&nbsp;|&nbsp;&nbsp;
				<?php endif;?>
				<?php if($value['status'] == 2){?>
				<a href="<?php echo OP_DOMAIN?>/qs_contract/uptoline/<?php echo $value['cid'];?>" target="ajaxTodo" title="确认开启销售">开启</a>
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
