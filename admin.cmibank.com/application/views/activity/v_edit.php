<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>/activity_management/edit" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>用户UID</dt>
                <dd>
                    <input type="text" class="filed-text" name="uid" value="<?php echo $detail['uid']; ?>" disabled="disabled"/>
                </dd>
                <dt>手机号</dt>
                <dd>
                    <input type="text" class="filed-text" name="account" value="<?php echo $detail['account']; ?>" disabled="disabled"/>
                </dd>
                <dt>排行</dt>
                <dd>
                    <input type="text" class="filed-text" name="rank" value="<?php echo $detail['rank']; ?>" />
                </dd>
                <dt>投资金额</dt>
                <dd>
                    <input type="text" class="filed-text" name="money" value="<?php echo $detail['money']; ?>" />
                </dd>
                <dt>奖品</dt>
                <dd>
                    <input type="text" class="filed-text" name="prize" value="<?php echo $detail['prize']; ?>" />
                </dd>
                <dt>是否发送奖品</dt>
                <dd>
                    <input type="text" class="filed-text" name="is_prize" value="<?php echo $detail['is_prize']; ?>" />
                </dd>
                <dt>审核状态</dt>
                <dd>
                    <select name="status">
                        <option value="0" <?php if($detail['status'] == '0') {echo 'selected';}?>>未审核</option>
                        <option value="1" <?php if($detail['status'] == '1') {echo 'selected';}?>>已审核</option>
                    </select>
                </dd>
                <dt>备注</dt>
                <dd>
                    <textarea  rows="3" cols="20" name="description"><?php echo $detail['description']; ?></textarea>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="saveedit" />
                <input type="hidden" name="id" value="<?php echo $detail['id']; ?>" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog()" >提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
    $.pdialog.resizeDialog({style: {height: 280}}, $.pdialog.getCurrent(), "");
    function closedialog(){
        $.pdialog.closeCurrent();
    }
</script>
