
<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN?>aboutus/addAboutus" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="56">
			<dl>
				<dt>信息名称</dt>
				<dd><input name="title" type="text" size="30" value="" class="required"/></dd>
			</dl>
			<dl>
				<dt>信息内容</dt>
				<dd><textarea name="content" class="required" cols="40" rows="4"></textarea></dd>
			</dl>		
		</div>
		<div class="formBar">
			<ul>
				<input type="hidden" name="op" value="addaboutus"/>
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