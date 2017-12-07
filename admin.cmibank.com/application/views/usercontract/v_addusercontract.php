<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>usercontract/addusercontract" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>模板名称</dt>
                <dd>
                    <input type="text" class="filed-text required" name="tplname" id="tplname" />
                </dd>
            </dl>
            <dl>
                <dt>模板页面名称:</dt>
                <dd>
                    <input type="text" class="filed-text required" name="tpl_pagename"  />
                </dd>
            </dl>
            <dl>
                <dt>模板编号:</dt>
                <dd>
                    <input type="text" class="filed-text" name="tplnumber" />
                </dd>
            </dl>
            <dl>    
                <dt>业务类型：</dt>
			    <dd>
                    <select class="combox required" name="proftype" ref="classid_group" refUrl="<?php echo OP_DOMAIN; ?>/proftype/getprofnamebyproftype/{value}">
                        <option value="0" selected="" >请选择</option>
                        <?php foreach($proftypelist as $_proftype){?>
                        <option value="<?php echo $_proftype['proftype'];?>" ><?php echo $_proftype['proftype'];?></option>
                        <?php } ?>
                    </select>
                    <select class="combox required" name="profid" id="classid_group"></select>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="addusercontract" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog()">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>
<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 450}}, $.pdialog.getCurrent(), "");
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }
</script>
