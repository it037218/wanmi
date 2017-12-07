
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
	$('input:checkbox').click( function () {
		var cids ='';
		$('input:checkbox').each( function () {
			if(this.checked){
				cids = cids+$(this).val()+",";
			}
		});
		cids = cids.substring(0,cids.length-1);
		$('#id_cids').val(cids);
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
    <form method="post" action="<?php echo OP_DOMAIN;?>couponsend/editCouponSend" class="pageForm required-validate" onsubmit="return validateCallback(this,closedialog)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>活动名称：</dt>
                <dd>
                	<input type="text" class="filed-text required" name="name" value="<?php echo $detail['name'] ;?>"/>
                	<input type="hidden"  name="id" value="<?php echo $detail['id'] ;?>"/>
                </dd>
            </dl>	
            <dl>    
                <dt>发放用户：</dt>
                <dd>
                    <table>
                    	<tr style="height: 25px">
                    		<td><input name="type" type="radio" id="type_radio1" style="float:left" value="1" <?php if($detail['type'] == 1){ echo 'checked';}?>/>所有用户</td>
                    		<td><input name="type" type="radio" id="type_radio2" style="float:left" value="2" <?php if($detail['type'] == 2){ echo 'checked';}?>/>指定用户</td>
                    		<input type="hidden" id="id_type" class="filed-text required" value="<?php echo $detail['type'] ;?>"/>
                    	</tr>
                    </table>
                </dd>
            </dl>
            <dl>    
            	<dt>用户列表：</dt>
                <dd>
                	<textarea name="accounts" cols="63" rows="6" id="id_accounts"  class="required"><?php echo $detail['accounts'] ;?></textarea>
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
					<input type="hidden" name="cids" id="id_cids" value="<?php echo $detail['cids'] ;?>" class="filed-text required"/>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="editcouponsend"/>
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
