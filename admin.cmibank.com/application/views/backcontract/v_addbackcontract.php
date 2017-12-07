
<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN?>backcontract/addbackcontract" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="56">
			<dl>
				<dt>产品名字</dt>
				<dd><input name="pname" type="text" size="30" value="" class="required"/></dd>
			</dl>
			<dl>
				<dt>编号</dt>
				<dd><input name="number" type="text" size="30" value="" class="required"/></dd>
			</dl>		
		</div>
		<div class="formBar">
			<ul>
				<input type="hidden" name="op" value="addbackcontract"/>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>

