<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN?>notice/editNotice" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent nowrap" layoutH="56">
			<dl>
				<dt>公告主题</dt>
				<dd><input name="title" type="text" size="30" value="<?php echo $detail['title']?>" class="required"/></dd>
			</dl>
			<dl>
				<dt>公告内容</dt>
				<dd><textarea name="content" class="required" cols="200" rows="30"><?php echo $detail['content']?></textarea></dd>
			</dl>
			<dl><dt></dt><dd></dd></dl>
			<dl><dt></dt><dd></dd></dl>
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
				        <option value="0" <?php if($detail['type'] == 0){ echo 'selected';}?>>广播</option>
				        <option value="1" <?php if($detail['type'] == 1){ echo 'selected';}?>>注册用户</option>
				        <option value="2" <?php if($detail['type'] == 2){ echo 'selected';}?>>非注册用户</option>
				        <option value="3" <?php if($detail['type'] == 3){ echo 'selected';}?>>所有交易过的用户</option>
				        <option value="4" <?php if($detail['type'] == 4){ echo 'selected';}?>>第一次交易用户</option>
				        <option value="5" <?php if($detail['type'] == 5){ echo 'selected';}?>>第二次交易用户</option>
				    </select>
				</dd>
			</dl>
			<dl>
			 <dt>手机类型：</dt>
			 <dd>
			    <select name="phonetype">
			    <option value="0" <?php if($detail['phonetype'] == 0){ echo 'selected';}?>>全部</option>
		        <option value="1" <?php if($detail['phonetype'] == 1){ echo 'selected';}?>>ios系统手机</option>
		        <option value="2" <?php if($detail['phonetype'] == 2){ echo 'selected';}?>>安卓手机用户</option>
		        </select>
			 </dd>
			</dl>		
		</div>
		<div class="formBar">
			<ul>
				<input type="hidden" name="op" value="editNotice"/>
				<input type="hidden" name="nid" value="<?php echo $detail['nid']; ?>"/>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog()">保存</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>
<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 400}}, $.pdialog.getCurrent(), "");
	function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }	
</script>
