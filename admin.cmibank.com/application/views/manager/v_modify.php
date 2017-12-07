
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN; ?>manager/modify/<?php echo $groupInfo['id'];?>" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
    <div class="pageFormContent nowrap" layoutH="97">
    	<dl>
    
    		<dt>账号：</dt>
    		<dd>
    		<input type="text" class="filed-text" name="name" id="name" maxlength="50" value="<?php echo $groupInfo['name'];?>"/>
            </dd>
    
    		<dt>用户名：</dt>
    		<dd>
    		<input type="text" class="filed-text" name="realname" id="realname" value="<?php echo $groupInfo['realname'];?>"/>
            </dd>
    
    
    		<dt>密码：</dt>
    		<dd>
    		<input type="password" class="filed-text" name="password" id="password" />
            </dd>
    
    		<dt>账号：</dt>
    		<dd>
   
            <select class="combox required" name="class_son_id"  ref="classid_group" refUrl="<?php echo OP_DOMAIN; ?>/authority/getGroupSons/{value}">
                <option value="0"selected="" >请选择父类</option>
                <?php foreach($group as $key=>$value){?>
                <option value="<?php echo $value['inner_group'];?>" <?php if(isset($groupInfo['inner_group']) && $value['inner_group']==$groupInfo['inner_group']):?>selected="selected"<?php endif;?>>&nbsp;&nbsp;└<?php echo $value['inner_group'];?></option>
                <?php } ?>
            </select>
            <select class="combox required" name="classid" id="classid_group">
                <option value="0"selected="" >请选择子类</option>
                <?php if($groupSons){?>
                    <?php foreach($groupSons as $key=>$val){?>
                    <option value="<?php echo $val[0];?>" <?php if($groupInfo['post']== $val[1]){?>selected="" <?php }?>><?php echo $val[1];?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            </dd>
    		<input type="hidden" name="op" value="modify" />
    	</dl>
	</div>
	<div class="formBar">
        <ul>                       
            <li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog()">提交</button></div></div></li>
            <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
        </ul>
    </div>
	</form>
</div>
<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 330}}, $.pdialog.getCurrent(), "");
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }
</script>
