<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>userinfomanage/getproductprofitDetails/<?php echo isset($uid) ? $uid : ''; ?>">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($searchstart)){?>
     <input type="hidden" name="searchstart" value="<?php echo $searchstart; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($searchpname)){?>
     <input type="hidden" name="searchend" value="<?php echo $searchend; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <!--  
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/userinfomanage/getproductprofitDetails/<?php echo isset($uid) ? $uid : ''; ?>" method="post">
            <li><span>产品名称</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入查询产品名字') this.value='';" onblur="if(this.value=='') this.value='请输入查询产品名字';" value="<?php echo isset($searchpname)?$searchpname:'请输入查询产品名字'?>"  id="searchpname" name="searchpname"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            -->
            <li class="line">line</li>
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/userinfomanage/getproductprofitDetails/<?php echo isset($uid) ? $uid : ''; ?>" method="post">
            <li><span>结算利息时间：</span></li>
            <li><input type="text " class="date" onfocus="if(this.value=='请输入开始时间') this.value='';" onblur="if(this.value=='') this.value='请输入开始时间';" value="<?php echo isset($searchstar)?$searchstart:'请输入开始时间'?>"  id="searchstart" name="searchstart">&nbsp;&nbsp;至</li>
            <li><input type="text" class="date" onfocus="if(this.value=='请输入结束时间') this.value='';" onblur="if(this.value=='') this.value='请输入结束时间';" value="<?php echo isset($searchend)?$searchend:'请输入结束时间'?>"  id="searchend" name="searchend"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="6%">用户id</th>
                <th width="6%">时间</th>
                <th width="6%">用户姓名</th>
                <th width="6%">当前项目</th>
                <th width="6%">债务利率</th>
                <th width="6%">客户起息日</th>
                <th width="6%">截止日期</th>
                <th width="6%">购买金额</th>
                <th width="6%">累计收益</th>

            </tr>
        </thead>
        <tbody>
         <?php $oldname = ''; 
         $i=0
         ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <tr>
                <td><?php echo $value['uid'];?></td>
                <td><?php echo $key;?></td> 
                <td><?php echo $value['realname'];?></td>
                <td><?php echo $value['num'];?></td>
                <td><?php echo "-------";?></td>
                <td><?php echo "-------";?></td>
                <td><?php echo "-------";?></td>
                <td><?php echo $value['money'];?></td>
                <td><?php echo $value['count_profit']?></td>
 
                <?php foreach ($value['product_list'] as $key=>$val):?>
                <tr> 
                <td colspan="3"></td>
                <td><?php echo $val['ue_id'].'号体验金'?></td>
                <td><?php echo $val['income']?></td>
                <td><?php echo $val['uistime']?></td>
                <td><?php echo $val['uietime']?></td>
                <td><?php echo $val['money']?></td>
                <td><?php echo $val['profit']?></td>
                </tr>
                <?php endforeach;?>   
            </tr>
            <?php endforeach;?> 
		<?php endif;?>
            <tr>
            <td>当前收益:<?php echo $nowProfit?></td>
            <td>累计体验金收益:<?php echo $countexprofit;?></td>
            <td>昨日收益<?php echo $yestexprofit?></td>
            </tr>
		</tbody>
	</table>
		<table>
    		
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


