<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>reminders/remit" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>银行选择</dt>
                <dd>
                    <select class="required" name="bank_no" id="bank_no">
                        <option value="0">-请选择-</option>
                        <?php if(!empty($banklist)){ ?>
                            <?php foreach($banklist as $bank_no => $bank_name){ ?>
                                <option value="<?php echo $bank_name['fuiou_bank_code'] ;?>"><?php echo $bank_name['name']; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </dd>
            </dl>
            <dl>
                <dt>开户地</dt>
                <dd>
                    <select class="combox required" name="province" id ="province" ref="combox_city" refUrl="<?php echo OP_DOMAIN; ?>/reminders/getCity/{value}">
                        <option value="">-请选择-</option>
                        <?php if(!empty($province)){ ?>
                            <?php foreach($province as $name){ ?>
                                <option value="<?php echo $name['province_code'] ;?>"><?php echo $name['province_name']; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <select class="combox required" name="city" id="combox_city">
                        <option value="">所有城市</option>
                    </select>

                </dd>
            </dl>
            <dl>
                <dt>银行卡号</dt>
                <dd>
                    <input type="text" class="filed-text required" name="account_id" value='' required="required" minlength="8"/>
                </dd>
            </dl>
            <dl>
                <dt>账户名称</dt>
                <dd>
                    <input type="text" class="filed-text required" name="id_name" value='' required="required"/>
                </dd>
            </dl>
            <dl>
                <dt>金额</dt>
                <dd>
                    <input type="text" class="filed-text required" name="cost_money" value='' required="required"/>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="add" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog()">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 250}}, $.pdialog.getCurrent(), "");
     function closedialog(){
         $.pdialog.closeCurrent();
     }

</script>
