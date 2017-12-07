
<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN?>version/addVersion" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="56">
			<dl>
				<dt>版本号</dt>
				<dd><input name="number" type="text" size="30" value="" class="required"/></dd>
			</dl>
			<dl>
				<dt>版本特性</dt>
				<dd><textarea name="features" class="required" cols="40" rows="5"></textarea></dd>
			</dl>
			<dl><dt></dt><dd></dd></dl>
			<dl><dt></dt><dd></dd></dl>
			<dl>
				<dt>上线时间</dt>
				<dd><input type="text"  name="linetime"  class="date required" dateFmt="yyyy-MM-dd HH:mm:ss"/></dd>
			</dl>		
		</div>
		
		<div class="formBar">
			<ul>
				<input type="hidden" name="op" value="addversion"/>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog()">保存</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>
<script type="text/javascript">
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }
</script>
