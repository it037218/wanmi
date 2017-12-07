
<style>
.pageFormContent dl.nowrap dd, .nowrap dd {
    width: 400px;
}
</style>
<script type="text/javascript">

$(function() {
	$('input:radio').click( function () {
		if($(this).val()==1){
			$("#id_accounts").val("");
			$('#id_accounts').attr({ disabled : true });
		}else{
			$('#id_accounts').attr({ disabled : false });
		}
		$('#id_type').val($(this).val());
	});
});

</script>
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>messsend/addMessSend" class="pageForm required-validate" onsubmit="return validateCallback(this,closedialog)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>消息主题：</dt>
                <dd>
                	<input type="text" class="filed-text required" name="title"  style="width:80%"/>
                </dd>
            </dl>	
            <dl>    
                <dt>发放用户：</dt>
                <dd>
                    <table>
                    	<tr style="height: 25px">
                    		<td><input name="type" type="radio" id="type_radio" style="float:left" value="1" checked/>所有用户</td>
                    		<td><input name="type" type="radio" id="type_radio" style="float:left" value="2"/>指定用户</td>
                    	</tr>
                    </table>
                </dd>
            </dl>
            <dl>    
            	<dt>用户列表：</dt>
                <dd>
                	<textarea name="accounts" cols="63" rows="6" id="id_accounts" ></textarea>
                </dd>
            </dl>
           <dl>    
                <dt>消息内容：</dt>
               <dd>
                	<textarea name="content" cols="63" rows="6" id="id_content" ></textarea>
                </dd>
            </dl>
            <dl>
                <dt>消息连接：</dt>
                <dd>
                	<input type="text" class="filed-text" name="link"  style="width:80%"/>
                </dd>
            </dl>	
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="addmesssend"/>
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 450}}, $.pdialog.getCurrent(), "");

     function closedialog(json){
   		$.pdialog.closeCurrent();	
   		navTabAjaxDone(json);
   	 }
</script>
