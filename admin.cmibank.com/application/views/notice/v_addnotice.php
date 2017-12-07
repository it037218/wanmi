<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN?>notice/addNotice" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent nowrap" layoutH="56">
			<dl>
				<dt>公告主题</dt>
				<dd><input name="title" type="text" size="30" value="" class="required"/></dd>
			</dl>
			<dl>
				<dt>公告内容</dt>
				<dd><textarea name="content" class="required" cols="200" rows="30"></textarea></dd>
			</dl>
			<!--  
			<dl>
				<dt>预约发布时间</dt>
				<dd><input name="yugaotime" type="text" class="date" datefmt="yyyy-MM-dd HH:mm:ss" readonly="true" size="30" value="" class="required"/></dd>
			</dl>
			-->
			<dl>
				<dt>发布类型</dt>
				<dd>
				    <select name="type">
				        <option value="0">广播</option>
				        <option value="1">注册用户</option>
				        <option value="2">非注册用户</option>
				        <option value="3">所有交易过的用户</option>
				        <option value="4">第一次交易用户</option>
				        <option value="5">第二次交易用户</option>
				    </select>
				</dd>
			</dl>
			<dl>
			 <dt>手机类型：</dt>
			 <dd>
			    <select name="phonetype">
			    <option value="0">全部</option>
		        <option value="1">ios系统手机</option>
		        <option value="2">安卓手机用户</option>
		        </select>
			 </dd>
			</dl>		
		</div>
		<div class="formBar">
			<ul>
				<input type="hidden" name="op" value="addNotice"/>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>
<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 400}}, $.pdialog.getCurrent(), "");
</script>
