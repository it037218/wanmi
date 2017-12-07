
<style>
.pageFormContent dl.nowrap dd, .nowrap dd {
    width: 400px;
}
</style>
<script type="text/javascript">

$(function() {
});

</script>
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>luckybagactivity/add" class="pageForm required-validate" onsubmit="return validateCallback(this,closedialog)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>活动名称：</dt>
                <dd>
                	<input type="text" class="filed-text required" name="name" />
                </dd>
            </dl>	
            <dl>    
                <dt>发放类型：</dt>
                <dd>
                    <table>
                    	<tr style="height: 25px">
                    	<td><input name="type" type="radio" id="type_radio" style="float:left" value="1"/>购买赠送</td>
                    	</tr>
                    </table>
                </dd>
            </dl>
            <dl>    
                <dt>起购金额：</dt>
                <dd>
                    <input type="text" class="digits" name="buymoney" id="id_buymoney"/>
                </dd>
            </dl>
            <dl>    
                <dt>赠送红包：</dt>
                <dd style="border: solid 1px #b8d0d6">
                	 <?php if(!empty($luckybagList)){?>
            			<?php foreach($luckybagList AS $key=>$value){?>
		                	<div style="height: 25px">
	                			<label style="width: 100%"><input type="radio" name="lid" value="<?php echo $value['id'] ;?>"/><?php echo $value['name'].'，适用于'.$value['pnames'];?></label>
		                	</div>
						<?php }?>
					<?php }?>
                </dd>
            </dl>
            <dl>    
                <dt>活动时间：</dt>
                <dd>
	                <input type="text" id="id_stime"  name="stime"  class="date required" dateFmt="yyyy-MM-dd HH:mm:ss" mindate="<?php echo date('Y-m-d',time());?>"/><span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span><input type="text" class="date required" id="id_etime" name="etime" dateFmt="yyyy-MM-dd HH:mm:ss" mindate="<?php echo date('Y-m-d',time());?>"/>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="add"/>
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
