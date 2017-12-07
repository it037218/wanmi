<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN;?>/remit">    
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
</form>

<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN; ?>remit" method="post">
            <li><span>公司名称</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchcorname)?$searchcorname:'请输入搜索内容'?>"  id="searchcorname" name="searchcorname"></li>
            <li><span>合同编号</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchcon_number)?$searchcon_number:'请输入搜索内容'?>"  id="searchcon_number" name="searchcon_number"></li>
            <li><span>产品名称</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchpname)?$searchpname:'请输入搜索内容'?>"  id="searchpname" name="searchpname"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <li class="line">line</li>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="15%">公司名称</th>
                <th width="10%">合同编号</th>
                <th width="8%">产品名称</th>
                <th width="4%">募集资金</th>
                <th width="4%">已售资金</th>
                <th width="3%">年化收益</th>
                <th width="6%">合作方起息日</th>
                <th width="6%">合作方截止日</th>
                <th width="4%">审核状态</th>
                <th width="8%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key => $value):?>
                <tr>
                <td><?php echo $contract[$value['cid']]['corname'];?></td>
                <td><?php echo $contract[$value['cid']]['con_number'];?></td>
                <td><?php echo $value['pname'];?></td>
                <td><?php echo $value['money'];?></td>
                <td><?php echo $value['sellmoney'];?></td>
                <td><?php echo $value['income'];?></td>
                <td><?php echo $value['cistime'];?></td>
                <td><?php echo $value['cietime'];?></td>
                <td>----</td>
                <td>
				    <a href="<?php echo OP_DOMAIN?>/remit/showremit/<?php echo $value['pid'];?>" target="navtab" title="给合作方打款">
				                 查看
				    </a>
				    <?php if($editable==1){?>
				    &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
				    <?php if(empty($value['remitid'])){?>
    				    <a href="<?php echo OP_DOMAIN?>remit/doremit/<?php echo $value['pid'];?>/today" target="navtab" title="给合作方打款">
    				                   给合作方打款
    				    </a>
				    <?php }else{?>
				        <a href="<?php echo OP_DOMAIN?>remit/editremit/<?php echo $value['pid'];?>" target="navtab" title="给合作方打款">
				                              编辑打款凭条
				        </a>
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
