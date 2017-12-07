<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/longproductbuyinfo">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($searchpname)){?>
     <input type="hidden" name="searchpname" value="<?php echo $searchpname; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($searchstart)){?>
     <input type="hidden" name="searchstart" value="<?php echo $searchstart; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($searchend)){?>
     <input type="hidden" name="searchend" value="<?php echo $searchend; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/longproductbuyinfo" method="post">
            <li><span>产品名称</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchpname)?$searchpname:'请输入搜索内容'?>"  id="searchpname" name="searchpname"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/longproductbuyinfo" method="post">
            <li><span>起息日期</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入开始日期') this.value='';" onblur="if(this.value=='') this.value='请输入开始日期';" value="<?php echo isset($searchstart)?$searchstart:'请输入开始日期'?>"  id="searchstart" name="searchstart" class="date">&nbsp;&nbsp;至</li>
            <li><input type="text" onfocus="if(this.value=='请输入结束日期') this.value='';" onblur="if(this.value=='') this.value='请输入结束日期';" value="<?php echo isset($searchend)?$searchend:'请输入结束日期'?>"  id="searchend" name="searchend" class="date"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <li class="line">line</li>
            
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="6%">产品id</th>
                <th width="6%">产品名称</th>
                <th width="6%">募集资金</th>
                <th width="6%">产品利率</th>
                <th width="6%">已售金额</th>
                <th width="6%">购买用户数</th>
                <th width="6%">产品状态</th>
                <th width="6%">起息时间</th>
                <th width="6%">购买详情</th>

            </tr>
        </thead>
        <tbody>
        <!-- --- $banklist['01050000']['name']);-->
         <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <?php switch ($value['status']){
                case 0: $status = '末上架';break;
                case 1: $status = '已上架';break;
                case 2: $status = '已下架';break;
                case 3: $status = '售罄';break;
                case 4: $status = '停售';break;
                case 5: $status = '已回款';break;
                case 6: $status = '已还款';break;
                default: $status = '末知状态'; break;
            }?>
            <tr>
               <td><?php echo $value['pid'];?></td>
               <td><?php echo $value['pname'];?></td>
               <td><?php echo $value['money'];?></td>
               <td><?php echo $value['income'];?></td>
               <td><?php echo $value['sellmoney'];?></td>
               <td><?php echo $value['count_people'];?></td>
               <td><?php echo $status;?></td>
               <td><?php 
                    if(empty($value['online_time'])){
                        echo $value['odate'];
                    }else{
                        echo date('Y-m-d',strtotime($value['online_time']));
                    }
                   ?></td>
               <td>
                <a href="<?php echo OP_DOMAIN?>/longproductbuyinfo/getlongproductbuyinfoBypid/<?php echo $value['pid']?>" target="navtab" title="购买用户详情">查看</a>
               </td>
                
            </tr>
            <?php endforeach;?>
		<?php endif;?>
		<tr>
		<?php if(!empty($rtnlongmoney)){?>
		  <td>累计活期已售金额：</td>
		  <td><?php echo number_format($rtnlongmoney)?></td>
		<?php }?>
		</tr>
		</tbody>
	</table>
	<div class="panelBar" style="display:<?php echo isset($none) ? $none : 'block'?>">
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


