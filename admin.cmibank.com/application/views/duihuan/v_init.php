
<style>
.pageFormContent dl.nowrap dd, .nowrap dd {
    width: 400px;
}
</style>
<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN;?>duihuan/doDuihuan" class="pageForm required-validate" onsubmit="return validateCallback(this,closedialog)">
        <div class="pageFormContent nowrap" layoutH="97">
            <div style='text-align: center;font-size:30px;margin-top:10px'>请输入<?php echo $detail['name'];?>实际金额：</div>
                <div style="margin-top:50px;margin-left:200px"><input type="text" name="money" class="number required"/></div>
                <input type="hidden" name="id" value="<?php echo $detail['wid'] ;?>" />
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
