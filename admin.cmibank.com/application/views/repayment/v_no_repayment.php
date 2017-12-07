<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>repayment/no_repayment">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($searchcorname)){?>
     <input type="hidden" name="searchcorname" value="<?php echo $searchcorname; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($searchcon_number)){?>
     <input type="hidden" name="searchcon_number" value="<?php echo $searchcon_number; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     
     <?php if(isset($searchpname)){?>
     <input type="hidden" name="searchpname" value="<?php echo $searchpname; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     
     <?php if(isset($startcietime)){?>
     <input type="hidden" name="startcietime" value="<?php echo $startcietime; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     
     <?php if(isset($endcietime)){?>
     <input type="hidden" name="endcietime" value="<?php echo $endcietime; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
</form>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN; ?>repayment/no_repayment" method="post">
            <li><span>公司名称</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchcorname)?$searchcorname:'请输入搜索内容'?>"  id="searchcorname" name="searchcorname"></li>
            <li><span>合同编号</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchcon_number)?$searchcon_number:'请输入搜索内容'?>"  id="searchcon_number" name="searchcon_number"></li>
            <li><span>产品名称</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchpname)?$searchpname:'请输入搜索内容'?>"  id="searchpname" name="searchpname"></li>
            <li><span>截止日期</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入开始日期') this.value='';" onblur="if(this.value=='') this.value='请输入开始日期';" value="<?php echo isset($startcietime)?$startcietime:'请输入开始日期'?>"  id="startcietime" name="startcietime" class="date">&nbsp;&nbsp;至</li>
            <li><input type="text" onfocus="if(this.value=='请输入结束日期') this.value='';" onblur="if(this.value=='') this.value='请输入结束日期';" value="<?php echo isset($endcietime)?$endcietime:'请输入结束日期'?>"  id="endcietime" name="endcietime" class="date"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="10%">公司名称</th>
                <th width="11%">合同编号</th>
                <th width="4%">项目名称</th>
                <th width="3%">产品ID</th>
                <th width="7%">产品名称</th>
                <th width="3%">收益率</th>
                <th width="3%">回款收益</th>
                <th width="5%">期限</th>
                <th width="6%">起息日期</th>
                <th width="6%">截止日期</th>
                <th width="4%">募集资金</th>
                <th width="4%">状态</th>
                <th width="15%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <?php switch ($value['status']){
                case 0: $status = '末上架';break;
                case 1: $status = '已上架';break;
                case 2: $status = '已下架';break;
                case 3: $status = '售罄';break;
                case 4: $status = '已打款';break;
                case 5: $status = '已回款';break;
                case 6: $status = '已还款';break;
                default: $status = '末知状态'; break;
            }?>
                <tr>
                <td><?php echo $contract_list[$value['cid']]['corname'];?></td>
                <td><?php echo $contract_list[$value['cid']]['con_number'];?></td>
                <td><?php echo $ptype_list[$value['ptid']];?></td>
                <td><?php echo $value['pid'] ;?></td>
                <td><?php echo $value['pname'] ;?></td>
                <td><?php echo $value['income'] ;?></td>
                <td><?php echo $contract_list[$value['cid']]['con_income'];?></td>
                <td><?php echo diff_days($value['uistime'], $value['uietime']) ;?></td>
                <td><?php echo $value['uistime'] ;?></td>
                <td><?php echo $value['uietime'] ;?></td>
                <td><?php echo $value['money'] ;?></td>
                <td><?php echo $status ?></td>
                <td>
                <?php if($value['status'] >= 5 && $value['uietime'] < date('Y-m-d')){?>
                    <a href="<?php echo OP_DOMAIN;?>product/detail? &pid=<?php echo $value['pid'];?>" target="navtab" title="查看">查看</a>
    				&nbsp;&nbsp;|&nbsp;&nbsp;
                    <?php if($value['status'] == 5 && $value['uietime'] < date('Y-m-d')){?>
                    <?php if($editable==1){?>
                    <a href="<?php echo OP_DOMAIN;?>repayment/create_repayment_order/<?php echo $value['pid']?>" target="ajaxtodo" >还款</a>
                    <?php }?>
                    <?php }else if($value['uietime'] < date('Y-m-d')){?>
                        <?php if($value['repayment_status'] == 0){?>
                        <?php if($editable==1){?>
                            <a href="<?php echo OP_DOMAIN;?>repayment/shenghe/<?php echo $value['pid']?>" target="ajaxtodo" >还款审核</a>
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                            <?php }?>
                        <?php }else if($value['repayment_status'] == 1){?>
                                                                            还款中
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                        <?php }else{?>
                                                                            已还款
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                        <?php } ?>
                    <a href="<?php echo OP_DOMAIN;?>repayment/productUserList/<?php echo $value['pid']?>/<?php echo strtotime($value['uietime'])+86400?>" target="navtab" >还款对账</a>
                    <?php }?>
                <?php }?>
                </td>
                </tr>
            <?php endforeach;?>
		<?php endif;?>
		<?php if(!empty($rtnproduct)){?>
		        <tr>
		          <td>统计共销售:<?php echo $rtnproduct['count_sellmoney'];?></td>
		          <?php foreach ($rtnproduct as $key=>$val){ if($key != 'count_sellmoney'){?>
		          <td><?php echo $key;?>销售：<?php echo $val['sellmoney']?></td>
		          <?php }} ?>
		       </tr>  
		      <?php }?>
		</tbody>
	</table>
	    <div class="panelBar" style="display:<?php echo isset($none) ? $none : 'black';?>">
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

