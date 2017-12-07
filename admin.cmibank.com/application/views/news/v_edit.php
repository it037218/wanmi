
<style>
.pageFormContent dl.nowrap dd, .nowrap dd {
    width: 400px;
}
</style>
<script type="text/javascript">

$(function() {
	$('input:radio').click( function () {
		if($(this).val()==1){
			$("#id_accounts").val("");
			$('#id_accounts').attr({ disabled : true });
		}else{
			$('#id_accounts').attr({ disabled : false });
		}
		$('#id_type').val($(this).val());
	});
});

</script>
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>news/editNews" class="pageForm required-validate" onsubmit="return validateCallback(this,closedialog)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>主题：</dt>
                <dd>
                	<input type="text" class="filed-text required" name="title" value='<?php echo $detail['title'] ;?>' style="width:80%"/>
                </dd>
            </dl>	
            <dl>
                    <dt style="width:80px;">图片:</dt>
                    <dd style="width:13%">
                        <input id="objectInput" type="file" name="object_file"
                            uploaderOption="{
                                swf:'<?php echo STATIC_DOMAIN; ?>/admin/dwz/uploadify/scripts/uploadify.swf',
                                uploader:'<?php echo OP_DOMAIN; ?>/product/doUpload',
                                fileObjName:'titlepic_file',
                                formData:{'<?php echo session_name(); ?>': '<?php echo session_id(); ?>',upload_session:'1', ajax:1},
                                buttonText:'图片上传',
                                fileSizeLimit:'1024KB',
                                fileTypeDesc:'*.jpg;*.jpeg;*.gif;*.png;',
                                fileTypeExts:'*.jpg;*.jpeg;*.gif;*.png;',
                                auto:true,
                                multi:true,
                                debug:true,
                                onUploadSuccess:uploadPicSuccess2,
                                onQueueComplete:uploadifyQueueComplete
                            }"
                        />
                    <input type="text" name="img" id="object_img" value="<?php echo $detail['img'];?>"  class="filed-text" style="width: 500px;"/>
                    <img alt="图片预览" id="_show_object" src="<?php echo $detail['img'];?>" style="position: absolute;height: 30px;width: 30px;right: 150px;top:10px;"/>
                    </dd>
            </dl>
            <dl>
                <dt>消息连接：</dt>
                <dd>
                	<input type="text" class="filed-text" name="link" value='<?php echo $detail['link'] ;?>'  style="width:80%"/>
                </dd>
            </dl>	
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="editnews"/>
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 450}}, $.pdialog.getCurrent(), "");

     function closedialog(json){
   		$.pdialog.closeCurrent();	
   		navTabAjaxDone(json);
   	 }
</script>
