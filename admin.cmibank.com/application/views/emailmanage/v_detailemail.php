<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>emailmanage/addemail" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
                <dl>
                    <dt>公司名称:</dt>
                    <dd>
                       <?php echo $detail['corname']?>
                    </dd>
                </dl>
                <dl>
                    <dt>收件账户:</dt>
                    <dd>
                        <?php echo $detail['address']?>
                    </dd>
                </dl>
                <dl>
                    <dt>抄送账户:</dt>
                    <dd>
                        <?php echo $detail['copyaddress']?>
                    </dd>
                </dl>
        </div>
        <div class="formBar">
            <ul>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>
<script type="text/javascript">
     $.pdialog.resizeDialog({style: {width: 750}}, $.pdialog.getCurrent(), "");
</script>
