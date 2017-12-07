
<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN?>backcontract/editbackcontract" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="56">
			<dl>
				<dt>产品名字</dt>
				<dd><input name="pname" type="text" size="30" value="<?php echo $detail['pname'];?>" class="required"/></dd>
			</dl>
			<dl>
				<dt>编号</dt>
				<dd><textarea name="number" class="required" cols="40" rows="4"><?php echo $detail['number'];?></textarea></dd>
			</dl>		
		</div>
		<div class="formBar">
			<ul>
				<input type="hidden" name="op" value="saveedit"/>
				<input type="hidden" name="bid" value="<?php echo $detail['bid']; ?>"/>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>

