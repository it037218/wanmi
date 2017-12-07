<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/userinfomanage/getExpmoneyLogById/<?php echo $uid?>">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />

</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/userinfomanage/getExpmoneyLogById/<?php echo $uid?>" method="post">
            <li><span>交易类型</span></li>
            <li>
                <select name="type">
                    <option value="0">全部</option>
                    <option value="1">收入</option>
                    <option value="2">支出</option>
                </select>
            </li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/userinfomanage/getExpmoneyLogById/<?php echo $uid?>" method="post">
            <li><span>时间</span></li>
            <li><input name="stime" readonly="true"  class="date" onfocus="if(this.value=='请选择开始时间') this.value='';" onblur="if(this.value=='') this.value='请选择开始时间';" value="<?php echo isset($stime)?date('Y-m-d',$stime):'请选择开始时间'?>"/>&nbsp;&nbsp;至</li>
            <li><input name="etime" readonly="true" class="date" onfocus="if(this.value=='请选择结束时间') this.value='';" onblur="if(this.value=='') this.value='请选择结束时间';" value="<?php echo isset($etime)?date('Y-m-d',$etime):'请选择结束时间'?>"/></li>
            <li><input type="hidden" value="search_bannertime" name="op"><button type="submit">检索</button></li>
            </form>
            <li class="line">line</li>
            
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="13%">用户id</th>
                <th width="13%">交易流水号</th>
                <th width="13%">日期</th>
                <th width="13%">类型</th>
                <th width="12%">收入</th>
                <th width="12%">支出</th>
                <th width="12%">可用体验金（元）</th>
                <th width="12%">在投使用体验金（元）</th>
                
                
            </tr>
        </thead>
        <tbody>
        <!-- --- $banklist['01050000']['name']);-->
         <?php $oldname = '';?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <tr>
                <td><?php echo $value['uid']?></td>
                <td><?php echo $value['trxId']?></td>
                <td><?php echo date('Y-m-d H:i:s',$value['ctime'])?></td>
                <td><?php echo $value['log_desc']?></td>
                <td><?php echo $value['in']?></td>
                <td><?php echo $value['out']?></td>
                <td><?php echo $value['balance']?></td>
                <td><?php echo $value['exp_using']?></td>
                
            </tr>
            <?php endforeach;?> 
		<?php endif;?>
		<tr>
    		  <td>总获体验金：<?php echo $count_expmoney;?></td>
    		</tr>
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


