<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>authority/editGroupType" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>类型名称：</dt>
                <dd>
                    <input type="text" class="filed-text" name="name" id="name" maxlength="20" value="<?php echo $grouptype['name']; ?>"/>
                </dd>    
            </dl>
            <input type="hidden" name="op" value="edit" />
            <input type="hidden" name="id" value="<?php echo $grouptype['id']; ?>" />
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
     $.pdialog.resizeDialog({style: {height: 150}}, $.pdialog.getCurrent(), "");
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }
</script>