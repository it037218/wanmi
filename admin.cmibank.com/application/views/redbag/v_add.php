
<style>
.pageFormContent dl.nowrap dd, .nowrap dd {
    width: 400px;
}
</style>
<script type="text/javascript">

$(function() {
	$('input:radio[name=redbag_user_type]').click( function () {
		$('#user_type').val($(this).val());
	});
	$('input:radio[name=redbag_type]').click( function () {
		$('#redbag_type').val($(this).val());
	});
});

</script>
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>redbag/addRedbag" class="pageForm required-validate" onsubmit="return validateCallback(this,closedialog)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>红包名称：</dt>
                <dd>
                	<input type="text" class="filed-text required" name="name" />
                </dd>
            </dl>	
            <dl>    
                <dt>发放用户：</dt>
                <dd>
                    <table>
                    	<tr style="height: 25px">
                    		<td><input name="redbag_user_type" type="radio" id="type_radio" style="float:left" value="3" checked/>所有用户</td>
                    		<td><input name="redbag_user_type" type="radio" id="type_radio" style="float:left" value="2"/>老用户</td>
                    		<td><input name="redbag_user_type" type="radio" id="type_radio" style="float:left" value="1"/>新用户</td>
                    		<input type="hidden" id="user_type" name="user_type" class="filed-text required" value="3"/>
                    	</tr>
                    </table>
                </dd>
            </dl>
             <dl>    
                <dt>红包类型：</dt>
                <dd>
                    <table>
                    	<tr style="height: 25px">
                    		<td><input name="redbag_type" type="radio" id="type_radio" style="float:left" value="1" checked/>固定金额红包(每个)</td>
                    		<td><input name="redbag_type" type="radio" id="type_radio" style="float:left" value="2"/>随机金额红包(总额)</td>
                    		<input type="hidden" id="redbag_type"  name="redbag_type" class="filed-text required" value="1"/>
                    	</tr>
                    </table>
                </dd>
            </dl>
            <dl>    
            	<dt>金额：</dt>
                <dd>
                	<input type="text" class="filed-text required" name="money" id="money" onchange="showTotal();"/>
                </dd>
            </dl>
            <dl>    
            	<dt>个数：</dt>
                <dd>
                	<input type="text" class="filed-text required" name="counts" id="counts" onchange="showTotal();"/>
                </dd>
            </dl>
             <dl>    
            	<dt>总额：</dt>
                <dd>
                	<input type="text" class="filed-text required"  readonly="readonly" id="total"/>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="addredbag"/>
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
   	 function showTotal(){
		var type = $('#redbag_type').val();
		if(type=='1'){
			var money = parseFloat($('#money').val());
			var counts = parseInt($('#counts').val());
			$('#total').val(money*counts);
		}else{
			$('#total').val($('#money').val());
			}
   	   	 
   	 }
</script>
