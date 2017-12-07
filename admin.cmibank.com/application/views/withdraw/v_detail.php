
<style>
.pageFormContent dl.nowrap dd, .nowrap dd {
    width: 400px;
}
</style>
<script type="text/javascript">

$(function() {
	$('#formTable input').attr({
		disabled : "true"
	});
});

</script>
<div class="pageContent">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>电话号码：</dt>
                <dd>
                	<input type="text" class="phone required" name="account" value="<?php echo $detail['account'] ;?>"/>
                	<input name="id" value="<?php echo $detail['id'] ;?>" style="visibility: hidden;"/>
                </dd>
            </dl>	
            <dl>    
                <dt>取现金额：</dt>
                <dd>
                    <input type="text" class="number required" name="sendmoney" value="<?php echo $detail['money'] ;?>"/>
                </dd>
            </dl>
            <dl style="height: 150px;">
				<dt>取现凭证：</dt>
				<dd>
                    <a href='<?php echo $detail['url'];?>' id="_open_service_image" target="_blank"><img alt="图片预览" id="_show_service_image" src="<?php echo $detail['url'];?>" style="position: absolute;height:150px;width: 300px;"/></a>
				</dd>
			</dl>
            <dl>    
                <dt>备注：</dt>
                <dd>
                	<textarea class="required" name="remark" cols="40" rows="5"><?php echo $detail['remark'];?></textarea>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close" onclick="closedialog()">取消</button></div></div></li>
            </ul>
        </div>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 450}}, $.pdialog.getCurrent(), "");
     function closedialog(){
	  		$.pdialog.closeCurrent();	
	 }
</script>
