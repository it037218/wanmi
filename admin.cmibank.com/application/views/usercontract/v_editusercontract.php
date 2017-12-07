<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>usercontract/editusercontract" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>模板名称</dt>
                <dd>
                    <input type="text" class="filed-text required" name="tplname" id="tplname" value="<?php echo $detail['tplname']?>"/>
                </dd>
            </dl>
            <dl>
                <dt>模板页面名称:</dt>
                <dd>
                    <input type="text" class="filed-text required" name="tpl_pagename"  value="<?php echo $detail['tpl_pagename']?>"/>
                </dd>
            </dl>
            <dl>
                <dt>模板页面链接:</dt>
                <dd>
                    <input type="text" class="filed-text required" name="tpllink"  value="<?php echo $detail['tpllink']?>"/>
                </dd>
            </dl>
            <dl>
                <dt>模板编号:</dt>
                <dd>
                    <input type="text" class="filed-text" name="tplnumber" value="<?php echo $detail['tplnumber']?>"/>
                </dd>
            </dl>
            <dl>    
                <dt>业务类型：</dt>
			    <dd>
                    <select class="combox required" name="proftype" ref="classid_group" refUrl="<?php echo OP_DOMAIN; ?>/proftype/getprofnamebyproftype/{value}">
                        <option value="0" selected="" >请选择</option>
                        <?php foreach($proftypelist as $_proftype){?>
                        <option value="<?php echo $_proftype['proftype'];?>" <?php if($_proftype['proftype'] == $currect_proftype['proftype']){ echo 'selected';}?> ><?php echo $_proftype['proftype'];?></option>
                        <?php } ?>
                    </select>
                    <select class="combox required" name="profid" id="classid_group">
                    
                        <?php foreach($profnamelist as $_value){?>
                        <option value="<?php echo $_value['profid'];?>" <?php if($_value['profname'] == $currect_proftype['profname']){ echo 'selected';}?> ><?php echo $_value['profname'];?></option>
                        <?php } ?>
                    </select>
                </dd>
                <dl>
                <hr />
                <div style="width:1000px;top:40px;right:0" layoutH="80">
<!--					<iframe width="100%" height="430" class="share_self"  frameborder="0" scrolling="yes" src="--><?php //echo OP_DOMAIN; ?><!--/usercontract/showtpl/--><?php //echo $detail['tpl_pagename']?><!--">-->
<!--                        -->
<!--                    </iframe>-->
                    <iframe width="100%" height="430" class="share_self"  frameborder="0" scrolling="yes" src="<?php echo $detail['tpllink']?>?ucid=<?php echo $detail['ucid']?>">

                    </iframe>
				</div>
                </dl>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="editusercontract" />
                <input type="hidden" name="ucid" value="<?php echo $detail['ucid'];?>" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 450}}, $.pdialog.getCurrent(), "");
</script>
