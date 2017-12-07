
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN; ?>manager/add" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">

	<div class="pageFormContent nowrap" layoutH="97">
		<dl>
			<dt>账号：</dt>
			<dd>
			<input type="text" class="filed-text" name="name" id="name" maxlength="50" />
                                <span class="wrong_on"></span>
            </dd>
            		
			<dt>用户名：</dt>
			<dd>
			<input type="text" class="filed-text" name="realname" id="realname" /><span class="wrong_on"></span>
		    </dd>
		    
			<dt>密码：</dt>
			<dd>
			<input type="text" class="filed-text" name="password" id="password" maxlength="16" /><span class="wrong_on"></span>
		    </dd>
			<dt>所属分组-岗位：</dt>
			<dd>
		
            <select class="combox required" name="group"  ref="classid_group" refUrl="<?php echo OP_DOMAIN; ?>/authority/getGroupSons/{value}">
                <option value="0"selected="" >请选择分组</option>
                <?php foreach($list as $key=>$value){?>
                <option value="<?php echo $value['inner_group'];?>" >&nbsp;&nbsp;└<?php echo $value['inner_group'];?></option>
                <?php } ?>
            </select>

            <select class="combox required" name="post" id="classid_group"></select>
			
           
            </dd>
			
		</dl>
	</div>
	<div class="formBar">
        <ul>            
            <input type="hidden" name="op" value="add" />           
            <li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog()">提交</button></div></div></li>
            <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
        </ul>
    </div>
    </form>
</div>
<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 350}}, $.pdialog.getCurrent(), "");
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }
</script>
