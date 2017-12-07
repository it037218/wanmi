<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>authority/addcolumn" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
         <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>所属分组：</dt>
                <dd>
                    <select name="group_name" id="group_name">
                        <?php if (!empty($group)): ?>
                            <?php foreach ($group AS $value): ?>
                                <option value="<?php echo $value['name']; ?>_<?php echo $value['id']?>" >&nbsp;└<?php echo $value['name']; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </dd>
                
                <dt>频道名称：</dt>
                <dd>
                    <input type="text" class="filed-text" name="name" id="name" maxlength="20" />
                </dd>

                <dt>频道URL：</dt>
                <dd>
                    <input type="text" class="filed-text" name="url" />
                </dd>

                <dt>频道状态：</dt>
                <dd>
                    <input type="radio" name="status" value="0" />关闭&nbsp;&nbsp;
                    <input type="radio" name="status" value="1" checked="checked" />打开
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
     $.pdialog.resizeDialog({style: {height: 350}}, $.pdialog.getCurrent(), "");
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }
</script>
