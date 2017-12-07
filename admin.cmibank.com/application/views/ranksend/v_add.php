
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>ranksend/add" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="50">
                <dl>
                    <dt>用户列表：</dt>
                	<dd>
                		<textarea name="accounts" cols="63" rows="6" id="id_accounts"  class="required"></textarea>
                	</dd>
                </dl>
                 <dl>
                    <dt>申请金额</dt>
                    <dd>
                        <input type="text" class="filed-text" name="money"/>
                    </dd>
                </dl>
                <dl>
                    <dt>备注</dt>
                    <dd>
                        <textarea style="width:200px;height:50px" name="remark" id="remark"></textarea>
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
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }
</script>

