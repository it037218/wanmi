
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>kltype/editKltype" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>类型名称</dt>
                <dd>
                <input type="text" class="filed-text" name="name" value="<?php echo $detail['name']; ?>" />
                </dd>
                <dt>权重</dt>
                <dd>
                <input type="text" class="filed-text" name="rank" value="<?php echo $detail['rank']; ?>" />
                </dd>
                
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="saveedit" />
                <input type="hidden" name="ptid" value="<?php echo $detail['ptid']; ?>" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 280}}, $.pdialog.getCurrent(), "");
</script>
