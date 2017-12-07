<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>userinfomanage/getProductDetails/<?php echo isset($uid) ? $uid : ''; ?>">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($searchtrxid)){?>
     <input type="hidden" name="searchtrxid" value="<?php echo $searchtrxid; ?>" />
     <input type="hidden" value="search_" name="op">
     <?php }?>
     <?php if(isset($searchpname)){?>
     <input type="hidden" name="searchpname" value="<?php echo $searchpname; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/userinfomanage/getProductDetails/<?php echo isset($uid) ? $uid : ''; ?>" method="post">
            <li><span>订单号</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索的订单号') this.value='';" onblur="if(this.value=='') this.value='请输入搜索的订单号';" value="<?php echo isset($searchtrxid)?$searchtrxid:'请输入搜索的订单号'?>"  id="searchtrxid" name="searchtrxid"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <li class="line">line</li>
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/userinfomanage/getProductDetails/<?php echo isset($uid) ? $uid : ''; ?>" method="post">
            <li><span>产品名称</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入查询产品名字') this.value='';" onblur="if(this.value=='') this.value='请输入查询产品名字';" value="<?php echo isset($searchpname)?$searchpname:'请输入查询产品名字'?>"  id="searchpname" name="searchpname"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <li class="line">line</li>
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/userinfomanage/getProductDetails/<?php echo isset($uid) ? $uid : ''; ?>" method="post">
            <li><span>还款状态</span></li>
            <li>
              <select class="combox" name="searchstatus">
				<option value="1">全部</option>
				<option value="2">未还款</option>
				<option value="3">已还款</option>
			</select>
            </li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <li class="line">line</li>
            <!--  
            <li><span>购买时间：</span></li>
            <li><input type="text " class="date" onfocus="if(this.value=='请输入查询产品名字') this.value='';" onblur="if(this.value=='') this.value='请输入查询产品名字';" value="<?php echo isset($searchpname)?$searchpname:'请输入购买开始时间'?>"  id="searchpname" name="searchpname">&nbsp;&nbsp;至</li>
            <li><input type="text" class="date" onfocus="if(this.value=='请输入查询产品名字') this.value='';" onblur="if(this.value=='') this.value='请输入查询产品名字';" value="<?php echo isset($searchpname)?$searchpname:'请输入购买结束时间'?>"  id="searchpname" name="searchpname"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            -->
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="6%">用户id</th>
                <th width="6%">订单号</th>
                <th width="8%">用户姓名</th>
<!--                 <th width="6%">手机号码</th> -->
                <th width="6%">产品名称</th>
                <th width="6%">债务利率</th>
                <th width="6%">客户起息日</th>
                <th width="6%">截止日期</th>
                <th width="6%">客户期限</th>
                <th width="7%">购买金额</th>
                <th width="6%">利息</th>
                <th width="6%">还款本息</th>
<!--                 <th width="7%">昨日收益</th> -->
                <th width="6%">购买时间</th>
                <th width="7%">还款时间</th>
                <th width="8%">交易状态</th>
            </tr>
        </thead>
        <tbody>
         <?php $oldname = ''; 
         ?>
        <?php if(!empty($userproduct_list)):?>
            <?php foreach($userproduct_list AS $key=>$value):?>
            <?php switch ($value['status']){
                case 0: $status = '未还款';break;
                case 1: $status = '已还款';break;
                default: $status = '未还款'; break;
               }
               ?>
            <tr>
                <td><?php echo $value['uid'];?></td>
                <td><?php echo $value['trxId'];?></td>
                <td><?php echo $value['realname'];?></td>
                <!--  <td><?php echo $value['phone'];?></td>-->
                <td><?php echo $value['pname'];?></td>
                <td><?php echo $value['income'];?></td>
                <td><?php echo $value['uistime'];?></td>
                <td><?php echo $value['uietime'];?></td>
                <td><?php echo $value['day'];?></td>
                <td><?php echo $value['money'];?></td>
                <td><?php echo round($value['profit'],2);?></td>
                <td><?php echo round($value['principal'],2);?></td>
                <!-- <td><?php echo round($value['yest_profit'],2);?></td> -->
                <td><?php echo date('Y-m-d H:i:s',$value['buytime'])?></td>
                <td><?php echo ($value['repaytime']!=0) ? date('Y-m-d',$value['repaytime']) : "---"?></td>
                <td><?php echo $status ?></td>
                
            </tr>
            <?php endforeach;?> 
		<?php endif;?>
		     <tr style="display:<?php if(empty($money)){echo 'none';}?>">
    		  <td>合计：</td>
    		  <td>已投资总额:<?php echo $money;?></td><td>已还款:<?php echo $repaymoney;?></td>
    		 <tr>
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


