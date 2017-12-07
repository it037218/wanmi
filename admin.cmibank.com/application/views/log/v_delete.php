<div id="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN; ?>log/delete" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>选择您要删除多久之前的操作记录：</dt>
                <dd>
                    <input type="text" name="stime" id="stime"  class="date" value="<?php echo date("Y-m-d", time()) ?>"/>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>                       
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
    
</div>
