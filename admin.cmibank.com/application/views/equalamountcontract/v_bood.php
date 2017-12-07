<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/contract/bond">
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
     <?php if(isset($searchinteresttimet)){?>
     <input type="hidden" name="searchinteresttimet" value="<?php echo $searchinteresttimet; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($searchrepaymenttime)){?>
     <input type="hidden" name="searchrepaymenttime" value="<?php echo $searchrepaymenttime; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/contract/bond" method="post">
            <li><span>公司名称</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchcorname)?$searchcorname:'请输入搜索内容'?>"  id="searchcorname" name="searchcorname"></li>
            <li><span>合同编号</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchcon_number)?$searchcon_number:'请输入搜索内容'?>"  id="searchcon_number" name="searchcon_number"></li>
            <li><span>合同还款日</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入开始日期') this.value='';" onblur="if(this.value=='') this.value='请输入开始日期';" value="<?php echo isset($searchinteresttimet)?$searchinteresttimet:'请输入开始日期'?>"  id="searchinteresttimet" name="searchinteresttimet" class="date">&nbsp;&nbsp;至</li>
            <li><input type="text" onfocus="if(this.value=='请输入结束日期') this.value='';" onblur="if(this.value=='') this.value='请输入结束日期';" value="<?php echo isset($searchrepaymenttime)?$searchrepaymenttime:'请输入结束日期'?>"  id="searchrepaymenttime" name="searchrepaymenttime" class="date"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="12%">公司名称</th>
                <th width="10%">合同编号</th>
                <th width="7%">合同金额</th>
                <th width="7%">保证金比率</th>
                <th width="7%">保证金金额</th>
                <th width="6%">应打款金额</th>
                <th width="6%">已经打款金额</th>
                <th width="5%">实际金额</th>
                <th width="8%">起息时间</th>
                <th width="8%">截止时间</th>
                <th width="4%">期限</th>
                <th width="5%">状态</th>
                <th width="20%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                <td><?php echo $value['corname'] ;?></td>
                <td><?php echo $value['con_number'] ?></td>
                <td><?php echo $value['real_money'] ;?></td>
                <td><?php echo $value['con_bzjbl'];?>%</td>
                <td><?php echo $value['real_money']*$value['con_bzjbl']/100 ;?></td>
                <td><?php echo $value['real_money']-($value['real_money']*$value['con_bzjbl']/100) ;?></td>
                <td><?php echo $value['con_dkje'] ;?></td>
                <td><?php echo $value['real_money']?></td>
                <td><?php echo $value['interesttime']?></td>
                <td><?php echo $value['repaymenttime']?></td>
                <td><?php echo diff_days($value['interesttime'],$value['repaymenttime']).'天'; ?></td>
                <td><?php 
                    if($value['con_dkje'] == $value['real_money']){
                        echo "已打款";
                    }else{
                        echo "正在募集";
                    }
                ?></td>
                <td>
                 <a href="<?php echo OP_DOMAIN;?>contract/getBonddetail/<?php echo $value['cid']?>" target="dialog" title="查看">查看</a>
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

