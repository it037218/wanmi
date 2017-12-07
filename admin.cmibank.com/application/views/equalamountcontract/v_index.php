<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/equalamountcontract">
    <input type="hidden" name="pageNum" value="<?php echo isset($pageNum) ? $pageNum : 0 ; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo isset($numPerPage) ? $numPerPage : 0; ?>" />
    
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
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/equalamountcontract" method="post">
            <li><span>公司名称</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchcorname)?$searchcorname:'请输入搜索内容'?>"  id="searchcorname" name="searchcorname"></li>
            <li><span>合同编号</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchcon_number)?$searchcon_number:'请输入搜索内容'?>"  id="searchcon_number" name="searchcon_number"></li>
            <li><span>合同还款日</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入开始日期') this.value='';" onblur="if(this.value=='') this.value='请输入开始日期';" value="<?php echo isset($searchinteresttimet)?$searchinteresttimet:'请输入开始日期'?>"  id="searchinteresttimet" name="searchinteresttimet" class="date">&nbsp;&nbsp;至</li>
            <li><input type="text" onfocus="if(this.value=='请输入结束日期') this.value='';" onblur="if(this.value=='') this.value='请输入结束日期';" value="<?php echo isset($searchrepaymenttime)?$searchrepaymenttime:'请输入结束日期'?>"  id="searchrepaymenttime" name="searchrepaymenttime" class="date"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <li class="line">line</li>
            <li><a title="新建合同" href="<?php echo OP_DOMAIN;?>equalamountcontract/addequalamountcontract" target="navtab"  class="icon"><span>添加合同</span></a></li>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="12%">公司名称</th>
                <th width="12%">合同编号</th>
                <th width="7%">合同实际金额</th>
                <th width="8%">可售金额（+回库）</th>
                <th width="5%">下分金额</th>
                <th width="5%">合同利率</th>
                <th width="6%">合同起息时间</th>
                <th width="6%">合同还款时间</th>
                <th width="4%">合同期限</th>
                <th width="6%">每月还款日</th>
                <th width="5%">扫描件地址</th>
                <th width="10%">用户电子模板</th>
                <th width="25%">操作</th>
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
                <td><?php echo $value['con_money'] ;?></td>
                <td><?php echo $value['money'] ;?></td>
                <td><?php echo $value['con_income'] ;?>%</td>
                <td><?php echo $value['interesttime'];?></td>
                <td><?php echo $value['repaymenttime'];?></td>
                <td><?php echo diff_days($value['interesttime'],$value['repaymenttime']).'天';?></td>
                <td><?php echo $value['repaymentday'];?></td>
                <td><a href="<?php echo $value['object_img']; ?>" target="_bank">预览</a></td>
                <td><?php echo $usercontract[$value['ucid']]; ?></td>
                <td>
                <a href="<?php echo OP_DOMAIN;?>equalamountcontract/editequalamountcontract/<?php echo $value['cid'];?>" target="navtab" title="合同编辑">基本编辑</a>
                <?php if($value['money'] == 0){?>
                    |&nbsp;&nbsp;
                   <a href="<?php echo OP_DOMAIN;?>equalamountcontract/delequalamountcontract/<?php echo $value['cid']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
                <?php }?>
                </td>
                </tr>
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
	<?php if(isset($pageNum)){?>
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
    <?php }?>
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

