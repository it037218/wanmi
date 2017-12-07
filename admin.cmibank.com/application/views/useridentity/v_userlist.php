<div class="pageContent">
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
            	<th width="2%">序号</th>
                <th width="4%">用户id</th>
                <th width="4%">用户名称</th>
                <th width="6%">注册手机号码</th>
                <th width="7%">银行预留手机号码</th>
                <th width="5%">身份证号码</th>
                <th width="5%">开户银行</th>
                <th width="7%">银行卡号</th>
                <th width="7%">注册时间</th>
                <th width="7%">绑卡时间</th>
                <th width="7%">最后登录时间</th>
                <th width="4%">是否绑定</th>
                <th width="4%">是否新用户</th>
                <th width="4%">用户来源</th>
                <th width="57%">操作</th>
            </tr>
        </thead>
        <tbody>
        <!-- --- $banklist['01050000']['name']);-->
         <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <tr>
            	<td><?php echo $key+1 ;?></td>
                <td><?php echo $value['uid'] ;?></td>
                <td><?php echo $value['realname'] ;?></td>
                <td><?php echo $value['account'] ;?></td>
                <td><?php echo $value['phone'] ;?></td>
                <td><?php echo $value['idCard'];?></td>
                <td><?php echo isset($banklist[$value['bankcode']]['name']) ? $banklist[$value['bankcode']]['name'] :'--'; ?></td>
                <td><?php echo $value['cardno'];?></td>
                <td><?php echo date('Y-m-d H:i:s', $value['ctime']);?></td>
                <td><?php echo empty($value['requestid'])?'':date('Y-m-d H:i:s', substr($value['requestid'],0,10));?></td>
                <td><?php echo date('Y-m-d H:i:s', $value['ltime']);?></td>
                <td><?php echo $value['ischeck'] == 1 ? '<font color="green" >绑定</font>': '<font color="red" >末绑定</font>';?></td>
                <td><?php echo empty($value['isnew'])?'新用户':($value['isnew'] == 1 ? '新用户': '老用户');?></td>
                <td><?php echo $value['plat']?></td>
                <td>
                    <!-- <a href="<?php echo OP_DOMAIN?>/useridentity/editUseridentity/<?php echo $value['uid']?>" target="dialog">修改用户信息</a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                     <a href="<?php echo OP_DOMAIN?>/useridentity/Tiebankcard/<?php echo $value['pnr_usrid']?>/<?php echo $value['cardno']?>/<?php echo $value['uid']?>" target="ajaxTodo" title="您真的要删除吗?">解绑银行卡</a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;-->
                    <a href="<?php echo OP_DOMAIN?>/userinfomanage/onePersonaldetail/<?php echo $value['uid']?>" target="navtab">查看账户</a>
                    <?php if($editable==1){?>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN?>/useridentity/editUseridentity/<?php echo $value['uid']?>" target="dialog">修改用户信息</a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN?>/useridentity/ReseTpwd/<?php echo $value['uid']?>" target="ajaxTodo" title="你确定要重置交易密码">重置交易密码</a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN?>/useridentity/ResePwd/<?php echo $value['uid']?>" target="ajaxTodo" title="重置登录密码">重置登录密码</a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <?php if($value['ischeck'] == 0){?>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo OP_DOMAIN?>/useridentity/againUseridentity/<?php echo $value['uid']?>" target="ajaxTodo" title="您真的要重新绑定吗?">重新绑定</a>
                    <?php }else{?>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo OP_DOMAIN?>/useridentity/resetUseridentity/<?php echo $value['uid']?>" target="ajaxTodo" title="您真的要解除绑定吗?">解除绑定</a>
                    <?php }?>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN?>/useridentity/restWithDraw/<?php echo $value['uid']?>" target="ajaxTodo" title="重置充值次数吗?">重置充值次数</a>
                    <?php if($value['forbidden'] == 0){?>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo OP_DOMAIN?>/useridentity/setforbidden/<?php echo $value['uid']?>" target="ajaxTodo" title="您真的要注册账户吗?">注销</a>
                    <?php }else{?>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo OP_DOMAIN?>/useridentity/resetforbidden/<?php echo $value['uid']?>" target="ajaxTodo" title="您真的要解除绑定吗?">取消注销</a>
                    <?php }?>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN?>/useridentity/updateUserRegisterPhone/<?php echo $value['uid']?>" target="dialog" >修改注册手机</a>
                    <?php }?>
                </td>
            </tr>
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</div>


