
<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN?>useridentity/editUseridentity" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="56">
		    <dl>
				<dt>用户姓名</dt>
				<dd><?php echo $detail['realname']?></dd>
			</dl>
			<dl>
				<dt>身份证号</dt>
				<dd><?php echo $detail['idCard']?></dd>
			</dl>
			<dl>
				<dt>手机号码</dt>
				<dd><?php echo $detail['phone']?></dd>
			</dl>
			<dl>
				<dt>开户行</dt>
				<dd>
    				<?php echo $banklist[$detail['bankCode']]['name'];?>
				</dd>
			</dl>
			<dl>
				<dt>银行账户</dt>
				<dd><?php echo $detail['cardno']?></dd>
			</dl>		
		</div>
		<div class="formBar">
			<ul>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>

