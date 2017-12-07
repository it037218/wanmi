
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>proftype/editproftype" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>业务类型</dt>
                <dd>
                <input type="text" class="filed-text" name="proftype" value="<?php echo $detail['proftype']; ?>" />
                </dd>
                <dt>业务名称</dt>
                <dd>
                <input type="text" class="filed-text" name="profname" value="<?php echo $detail['profname']; ?>" />
                </dd>
                
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="saveproftype" />
                <input type="hidden" name="profid" value="<?php echo $detail['profid']; ?>" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog()">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 280}}, $.pdialog.getCurrent(), "");
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }
</script>
