<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/useridentity">
   	 <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
     <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
   	 <?php if(isset($type)){?>
     <input type="hidden" name="type" value="<?php echo $type; ?>" />
     <?php }?>
     <?php if(isset($searchtitle)){?>
     <input type="hidden" name="searchtitle" value="<?php echo $searchtitle; ?>" />
     <?php }?>
     <input type="hidden" value="<?php echo $bangka; ?>" name="bangka">
   	 <?php if(isset($searchtitle)){?>
     <input type="hidden" value="search" name="op">
     <?php }?>
</form>
<div class="pageHeader">
	<form onsubmit="return myValidateCallback(this);"  class="pageForm required-validate" action="<?php echo OP_DOMAIN?>/useridentity" method="post" id="form_cc"> 
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					<select name="type">
						<option value="1" <?php if($type == 1){ echo 'selected';}?>>注册手机号</option>
						<option value="2" <?php if($type == 2){ echo 'selected';}?>>用户姓名</option>
						<option value="3" <?php if($type == 3){ echo 'selected';}?>>UID</option>
						<option value="4" <?php if($type == 4){ echo 'selected';}?>>银行预留手机号码</option>
						<option value="5" <?php if($type == 5){ echo 'selected';}?>>身份证号码</option>
					</select>
				</td>
				<td>
					<input type="text"  value="<?php echo isset($searchtitle)?$searchtitle:''?>"  id="searchtitle" name="searchtitle" class='filed-text required'>
				</td>
				<td>是否绑卡：
					<select name="bangka" id="id_bangka">
						<option value="0">全部</option>
						<option value="1" <?php if($type == 1){ echo 'selected';}?>>已绑卡</option>
						<option value="2" <?php if($type == 2){ echo 'selected';}?>>未绑卡</option>
					</select></td>
				<td><input type="hidden" value="search" name="op"><button type="submit" >检索</button></td>
			</tr>
			
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
        	<?php if($editable==1){?>
            <li><span>注册人数：</span></li>
            <li style="padding-top:7px"><?php echo isset($accountNumber) ? $accountNumber : 0;?>人</li>
            <li><span>绑卡用户：</</span></li>
            <li style="padding-top:7px"><?php echo $totalValidate;?>人</li>
            <li><span>定期交易人数：</</span></li>
            <li style="padding-top:7px"><?php echo isset($useriNumber) ? $useriNumber : 0?>人</li>
            <li><span>总交易人数：</</span></li>
            <li style="padding-top:7px"><?php echo isset($userallNumber) ? $userallNumber : 0?>人</li>
            <?php }?>
            <li><span>实时用户余额：</span></li>
            <li style="padding-top:7px"><?php echo isset($totalBalance) ? $totalBalance : 0;?>元</li>
            <li><span>实时用户活期总额：</</span></li>
            <li style="padding-top:7px"><?php echo $totalLongMoney?>元</li>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="95">
        <thead>
            <tr>
                <th width="4%">用户id</th>
                <th width="4%">id表</th>
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
                <td><?php echo $value['uid'] ;?></td>
                <td><?php echo $value['uid']%16 ;?></td>
                <td><?php echo $value['realname'] ;?></td>
                <td><?php echo $value['account'] ;?></td>
                <td><?php echo $value['phone'] ;?></td>
                <td><?php echo $value['idCard'];?></td>
                <td><?php echo isset($banklist[$value['bankcode']]['name']) ? $banklist[$value['bankcode']]['name'] :'--'; ?></td>
                <td><?php echo $value['cardno'];?></td>
                <td><?php echo date('Y-m-d H:i:s', $value['ctime']);?></td>
                <td><?php echo date('Y-m-d H:i:s', $value['bankatime']);?></td>
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
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <?php if(empty($value['fengkong'])){?>
                        <a href="<?php echo OP_DOMAIN?>/useridentity/addFengKong/<?php echo $value['uid']?>/<?php echo $value['account']?>" target="ajaxTodo" title="您真的要列为风控吗?">列为风控</a>
                    <?php }else{?>
                        <a href="<?php echo OP_DOMAIN?>/useridentity/removeFengKong/<?php echo $value['uid']?>" target="ajaxTodo" title="您真的要重新移除风控吗?" style="color:red">移除风控</a>
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

<script type="text/javascript">
function myValidateCallback(form) {
	var $form = $(form);

	if (!$form.valid()) {
		return false;
	}
	return navTabSearch(form);
}
</script>
