
<style>
.pageFormContent dl.nowrap dd, .nowrap dd {
    width: 400px;
}
</style>
<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN;?>withdraw/doWithdraw" class="pageForm required-validate" onsubmit="return validateCallback(this,closedialog)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>输入验证码：</dt>
                <dd>
                	<input type="text" name="code" class="number required"/>
                	<a style="padding-left: 10px" href="<?php echo OP_DOMAIN;?>withdraw/sendCode/<?php echo $detail['id']?>" target="ajaxTodo" title="发送验证码?">获取验证码</a>
                	<input name="id" value="<?php echo $detail['id'] ;?>" style="visibility: hidden;"/>
                </dd>
            </dl>	
        </div>
        <div class="formBar">
            <ul>
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 300}}, $.pdialog.getCurrent(), "");
     function closedialog(json){
	  		$.pdialog.closeCurrent();	
	  		navTabAjaxDone(json);
	 }
</script>
