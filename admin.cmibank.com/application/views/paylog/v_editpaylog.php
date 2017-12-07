
<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN?>paylog/editpaylog" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="56">
			<dl>
				<dt>错误消息</dt>
				<dd>
				<textarea name="errormsg" class="required textInput valid" cols="50" rows="3"><?php echo $detail[0]['errormsg'];?> </textarea>
			</dl>		
		</div>
		<div class="formBar">
			<ul>
				<input type="hidden" name="op" value="saveedit"/>
				<input type="hidden" name="ordid" value="<?php echo $detail[0]['ordid']; ?>"/>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>
