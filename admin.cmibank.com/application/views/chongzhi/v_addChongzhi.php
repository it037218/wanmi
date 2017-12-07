
<style>
.pageFormContent dl.nowrap dd, .nowrap dd {
    width: 400px;
}
</style>

<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>chongzhi/addchongzhi" class="pageForm required-validate" onsubmit="return validateCallback(this,closedialog)">
        <div class="pageFormContent nowrap" layoutH="97">
       		 <dl>
                <dt>类型：</dt>
                <dd>
                	<select name="type">
					<option value="1">活期利息</option>
					<option value="2">运营</option>
					<option value="3">宝付手续费</option>
					<option value="4">富友手续费</option>
					</select>
                </dd>
            </dl> 
           	<dl style="height: 150px;">
				<dt>上传充值凭证：</dt>
				<dd>
				 <input id="service_image_Input" type="file" name="service_image_file"
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
                                onUploadSuccess:uploadPicSuccess3,
                                onQueueComplete:uploadifyQueueComplete
                            }"
                        />
                    <input type="text" name="service_image" id="service_image" class="required" style="width: 400px;"/>
                    <a href="" id="_chongzhi_image" target="_blank"><img alt="图片预览" id="_show_chongzhi_image" src="" style="position: absolute;height:80px;width: 150px;right: 99px;top:76px;"/></a>
				</dd>
			</dl>
            <dl>    
                <dt>充值金额：</dt>
                <dd>
                    <input type="text" class="required" name="money"/>
                </dd>
            </dl>
            <dl>    
                <dt>备注：</dt>
                <dd>
                	<textarea class="required" name="remark" cols="40" rows="5"></textarea>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="addchongzhi" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 500}}, $.pdialog.getCurrent(), "");
     $.pdialog.resizeDialog({style: {width: 600}}, $.pdialog.getCurrent(), "");

     function uploadPicSuccess3(file, data, response){
         $('#_show_chongzhi_image').attr('src', data);
         $('#service_image').val(data);
         $('#_chongzhi_image').attr('href', data);
     }
     
     function closedialog(json){
	  		$.pdialog.closeCurrent();	
	  		navTabAjaxDone(json);
	 }
</script>

