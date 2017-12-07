
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
	var cids=$('#id_cids').val().split(',');
	$('input:checkbox').each( function () {
		for(var i=0;i<cids.length;i++){
			if(this.id==cids[i]){
				this.checked=true;
				break;	
			}
		}
	});
});

</script>
<div class="pageContent">
    <form id="formTable">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>活动名称：</dt>
                <dd>
                	<input type="text" class="filed-text required" name="name" value="<?php echo $detail['name'] ;?>"/>
                </dd>
            </dl>	
            <dl>    
                <dt>发放用户：</dt>
                <dd>
                    <table>
                    	<tr style="height: 25px">
                    		<td><input name="type" type="radio" id="type_radio1" style="float:left" value="1" <?php if($detail['type'] == 1){ echo 'checked';}?>/>所有用户</td>
                    		<td><input name="type" type="radio" id="type_radio2" style="float:left" value="2" <?php if($detail['type'] == 2){ echo 'checked';}?>/>指定用户</td>
                    	</tr>
                    </table>
                </dd>
            </dl>
            <dl>    
            	<dt>用户列表：</dt>
                <dd>
                	<textarea name="accounts" cols="63" rows="6" id="id_accounts"><?php echo $detail['accounts'] ;?></textarea>
                </dd>
            </dl>
            <dl>    
                <dt>赠送抵用券：</dt>
                <dd style="border: solid 1px #b8d0d6">
                	 <?php if(!empty($couponList)){?>
            			<?php foreach($couponList AS $key=>$value){?>
		                	<div style="height: 25px">
	                			<label style="width: 100%"><input type="checkbox" name="cid" value="<?php echo $value['id'] ;?>" id="<?php echo $value['id'] ;?>"/><?php echo $value['name'].'， '.$value['sendmoney'].'元，起购'.$value['minmoney'].'，适用于'.$value['pnames'];?></label>
		                	</div>
						<?php }?>
					<?php }?>
					<input type="hidden" name="cids" id="id_cids" value="<?php echo $detail['cids'] ;?>"/>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 450}}, $.pdialog.getCurrent(), "");

</script>
