<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>luckmoney_list/">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>

<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
        <?php if($editable==1){?>
            <li><a title="添加红包" target="navtab" href="<?php echo OP_DOMAIN; ?>luckmoney_list/addluckmoney" class="icon"><span>添加红包</span></a></li>
            <li class="line">line</li><?php }?>
        </ul>
    </div>
    <table class="list" width="100%" layoutH="55">
        <thead>
        <tr>
			<th width="5%">序号</th>
            <th width="7%">红包名称</th>
			<th width="7%">红包金额</th>
			<th width="7%">用户群体</th>
			<th width="10%">红包权重</th>
			<th width="8%">预告时间</th>
			<th width="8%">开启日期及时间</th>
			<!--<th width="8%">结束时间</th>-->
			<th width="7%">文字显示</th>
			<th width="7%">获奖红包祝福语</th>
			<th width="7%">未获奖红包祝福语</th>
			<th width="7%">状态</th>
			<th width="20%">操作</th>
		</tr>
		</thead>
		<?php if(!empty($list)):?>
		<?php foreach($list AS $key=>$value):?>
		<tr>
            <td><?php echo $value['lmid'];?></td>
			<td><?php echo $value['lname'];?></td>
			<td><?php echo $value['lmoney'];?></td>
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
			<td><?php echo date("Y-m-d H:i",$value['yugaotime']);?></td>
			<td><?php echo date("Y-m-d H:i",$value['lstime']);?></td>
			<!--  <td><?php if($value['etime'] == 0){ echo "--";}else{ echo date("Y-m-d H:i",$value['etime']);}?></td>-->
			<td><?php echo $value['ltext'];?></td>
			<td><?php echo $value['bless_text'];?></td>
			<td><?php echo $value['nobless_text'];?></td>
			<td>
			<?php 
			if($value['etime'] != 0){
			    echo "结束";
			}else{
			    switch($value['status']){
			        case '0':
			            echo '未发布';break;
			        case '1':
			            echo '已发布';break;
			        default: '有误';
			    } 
			}
			?>
			</td>
			<td>
			<?php if($editable==1){?>
			 <a href="<?php echo OP_DOMAIN;?>luckmoney_list/editLuckmoney/<?php echo $value['lmid'];?>" title="红包编辑" target="navtab">编辑</a>
			 <?php if($value['status'] == 0){?>
			 &nbsp;|&nbsp;
			 <a href="<?php echo OP_DOMAIN;?>luckmoney_list/uptoline/<?php echo $value['lmid'];?>" target="ajaxTodo" title="你真的要发布么？">发布</a>
			 <?php }?>
			 &nbsp;|&nbsp;
			 <a href="<?php echo OP_DOMAIN;?>luckmoney_list/deleteluckmoney/<?php echo $value['lmid'];?>/<?php echo $value['yugaotime'];?>" target="ajaxTodo" title="你真的删除？">你真的删除</a>
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
