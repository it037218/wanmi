<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/invite">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($searchtitle)||isset($inviteduser)){?>
    	<?php if(isset($searchtitle)){?>
	     <input type="hidden" name="searchtitle" value="<?php echo $searchtitle; ?>" />
	    <?php }?>
	    <?php if(isset($inviteduser)){?>
	     <input type="hidden" name="inviteduser" value="<?php echo $inviteduser; ?>" />
	    <?php }?>
	     <input type="hidden" value="search" name="op">
     <?php }?>
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/invite" method="post">
            <li><span>邀请人手机号码</</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchtitle)?$searchtitle:'请输入搜索内容'?>"  id="searchtitle" name="searchtitle"></li>
            
            <li><span>被邀请人手机号码</</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($inviteduser)?$inviteduser:'请输入搜索内容'?>"  id="inviteduser" name="inviteduser"></li>
            
            
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <li class="line">line</li>
            
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="10%">邀请人手机号码</th>
                <th width="10%">被邀请人姓名</th>
                <th width="10%">被邀请人手机号码</th>
                <th width="20%">被邀请人首投时间</th>
                <th width="20%">注册时间</th>
                <th width="15%">首投金额</th>
                 <th width="15%">邀请人奖励金额</th>
            </tr>
        </thead>
        <tbody>
         <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <tr>
                <td><?php echo $value['invite_account']?></td>
                <td><?php if(!empty($user[$value['uid']])){echo $user[$value['uid']];}else{ echo "还没有交易";};?></td>
                <td><?php echo $value['u_account']?></td>
                <td><?php if(!empty($value['buytime'])){ echo date('Y-m-d H:i:s',$value['buytime']);}else{ echo "还没有投资";}?></td>
                <td><?php echo date('Y-m-d H:i:s',$value['itime'])?></td>
                <td><?php echo $value['money']?></td>
                <td><?php echo $value['rewardmoney']==0?'':$value['rewardmoney']?></td>
            </tr>
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
	<div class="panelBar">
        <div class="pages">
            <span>显示</span>
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
                <option value="20" <?php echo $numPerPage == 20 ? 'selected' : ''; ?>>20</option>
                <option value="40" <?php echo $numPerPage == 40 ? 'selected' : ''; ?>>40</option>
            </select>
            <span>条，共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>


