
<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN?>banner/editBanner" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="56">
			<dl>
				<dt>广告主题</dt>
				<dd><input name="title" type="text" size="30" value="<?php echo $detail['title'];?>"/></dd>
			</dl>
			  <dl style="height:65px">
			     <dt>图片：</dt>
			     <dd><input id="standardInput" type="file" name="standard_file"
                        uploaderOption="{
                        swf:'<?php echo STATIC_DOMAIN; ?>/admin/dwz/uploadify/scripts/uploadify.swf',
                        uploader:'<?php echo OP_DOMAIN; ?>/product/doUpload',
                        fileObjName:'titlepic_file',
                        formData:{'<?php echo session_name(); ?>': '<?php echo session_id(); ?>',upload_session:'1', ajax:1},
                        buttonText:'图片上传',
                        fileSizeLimit:'1024KB',
                        fileTypeDesc:'*.png;*.jpg;',
                        fileTypeExts:'*.png;*.jpg;',
                        auto:true,
                        multi:true,
                        debug:true,
                        onUploadSuccess:uploadPicSuccess,
                        onQueueComplete:uploadifyQueueComplete
                        }"
                    />
                    <input type="text" name="img" id="img" value="<?php echo $detail['img']?>" class="required" style="width: 300px;"/>
                    <img alt="图片预览" id="_show_pic" src="<?php echo $detail['img']?>" style="position: absolute;height: 30px;width: 30px;right: 57px;top:7px;"/>
                    </dd>
			  </dl>
			<dl>
				<dt>活动时间开始时间</dt>
				<dd><input type="text" name="startime" class="date"  dateFmt="yyyy-MM-dd HH:mm:ss" value="<?php echo $detail['startime']?>"></dd>
			</dl>
			<dl>
				<dt>活动结束时间型</dt>
				<dd><input type="text" class="date" name="endtime"  dateFmt="yyyy-MM-dd HH:mm:ss" value="<?php echo $detail['endtime']?>"></dd>
			</dl>
			<dl>
                    <dt>链接页面名称</dt>
                    <dd>
                         <input type="text" class="filed-text" id="uri_title" name="uri_title" value='<?php echo $detail['uri_title'] ?>' onchange="setTitle();"/>
                    </dd>
           </dl>
			<dl style="height:65px">
				<dt>链接地址</dt>
                 <dd>
	                    <input id="urlInput" type="file"/>
	                    <input type="text" name="uri" id="uri" value="<?php echo $detail['uri'] ?>" style="width: 300px;"/>
                </dd>
			</dl>
			<dl>
				<dt>显示位置</dt>
			    <dd><input name="location" value="<?php echo $detail['location'] ?>" type="text" size="30" class="required"/></dd>
			</dl>
			<dl>
				<dt>创建时间：</dt>
				<dd><input type="text" name="ctime" datefmt="yyyy-MM-dd HH:mm:ss" class="date" size="30" value="<?php echo date('Y-m-d H:i:s',$detail['ctime']);?>"/></dd>
			</dl>
			
		</div>
		<div class="formBar">
			<ul>
				<input type="hidden" name="op" value="editBanner"/>
				<input type="hidden" name="bid" value="<?php echo $detail['bid']?>"/>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog()">保存</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>
<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 530}}, $.pdialog.getCurrent(), "");
     function uploadifyQueueComplete(queueData){}
     function uploadPicSuccess(file, data, response){
         $('#_show_pic').attr('src', data);
         $('#img').val(data);
     }
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }

	 urltitle = '';
	 $(function() {
	     $('#urlInput').uploadify({
	     	'swf':'<?php echo STATIC_DOMAIN; ?>/admin/dwz/uploadify/scripts/uploadify.swf',
	         'uploader':'<?php echo OP_DOMAIN; ?>/product/doGenerateHtml',
	         'fileObjName':'titlepic_file',
	         'formData':{'<?php echo session_name(); ?>': '<?php echo session_id(); ?>','upload_session':'1', 'ajax':1},
	         'buttonText':'图片上传',
	         'fileSizeLimit':'1024KB',
	         'fileTypeDesc':'*.jpg;*.jpeg;*.gif;*.png;',
	         'fileTypeExts':'*.jpg;*.jpeg;*.gif;*.png;',
	         'auto':true,
	         'multi':true,
	         'debug':true,
	         'onUploadSuccess':uploadPicSuccess2,
	         'onUploadStart':settingTitle,
	         'onQueueComplete':uploadifyQueueComplete
	     });
	 });

	 function uploadPicSuccess2(file, data, response){
         $('#uri').val(data);
     }
     
	 function setTitle(){
	 	urltitle =  $('#uri_title').val();
	 }
	 function settingTitle(){
	 	$("#urlInput").uploadify("settings", "formData", { 'title': urltitle }); 
	 }
</script>

