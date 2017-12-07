
<style>
.pageFormContent dl.nowrap dd, .nowrap dd {
    width: 400px;
}
</style>
<script type="text/javascript">

$(function() {
	$('input:radio').click( function () {
		if($(this).val()==1){
			$("#id_stime").val("");
			$('#id_etime').val("");
			$('#id_etime').attr({ disabled : true });
			$('#id_stime').attr({ disabled : true });
			$('#id_days').attr({ disabled : false });
		}else{
			$('#id_days').val("");
			$('#id_days').attr({ disabled : true });
			$('#id_etime').attr({ disabled : false });
			$('#id_stime').attr({ disabled : false });
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
});

</script>
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>coupon/addcoupon" class="pageForm required-validate" onsubmit="return validateCallback(this,closedialog)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>代金券名称：</dt>
                <dd>
                	<input type="text" class="filed-text required" name="name" />
                </dd>
            </dl>	
            <dl>    
                <dt>代金券金额：</dt>
                <dd>
                    <input type="text" class="required" name="sendmoney" max="9999"/>
                </dd>
            </dl>
            <dl>    
                <dt>适用产品：</dt>
                <dd>
                    <dd style="border: solid 1px #b8d0d6;height: 75px">
                	<div style="height: 25px">
                		<label><input type="checkbox" name="ptid" value="41" title="1月旺"/>1月旺</label>
						<label><input type="checkbox" name="ptid" value="42" title="2月旺"/>2月旺</label>
						<label><input type="checkbox" name="ptid" value="40" title="3月旺"/>3月旺</label>
                	</div>
                	<div style="height: 25px"> 
	                    <label><input type="checkbox" name="ptid" value="43" title="6月旺"/>6月旺</label>
						<label><input type="checkbox" name="ptid" value="44" title="季度旺"/>季度旺</label>
						<label><input type="checkbox" name="ptid" value="45" title="新人旺"/>新人旺</label>
                	</div>
                	<div style="height: 25px"> 
	                    <label><input type="checkbox" name="ptid" value="46" title="6月旺"/>返利旺</label>
                	</div>
                	<input type="hidden" name="ptids" id="id_ptids" class="filed-text required"/>
                	<input type="hidden" name="pnames" id="id_pnames"/>
                	</dd>
                </dd>
            </dl>
            <dl>    
                <dt>至少购买金额：</dt>
                <dd>
                	<input type="text" class="digits required" name="minmoney" />
                </dd>
            </dl>
            <dl>    
                <dt>有效期：</dt>
                <dd style="height: 50px">
                	<div style="height: 25px">
                		<input type="radio" id="days_radio" style="float:left" name="type_name" value="1" checked/><input type="text" name="days" id="id_days"/>
                	</div>
                	<div style="height: 25px"> 
	                    <input type="radio" id="periods_radio" style="float:left" name="type_name" value="2"/><input type="text" id="id_stime"  name="stime"  class="date" dateFmt="yyyy-MM-dd" mindate="<?php echo date('Y-m-d',time());?>" disabled="disabled"/><span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span><input type="text" class="date" id="id_etime" name="etime" dateFmt="yyyy-MM-dd" disabled="disabled"/>
                	</div>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="addcoupon" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 450}}, $.pdialog.getCurrent(), "");

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
