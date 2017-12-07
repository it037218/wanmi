<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN?>luckybag">
     <input type="hidden" name="pageNum" value="<?php echo isset($pageNum) ? $pageNum : 0; ?>" />
   	 <input type="hidden" name="numPerPage" value="<?php echo isset($numPerPage) ? $numPerPage : 0; ?>" />
   	 <?php if(isset($phone)){?>
     <input type="hidden" name="phone" value="<?php echo $phone; ?>" />
     <?php }?>
     <?php if(isset($uid)){?>
      <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
     <?php }?>
     <?php if(isset($ssucctime)){?>
      <input type="hidden" name="stime" value="<?php echo $stime; ?>" />
      <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($esucctime)){?>
      <input type="hidden" name="etime" value="<?php echo $etime; ?>"/>
      <input type="hidden" value="search" name="op">
     <?php }?>
      <input type="hidden" name="type" value="<?php echo $type; ?>" />
      <input type="hidden" name="status" value="<?php echo $status; ?>" />
     <input type="hidden" value="search" name="op">
</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>luckybag" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					手机号码：<input name="phone" id="phone" value="<?php echo isset($phone)?$phone:''?>"/>
				</td>
				<td>
					状态：<select name="status" id="id_status">
						<option value="0" >全部</option>
						<option value="1" <?php if($status == 1){ echo 'selected';}?>>待激活</option>
						<option value="2" <?php if($status == 2){ echo 'selected';}?>>已激活</option>
						</select>
				</td>
				<td>
					类型：<select name="type" id="id_type">
						<option value="0" <?php if($status == 0){ echo 'selected';}?>>全部</option>
						<option value="1" <?php if($status == 1){ echo 'selected';}?>>购买赠送</option>
						<option value="2" <?php if($status == 2){ echo 'selected';}?>>积分兑换</option>
						</select>
				</td>
				<td>
					获得日期：<input name="stime" readonly="true"  class="date"  value="<?php echo isset($stime) ? $stime : "";?>"  />&nbsp;&nbsp;至
            			 <input name="etime" readonly="true" class="date"  value="<?php echo isset($etime) ? $etime : "";?>"  />
				</td>
				<td><input type="hidden" value="search" name="op"><button type="submit">检索</button></td>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <li><span>总笔数：</span></li>
            <li style="padding-top:7px"><?php echo $count; ?></li>
            <li><span>总金额：</</span></li>
            <li style="padding-top:7px"><?php echo isset($sum_money)?$sum_money:0; ?></li>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="95">
        <thead>
            <tr>
            	<th width="5%">id</th>
                <th width="5%">邀请人</th>
                <th width="5%">姓名</th>
                <th width="5%">被邀请人</th>
                <th width="5%">被邀请姓名</th>
                <th width="5%">类型</th>
                <th width="8%">获得时间</th>
                <th width="8%">过期时间</th>
                <th width="8%">激活时间</th>
                <th width="5%">金额</th>
                <th width="8%">状态</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value){
            	$statsstr = "";
            	if($value['status']==1){
            		$statsstr = '<font color="red">待激活</font>';
            	}else if($value['status']==2){
            		$statsstr = '<font color="slateblue">已激活</font>';
            	}else{
            		if($value['etime']<time()){
            			$statsstr = "已过期";
            		}
            	}
            ?>
                <tr>
                	<td><?php echo $value['id'] ;?></td>
                    <td><a href="<?php echo OP_DOMAIN?>/userinfomanage/onePersonaldetail/<?php echo $value['uid']?>" target="navtab"><?php echo empty($phones[$value['uid']])?'':$phones[$value['uid']];?></a></td> 
                    <td><?php echo empty($names[$value['uid']])?'':$names[$value['uid']];?></td>
                    <td><a href="<?php echo OP_DOMAIN?>/userinfomanage/onePersonaldetail/<?php echo $value['uuid']?>" target="navtab"><?php echo $value['uuaccount'] ;?></a></td>
                    <td><?php echo empty($names[$value['uuid']])?'':$names[$value['uuid']];?></td>
                    <td><?php echo $value['type']==1?'购买赠送':'积分兑换'?></td>
                    <td><?php echo empty($value['ctime'])?'':date('Y-m-d H:i:s',$value['ctime']);?></td>
                    <td><?php echo empty($value['etime'])?'':date('Y-m-d H:i:s',$value['etime']);?></td>
                    <td><?php echo empty($value['utime'])?'':date('Y-m-d H:i:s',$value['utime']);?></td>
                    <td><?php echo $value['money'] ;?></td>          
                    <td><?php echo $statsstr;?></td>
                </tr>
            <?php }?>
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
            <span>共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>


