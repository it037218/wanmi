<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/paylog">
     <input type="hidden" name="pageNum" value="<?php echo isset($pageNum) ? $pageNum : 0; ?>" />
   	 <input type="hidden" name="numPerPage" value="<?php echo isset($numPerPage) ? $numPerPage : 0; ?>" />
   	 <?php if(isset($phone)){?>
     <input type="hidden" name="phone" value="<?php echo $phone; ?>" />
     <?php }?>
     <?php if(isset($uid)){?>
     <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
     <?php }?>
     <?php if(isset($trxId)){?>
     <input type="hidden" name="trxId" value="<?php echo $trxId; ?>" />
     <?php }?>
      <?php if(isset($stime)){?>
     <input type="hidden" name="stime" value="<?php echo $stime; ?>" />
     <?php }?>
      <?php if(isset($etime)){?>
     <input type="hidden" name="etime" value="<?php echo $etime; ?>" />
     <?php }?>
      <?php if(isset($errormsg)){?>
     <input type="hidden" name="errormsg" value="1" />
     <?php }?>
      <?php if(isset($status)){?>
     <input type="hidden" name="status" value="1" />
     <?php }?>
     <input type="hidden" value="search" name="op">
</form>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/paylog" method="post">
            <li><span>手机号码</span></li>
            <li><input name="phone" id="phone" value="<?php echo isset($phone)?$phone:''?>"/></li>
            <li><span>uid</span></li>
            <li><input name="uid" id="uid" value="<?php echo isset($uid)?$uid:''?>"/></li>
            <li><span>订单号</span></li>
            <li><input name="trxId" id="trxId" value="<?php echo isset($trxId)?$trxId:''?>"/></li>
            <li><span>日期</</span></li>
            <li><input name="stime" readonly="true"  class="date"  value="<?php echo isset($stime) ? $stime : "";?>"/>&nbsp;&nbsp;至</li>
            <li><input name="etime" readonly="true" class="date"  value="<?php echo isset($etime) ? $etime : "";?>"/></li>
            <li><span>错误消息为空</span></li>
            <li><input type="checkbox" name="errormsg" value="1" <?php echo isset($errormsg) ? 'checked' : "";?>></li>
            <li><span>已购买</span></li>
            <li><input type="checkbox" name="status" value="1" <?php echo isset($status) ? 'checked' : "";?>></li>
            <li><input type="hidden" value="search" name="op"><button type="submit">检索</button></li>
            </form>
        </ul>
    </div>

    <div class="panelBar">
        <ul class="toolBar">
            <li><span>总金额：</span></li>
            <li style="padding-top:7px"><?php echo isset($sum)?$sum:0; ?></li>
        </ul>
    </div>

	<table class="list" width="100%" layoutH="110">
        <thead>
            <tr>
                <th width="10%">订单id</th>
                <th width="10%">平台id</th>
                <th width="3%">用户id</th>
                <th width="3%">币种</th>
                <th width="5%">充值金额</th>
                <th width="5%">平台</th>
                <th width="6%">状态</th>
                <th width="10%">时间</th>
                <th width="5%">是否回调</th>
                <th width="13%">错误消息</th>
                <th width="10%">错误代码</th>
                <th width="10%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <?php 
            switch ($value['isback']){
                case 0: $isback = '没有';break;
                case 1: $isback = '有';break;
                default: $isback = '末知状态'; break;
            }
            
            switch ($value['status']){
                case 0: $status = '未购买';break;
                case 1: $status = '已购买';break;
                default: $status = '末知状态'; break;
            }
            
            ?>
                <tr>
                    <td><?php echo $value['ordid'] ;?></td>
                    <td><?php echo $value['trxId'] ;?></td>
                    <td><?php echo $value['uid'] ;?></td>
                    <td><?php echo $value['curcode'] ;?></td>
                    <td><?php echo $value['amt'] ;?></td>
                    <td><?php echo $value['platform'] ;?></td>
                    <td><?php echo $status;?></td>
                    <td name="ctime" id="ctime"><?php echo date('Y-m-d H:i:s',$value['ctime']);?></td>
                    <td><?php echo $isback;?></td>
                    <td><?php echo $value['errormsg'] ;?></td>
                    <td><?php echo $value['errorcode'] ;?></td>
                    <td>
                    <a href="<?php echo OP_DOMAIN;?>paylog/editpaylog/<?php echo $value['ordid'] ;?>" target="dialog">编辑</a>
                    |&nbsp;&nbsp;
                    <?php if($value['isback']==0){?>
                    <a href="<?php echo OP_DOMAIN;?>paylog/handle/<?php echo $value['ordid'] ;?>" target="ajaxTodo" title="您真的要处理吗?">处理</a>
                    <?php }?>
                    <?php   
                        $num = intval(date('W',strtotime(date('Ymd',$value['ctime']))));
                        $now = intval(date('W',strtotime(date('Ymd',time()))));
                        if($num != $now && $value['isback']== 0){
                    ?>
                    |&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN;?>paylog/backtotpaylog/<?php echo $value['ordid'] ;?>" target="ajaxTodo">加入本周</a>
                    <?php }?>
                    </td>
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
            <span>共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>


