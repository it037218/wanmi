
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>klproduct/uptoline" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>选择产品发布日期</dt>
                <dd>
                <label><input type="radio" name="odate" checked="checked" value="1" />当日</label>
                <label><input type="radio" name="odate" value="2" />次日</label>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="online" />
                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                <input type="hidden" name="ptid" value="<?php echo $ptid; ?>" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 200}}, $.pdialog.getCurrent(), "");
</script>
