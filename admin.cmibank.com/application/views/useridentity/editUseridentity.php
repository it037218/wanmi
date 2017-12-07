
<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN?>useridentity/editUseridentity" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="56">
		    <dl>
				<dt>用户姓名</dt>
				<dd><input name="realname" type="text" size="30" value="<?php echo $detail['realname']?>" class="required"></dd>
			</dl>
			<dl>
				<dt>身份证号</dt>
				<dd><input name="idCard" type="text" size="30" value="<?php echo $detail['idCard']?>" class="required" readonly="readonly"></dd>
			</dl>
			<dl>
				<dt>手机号码</dt>
				<dd><input name="phone" type="text" size="30" value="<?php echo $detail['phone']?>" class="required"/></dd>
			</dl>
			<dl>
				<dt>开户行</dt>
				<dd>
    				<select name="bankCode">
    				    <option>请选择</option>
    				    <?php foreach ($banklist as $key=>$_bankinfo){?>
    				    <option value="<?php echo $key?>" <?php if($key == $detail['bankcode']){ echo 'selected';}?>><?php echo $_bankinfo['name']?></option>
    				    <?php }?>
    				</select>
				</dd>
			</dl>
			<dl>
				<dt>银行账户</dt>
				<dd><input name="cardno" type="text" size="30" value="<?php echo $detail['cardno']?>" class="required"/></dd>
			</dl>		
		</div>
		<div class="formBar">
			<ul>
				<input type="hidden" name="op" value="saveedit"/>
				<input type="hidden" name="uid" value="<?php echo $detail['uid']?>"/>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>

