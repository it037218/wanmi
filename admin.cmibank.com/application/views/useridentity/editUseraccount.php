
<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN?>useridentity/updateUserRegisterPhone" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="56">
		    <dl>
				<dt>原账号:</dt>
				<dd><?php echo $account; ?></dd>
			</dl>
			<dl>
				<dt>新账号:</dt>
				<dd><input name="new_phone" type="text" size="30" value="" /></dd>
			</dl>
		</div>
		<div class="formBar">
			<ul>
				<input type="hidden" name="op" value="saveedit"/>
				<input type="hidden" name="uid" value="<?php echo $uid; ?>"/>
				<input type="hidden" name="old_phone" value="<?php echo $account; ?>"/>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>

