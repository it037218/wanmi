<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN;?>stockproduct/StockLongmoney" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone)">
		<div class="pageFormContent" layoutH="56">
			<p>
				<label>合同剩余金额:</label>
				<?php echo $shengyu_money;?>
			</p>
			<p>
				<label>活期可使用金额：</label>
				<?php echo $longmoney;?>
			</p>

			<p>
				<label>采购金额：</label>
				<input type="text"  value="" name="stockmoney" class="textInput">
			</p>
			<p>
				<label>截止日期：</label>
				<?php echo $repaymenttime;?>
			</p>
			<p>
			     <label>备注</label>
			     <input type="text" name="des" value="" class="textInput valid">
			</p>
			<p>
			<input id="standardInput" type="file" name="warrant_img_up"
                    uploaderOption="{
                    swf:'<?php echo STATIC_DOMAIN; ?>/admin/dwz/uploadify/scripts/uploadify.swf',
                    uploader:'<?php echo OP_DOMAIN; ?>/product/doUpload',
                    fileObjName:'titlepic_file',
                    formData:{'<?php echo session_name(); ?>': '<?php echo session_id(); ?>',upload_session:'1', ajax:1},
                    buttonText:'上传凭证',
                    fileSizeLimit:'1024KB',
                    fileTypeDesc:'*.jpg;*.jpeg;*.gif;*.png;',
                    fileTypeExts:'*.jpg;*.jpeg;*.gif;*.png;',
                    auto:true,
                    multi:true,
                    debug:true,
                    onUploadSuccess:uploadPicSuccess,
                    onQueueComplete:uploadifyQueueComplete
                    }"
                />
                <input type="text" name="warrant_img" id="img" value="" class="required" style="width: 500px;"/>
                </p>
               
            
		</div>
		<div class="formBar">
			<ul>
			    <input type="hidden" name="op" value="stock" />
                <input type="hidden" name="cid" value="<?php echo $cid; ?>" />
                <input type="hidden" name="money" value="<?php echo $shengyu_money;?>"/>
				<input type="hidden" name="can_user_money" value="<?php echo $longmoney;?>"/>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog();">保存</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>
<script type="text/javascript">
function closedialog(){
	$.pdialog.closeCurrent();	
}
function uploadifyQueueComplete(queueData){}
function uploadPicSuccess(file, data, response){
    $('#_show_pic').attr('src', data);
    $('#img').val(data);
}
</script>

