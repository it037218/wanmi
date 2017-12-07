<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/bireport/dayallplat">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" /> 
</form>
 
<div class="pageContent">
    <!--<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/bireport" method="post">
            <li><span>日期</</span></li>
            <li><input name="stime" readonly="true"  class="date" onfocus="if(this.value=='请选择开始时间') this.value='';" onblur="if(this.value=='') this.value='请选择开始时间';" value="<?php echo isset($stime)?$stime:'请选择开始时间'?>"/>&nbsp;&nbsp;至</li>
            <li><input name="etime" readonly="true" class="date" onfocus="if(this.value=='请选择结束时间') this.value='';" onblur="if(this.value=='') this.value='请选择结束时间';" value="<?php echo isset($etime)?$etime:'请选择结束时间'?>"/></li>
            <li><input type="hidden" value="search_bannertime" name="op"><button type="submit">检索</button></li>
            </form>           
        </ul> -->
    </div>
    
	<table class="list" width="100%" layoutH="30">
        <thead>
            <tr>
                <th width="8%">日期</th>
                <th width="4%">激活数</th>
                <th width="4%">注册数</th>
                <th width='4%'>当日新增绑卡用户</th>
                <th width='4%'>当日新增交易用户</th>
                <th width="4%">注册激活</th>
                <th width="4%">当日新增交易</th>
                <th width="4%">老用户转化交易人数</th>
                <th width="4%">老用户转化交易</th>
                <th width="4%">新增交易汇总</th>
                <th width="4%">当日新增交易/注册</th>
                <th width="4%">当日新增交易/绑卡</th>
                <th width="4%">当日新增绑卡/注册</th>
                <th width="4%">新用户新增交易金额</th>
                <th width="5%">老用户转化交易金额</th>
                <th width="5%">当日购买用户数</th>
                <th width="5%">当日定期交易金额</th>
                <th width="5%">当日活期交易金额</th>      
                <th width="5%">当日总交易金额</th>
                <th width="4%">当日申购笔数</th>
                <th width="5%">ARPU</th>
                <th width="4%">复购率</th>
                <th width="4%">留存率</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)):?>
            <?php foreach($list as $value){?>
                <tr>
                    <td><?php echo $value['cdate'];?></td>
                    <td><?php echo "0";?></td>
                    <?php if(empty($value['register'])){?>
	                    <td><?php echo $value['register'];?></td>
                    <?php }else{?>
                    	<td><a href="<?php echo OP_DOMAIN?>/useridentity/getSomeUsers/1/<?php echo $value['cdate'];?>/0" target="navtab" title="用户基本信息管理"><?php echo $value['register'];?></a></td>
                    <?php }?>
                    <?php if(empty($value['bangkashu'])){?>
	                    <td><?php echo $value['bangkashu'];?></td>
                    <?php }else{?>
                    	<td><a href="<?php echo OP_DOMAIN?>/useridentity/getSomeUsers/2/<?php echo $value['cdate'];?>/0" target="navtab" title="用户基本信息管理"><?php echo $value['bangkashu'];?></a></td>
                    <?php }?>
                    <td><?php echo $value['newuid'];?></td>
                    <td><?php echo "0";?></td>
                    <td><?php echo $value['daydeal'];?></td>
                    <td><?php echo $value['oldnum'];?></td>
                    <td><?php echo $value['olddeal'];?></td>
                    <td><?php echo $value['countdeal'];?></td>
                    <td><?php echo floor($value['deal_reg']).'%';?></td>
                    <td><?php echo floor($value['daydeal_bangka']).'%';?></td>
                    <td><?php echo floor($value['bangka_reg']).'%';?></td>
                    <td><?php echo $value['dealmoney'];?></td>
                    <td><?php echo $value['oldmoney'];?></td>
                    <td><?php echo $value['daybuyuser'];?></td>
                    <td><?php echo $value['daymoney_d'];?></td>
                    <td><?php echo $value['daymoney_h'];?></td>
                    <td><?php echo $value['daymoney'];?></td>
                    <td><?php echo $value['daynumber'];?></td>
                    <td><?php echo floor($value['arpu']);?></td>
                    <td><?php echo round($value['fugoulv'],2).'%';?></td>
                    <td><?php echo "0";?></td>
                </tr>
            <?php }?>
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
    </div>
</div>

