<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>remindermail/sendemail" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <fieldset class="EditField">
                <dl>
                    <dt>收件人:</dt>
                    <dd>
                       <?php  if(isset($emailmanage['address'])){ echo $emailmanage['address'];}else{echo "请添加收件人地址";}?>
                    </dd>
                </dl>
                <dl>
                    <dt>抄送人:</dt>
                    <dd>
                    <?php  if(isset($emailmanage['copyaddress'])){ echo $emailmanage['copyaddress'];}else{echo "请添加抄送人地址";}?>
                    </dd>
                </dl>
                <dl>
                    <dt>主题:</dt>
                    <dd>
                       <input type="text" class="filed-text" name="subject" value=""/>
                    </dd>
                </dl>
            </fieldset>
            <fieldset class="EditField"><legend>邮件内容</legend>
                <dl>
                    <dt></dt>
                    <dd >
                        <textarea style="width:150%;height:400px" name="content" id="content"><?php echo $emailcontent?></textarea>
                    </dd>
                </dl>
              
            </fieldset>
            
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="sendemail" />
                <input type="hidden" name="bid" value="<?php echo $bid;?>" />
                <input type="hidden" name="address" value="<?php echo $emailmanage['address']?>" />
                <input type="hidden" name="copyaddress" value="<?php echo $emailmanage['copyaddress']?>" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">发送</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 800}}, $.pdialog.getCurrent(), "");
</script>
