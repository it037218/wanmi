<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN?>/luckmoney_list/detailLuckMoney">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    
    <?php if(isset($searchlname)){?>
     <input type="hidden" name="searchlname" value="<?php echo $searchlname; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
</form>

<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/luckmoney_list/detailLuckMoney">
            <li><span>红包名称</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchlname)?$searchlname:'请输入搜索内容'?>"  id="searchlname" name="searchlname"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
        </ul>
    </div>
    <table class="list" width="100%" layoutH="55">
     <thead>
        <tr>
			<th width="8%">红包名称</th>
            <th width="8%">红包金额</th>
            <th width="6%">已经抢</th>
			<th width="8%">用户群体</th>
			<th width="10%">红包权重</th>
			<th width="10%">开启时间</th>
			<th width="10%">结束时间</th>
			<th width="10%">文字显示</th>
			<th width="10%">参与用户（人）</th>
			<th width="10%">实际抢到用户（人）</th>
			<th width="10%">操作</th>
		</tr>
		</thead>
		<?php if(!empty($list)):?>
		<?php foreach($list AS $key=>$value):?>
		<tr>
            <td><?php echo $value['lname']?></td>
			<td><?php echo $value['lmoney']?></td>
			<td><?php echo round($money[$value['lmid']])?></td>
			<td>
			 <?php switch($value['ltarget']){
        		case '0':
        		echo '全部用户';break;
        		case '1':
        		echo '投资用户';break;
        		case '2':
        		echo '定期用户';break;
        		default: '权重有误';
		     }?>
			</td>
			<td><?php echo $value['lweight1_money']."元&nbsp;&nbsp;&nbsp;&nbsp;".$value['lproportion1']."%<br/>".$value['lweight2_money']."元&nbsp;&nbsp;&nbsp;&nbsp;".$value['lproportion2']."%<br/>".$value['lweight3_money']."元&nbsp;&nbsp;&nbsp;&nbsp;".$value['lproportion3'].'%';?></td>
			<td><?php echo date('Y-m-d H:i:s',$value['lstime'])?></td>
			<td><?php if($value['etime'] == 0){ echo "红包正在线上";}else{ echo date("Y-m-d H:i",$value['etime']);}?></td>
			<td><?php echo $value['ltext'];?></td>
			<td><?php echo $JoinPeoples[$value['lmid']]?></td>
			<td><?php echo $peoples[$value['lmid']]?></td>
			<td>
			 <a href="<?php echo OP_DOMAIN;?>luckmoney_list/getAlreadyLuckMoney/<?php echo $value['lmid'];?>" title="查看用户红包明细" target="navtab">查看用户红包明细</a>
			 <?php if($editable==1){?>
			 <?php if($value['lmid'] == $dlmid){?>
			 &nbsp;&nbsp;|&nbsp;&nbsp;
			 <a href="<?php echo OP_DOMAIN;?>luckmoney_list/seehandle/<?php echo $value['lmid'];?>" title="你确定要设置为看手气？" target=ajaxTodo>设为看手气</a>
			 &nbsp;&nbsp;|&nbsp;&nbsp;
			 <a href="<?php echo OP_DOMAIN;?>luckmoney_list/downtoline/<?php echo $value['lmid'];?>" title="你确定要设置为下线？" target=ajaxTodo>下线</a>
			 <?php }?>
			 <?php }?>
			</td>
			
		</tr>
		<?php endforeach;?>
        <?php endif;?>
  
    
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
