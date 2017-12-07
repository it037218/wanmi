
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN; ?>authority/modify/<?php echo $user_id;?>" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">  
		<div class="pageFormContent nowrap" layoutH="97">
		<dl>
			<dd>
                <select class="combox required" name="class_son_id"  ref="classid_group" refUrl="<?php echo OP_DOMAIN; ?>/authority/getGroupSons/{value}">

                    <option value="0"selected="" >请选择父类</option>

                    <?php foreach($group as $key=>$value){?>
                    <option value="<?php echo $value['inner_group'];?>" <?php if($value['inner_group']==$groupInfo['inner_group']):?>selected="selected"<?php endif;?>>&nbsp;&nbsp;└<?php echo $value['inner_group'];?></option>
                    <?php } ?>
                </select>

                <select class="combox required" name="classid" id="classid_group">
                    <option value="0"selected="" >请选择子类</option>
                    <?php foreach($groupSons as $key=>$val){?>
                    <option value="<?php echo $val[0];?>" <?php if($groupInfo['name']== $val[1]){?>selected="" <?php }?>><?php echo $val[1];?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="op" value="modify" />
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

