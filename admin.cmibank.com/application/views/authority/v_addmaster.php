<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>authority/addgroupmaster" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
		<div class="pageFormContent nowrap" layoutH="97">
    		<dl>
    			<dd>
    			    <dt>
                                                            岗位名称
                    </dt>
                    <dt>
                    <input type="text" class="filed-text" name="groupType" maxlength="20"/>
                    </dt>
                </dd>  
                <dd>
                    <dt>
                                                            岗位名称
                    </dt>
                    <dt>
                    <input type="text" class="filed-text" name="name" maxlength="20"/>
                    </dt>
                    <input type="hidden" name="op" value="add" />
                </dd> 
            </dl>         
		</div>
        <div class="formBar">
            <ul>                       
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog()">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
   </form>
</div>
<!----
<script type="text/javascript" src="<?php echo STATIC_DOMAIN;?>common/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript">


var chkstrlen = function(str) {
    ///<summary>获得字符串实际长度，中文2，英文1</summary>
    ///<param name="str">要获得长度的字符串</param>
    var realLength = 0, len = str.length, charCode = -1;
    for (var i = 0; i < len; i++) {
        charCode = str.charCodeAt(i);
        if (charCode >= 0 && charCode <= 128){
            realLength += 1;
        } else {
            realLength += 2;
        }
    }
    return realLength;
}

</script>
--->
<script type="text/javascript">
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }
</script>
