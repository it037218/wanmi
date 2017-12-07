
<style>
.pageFormContent dl.nowrap dd, .nowrap dd {
    width: 400px;
}
</style>
<script type="text/javascript">
$(function() {
	$('#id_money').attr({ disabled : true });
	$('#id_goumaimoney').attr({ disabled : true });
	$("input[name='type_name']").click( function () {
		if($(this).val()==1){
			$('#id_bili').val("");
			$('#id_bili').attr({ disabled : true });
			$('#id_money').attr({ disabled : false });
		}else{
			$('#id_money').val("");
			$('#id_money').attr({ disabled : true });
			$('#id_bili').attr({ disabled : false });
		}
	});
	$("input[name='type_jihuo']").click( function () {
		if($(this).val()==1){
			$('#id_goumaibeishu').val("");
			$('#id_goumaibeishu').attr({ disabled : true });
			$('#id_goumaimoney').attr({ disabled : false });
		}else{
			$('#id_goumaimoney').val("");
			$('#id_goumaimoney').attr({ disabled : true });
			$('#id_goumaibeishu').attr({ disabled : false });
		}
	});
	$('input:checkbox').click( function () {
		var ptids ='';
		var ptnames='';
		$('input:checkbox').each( function () {
			if(this.checked){
				ptids = ptids+$(this).val()+",";
				ptnames = ptnames+$(this).attr('title')+",";
			}
		});
		ptids = ptids.substring(0,ptids.length-1);
		ptnames = ptnames.substring(0,ptnames.length-1);
		$('#id_ptids').val(ptids);
		$('#id_pnames').val(ptnames);
	});
	var cids=$('#id_ptids').val();
	$('input:checkbox').each( function () {
		if(cids.indexOf(this.id)>=0){
			this.checked=true;
		}
	});
});

</script>
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>luckybag/edit" class="pageForm required-validate" onsubmit="return validateCallback(this,closedialog)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>名称：</dt>
                <dd>
                	<input type="text" class="filed-text required" name="name" value="<?php echo $detail['name']?>"/>
                	<input name="id" value="<?php echo $detail['id'] ;?>" style="visibility: hidden;"/>
                </dd>
            </dl>	
            <dl>    
                <dt>红包金额：</dt>
                <dd style="height: 50px;line-height: 30px">
	                    <label><input type="radio" name="type_name"  id="type_bl"  value="2" <?php echo $detail['type']==1?'': 'checked';?>/>购买金额</label><input type="text" class="number" name="bili" id="id_bili" value="<?php echo $detail['bili']?>"  max="5"/>%
                		<label><input type="radio" name="type_name" id="type_zd"  value="1" <?php echo $detail['type']==2?'': 'checked';?>/>指定金额</label><input type="text" class="digits" name="money" id="id_money" value="<?php echo $detail['money']?>"  max="50"/></br>元
                </dd>
            </dl>
            <dl>    
                <dt>激活条件：</dt>
                <dd style="height: 50px">
                	<div style="height: 25px"> 
	                    <label><input type="radio" name="type_jihuo"  id="type_bl" value="2" <?php echo $detail['usetype']==1?'': 'checked';?>/>红包金额倍数</label><input type="text" class="digits" name="goumaibeishu" id="id_goumaibeishu" value="<?php echo $detail['goumaibeishu']?>"/>倍
                	</div>
                	<div style="height: 25px">
                		<label><input type="radio" name="type_jihuo" id="type_zd" value="1" <?php echo $detail['usetype']==2?'': 'checked';?>/>指定金额</label><input type="text" class="digits" name="goumaimoney" id="id_goumaimoney" value="<?php echo $detail['goumaimoney']?>"/>元
                	</div>
                </dd>
            </dl>
            <dl>    
                <dt>适用产品：</dt>
                <dd>
                    <dd style="border: solid 1px #b8d0d6;height: 75px">
                	<div style="height: 25px">
                		<label><input type="checkbox" name="ptid" value="41" title="1月旺" id="41"/>1月旺</label>
						<label><input type="checkbox" name="ptid" value="42" title="2月旺" id="42"/>2月旺</label>
						<label><input type="checkbox" name="ptid" value="40" title="3月旺" id="40"/>3月旺</label>
                	</div>
                	<div style="height: 25px"> 
	                    <label><input type="checkbox" name="ptid" value="43" title="6月旺" id="43"/>6月旺</label>
						<label><input type="checkbox" name="ptid" value="44" title="季度旺" id="44"/>季度旺</label>
						<label><input type="checkbox" name="ptid" value="45" title="新人旺" id="45"/>新人旺</label>
                	</div>
                	<div style="height: 25px"> 
	                    <label><input type="checkbox" name="ptid" value="46" title="返利旺" id="46"/>返利旺</label>
                	</div>
                	<input type="hidden" name="ptids" id="id_ptids" value="<?php echo $detail['ptids'] ;?>"  class="filed-text required"/>
                	<input type="hidden" name="pnames" id="id_pnames" value="<?php echo $detail['pnames'] ;?>"/>
                	</dd>
                </dd>
            </dl>
            <dl>    
                <dt>有效期：</dt>
                <dd style="height: 50px">
                	<input type="text" name="days" id="id_days" class="digits required" value="<?php echo $detail['days']?>"/>天
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="edit" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 600}}, $.pdialog.getCurrent(), "");
     $.pdialog.resizeDialog({style: {top: 100}}, $.pdialog.getCurrent(), "");
     function uploadifyQueueComplete(queueData){}
     function uploadPicSuccess(file, data, response){
         $('#_show_pic').attr('src', data);
         $('#img').val(data);
     }
	 
	 function closedialog(json){
	  		$.pdialog.closeCurrent();	
	  		navTabAjaxDone(json);
 	 }
</script>
