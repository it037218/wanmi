
<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN?>backmoney/serviceMoney" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="100">
			<dl>
				<dt>服务费金额：</dt>
				<dd><input name="service_money" type="text" size="30" value="<?php echo $detail['service_money'];?>" class="number required"/>元</dd>
			</dl>
			<dl style="height: 150px;">
				<dt>上传服务费凭证：</dt>
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
                    <input type="text" name="service_image" id="service_image" value="<?php echo $detail['service_image'];?>" class="required" style="width: 400px;"/>
                    <a href='' id="_open_service_image" target="_blank"><img alt="图片预览" id="_show_service_image" src="<?php echo $detail['service_image'];?>" style="position: absolute;height:80px;width: 150px;right: 99px;top:76px;"/></a>
				</dd>
			</dl>
			<dl>
				<dt>服务费备注</dt>
				<dd><textarea name="service_note" cols="40" rows="4"><?php echo $detail['service_note'];?></textarea></dd>
			</dl>		
		</div>
		<div class="formBar">
			<ul>
				<input type="hidden" name="op" value="addServiceInfo"/>
				<input type="hidden" name="bid" value="<?php echo $detail['bid'];?>"/>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog()">保存</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 500}}, $.pdialog.getCurrent(), "");
     $.pdialog.resizeDialog({style: {width: 600}}, $.pdialog.getCurrent(), "");

     function uploadPicSuccess3(file, data, response){
         $('#_show_service_image').attr('src', data);
         $('#service_image').val(data);
         $('#_open_service_image').attr('href', data);
     }
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }
</script>
