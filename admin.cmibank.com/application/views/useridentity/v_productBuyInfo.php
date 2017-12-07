<div class="pageContent">
	<table class="list" width="100%" layoutH="35">
        <thead>
            <tr>
             	<th width="2%">序号</th>
                <th width="6%">产品名称</th>
                <th width="6%">订单号</th>
                <th width="6%">用户姓名</th>
                <th width="6%">手机号</th>
                <th width="6%">身份证号</th>
                <th width="6%">购买金额</th>
                <th width="6%">购买时间</th>

            </tr>
        </thead>
        <tbody>
        <!-- --- $banklist['01050000']['name']);-->
         <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
        <?php ?>
            <?php foreach($list AS $key=>$value):?>
            <tr>
               <td><?php echo $key+1?></td>
               <td><?php echo $value['pname']?></td>
               <td><?php echo $value['ordid'];?></td>
               <td><?php echo $value['realname']?></td>
               <td><?php echo $value['account'];?></td>
               <td><?php echo $value['idCard']?></td>
               <td><?php echo $value['amt']?></td>
               <td><?php echo date('Y-m-d H:i:s',$value['ctime']);?></td>
                
            </tr>
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</div>


