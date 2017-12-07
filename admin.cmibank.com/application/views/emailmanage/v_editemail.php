<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>emailmanage/editemail" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
                <dl>
                    <dt>公司名称:</dt>
                    <dd>
                        <select name="corid">
                            <?php foreach($corporation AS $key=>$value):?>
            				<option value="<?php echo $value['corid']?>" <?php if($value['corid'] == $detail['corid']){ echo 'selected';}?>><?php echo $value['cname']?></option>
            				 <?php endforeach;?>
            			</select>
                    </dd>
                </dl>
                <dl>
                    <dt>收件账户:</dt>
                    <dd>
                        <textarea style="width:100%;height:50px" name="address" id="address"><?php echo $detail['address']?></textarea>
                    </dd>
                </dl>
                <dl>
                    <dt>抄送账户:</dt>
                    <dd>
                        <textarea style="width:100%;height:50px" name="copyaddress" id="copyaddress"><?php echo $detail['copyaddress']?></textarea>
                    </dd>
                </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="editemail" />
                <input type="hidden" name="corname" value="<?php echo $detail['corname']?>" />
                <input type="hidden" name="corid" value="<?php echo $detail['corid']?>" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog()">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>
<script type="text/javascript">
     $.pdialog.resizeDialog({style: {width: 750}}, $.pdialog.getCurrent(), "");
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }
</script>
