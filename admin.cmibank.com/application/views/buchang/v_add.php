
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>buchang/addbuchang" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="50">
                <dl>
                    <dt>申请类型</dt>
                    <dd>
                        <select name="btype" class="combox">
                            <option value="1">老用户买送奖励</option>
                            <option value="2">邀请奖励补发</option>
                            <option value="3">取现失败补偿</option>
                            <option value="4">充值失败补偿</option>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt style="width: 100%">申请人UID <span style="color: red">（多个可用英文,隔开）</span></dt>
                    <dd>
                        <input type="text" class="filed-text" name="uid"  placeholder="11036,11068,11021"/>
                    </dd>
                </dl>
                 <dl>
                    <dt>申请金额</dt>
                    <dd>
                        <input type="text" class="filed-text" name="money"/>
                    </dd>
                </dl>
                <dl>
                    <dt>备注</dt>
                    <dd>
                        <textarea style="width:200px;height:50px" name="desc" id="desc"></textarea>
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
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }
</script>

