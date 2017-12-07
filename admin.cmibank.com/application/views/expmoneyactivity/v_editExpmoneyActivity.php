
<style>
.pageFormContent dl.nowrap dd, .nowrap dd {
    width: 400px;
}
</style>
<script type="text/javascript">

</script>
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>expmoneyactivity/editExpmoneyActivity" class="pageForm required-validate" onsubmit="return validateCallback(this,closedialog)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>活动名称：</dt>
                <dd>
                	<input type="text" class="filed-text required" name="name" value="<?php echo $detail['name'] ;?>"/>
                	<input type="hidden"  name="id" value="<?php echo $detail['id'] ;?>"/>
                </dd>
            </dl>	
            <dl>    
                <dt>活动类型：</dt>
                <dd>
                    <input name="type" type="hidden" value="1"/>注册赠送
                </dd>
            </dl>
            <dl>    
                <dt>体验金名称：</dt>
                <dd>
                    <input type="text" class="filed-text required" name="expname" id="id_expname" value="<?php echo $detail['expname'] ;?>"/>
                </dd>
            </dl>
            <dl>    
                <dt>体验金金额：</dt>
                <dd>
                    <input type="text" class="digits" name="money" id="id_money" value="<?php echo $detail['money'] ;?>"/>
                </dd>
            </dl>
            <dl>    
                <dt>活动时间：</dt>
                <dd>
	                <input type="text" id="id_stime"  name="stime"  class="date required" dateFmt="yyyy-MM-dd HH:mm:ss" value="<?php echo date('Y-m-d H:i:s',$detail['stime']) ;?>" mindate="<?php echo date('Y-m-d',time());?>"/><span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span><input type="text" class="date required" id="id_etime" name="etime" dateFmt="yyyy-MM-dd HH:mm:ss" value="<?php echo date('Y-m-d H:i:s',$detail['etime']) ;?>" mindate="<?php echo date('Y-m-d',time());?>"/>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="editexpmoneyactivity"/>
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 350}}, $.pdialog.getCurrent(), "");

	 function closedialog(json){
	  		$.pdialog.closeCurrent();	
	  		navTabAjaxDone(json);
 	 }
</script>
