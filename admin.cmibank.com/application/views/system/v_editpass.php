
	<div class="pageContent">
		<div class="pageContent">
			<form action="<?php echo OP_DOMAIN;?>system/editpass" method="post" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
			<div class="pageFormContent nowrap" layoutH="97">
                <dl>
                    <dt>原密码：</dt>
                    <dd>
                        <input type="text" name="oldpass"  minlength='6',  maxlength="30" class="required"/>
                    </dd>
                </dl>
			    <dl>
                    <dt>新密码：</dt>
                    <dd>
                        <input type="text" name="newpass"  minlength='6',  maxlength="30" class="required"/>
                    </dd>
                </dl>
                <dl>
                    <dt>新密码：</dt>
                    <dd>
                        <input type="text" name="checkpass"  minlength='6',  maxlength="30" class="required"/>
                    </dd>
                </dl>
			     <input type="hidden" name="op" value="password" />
			</div>
			<div class="formBar">
            <ul>
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
			</form>
		</div>
	</div>


