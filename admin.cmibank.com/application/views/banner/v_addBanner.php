
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>banner/addBanner" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
                <dl>
                    <dt>banner类型</dt>
                    <dd>
                        <select name="type" class="combox">
                            <option value="0">活动Banner</option>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>广告主题</dt>
                    <dd>
                    <input type="text" class="filed-text" name="title" />
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">广告图片:</dt>
                    <dd style="width:13%">
                        <input id="standardInput" type="file" name="standard_file"
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
                            onUploadSuccess:uploadPicSuccess,
                            onQueueComplete:uploadifyQueueComplete
                            }"
                        />
                    <input type="text" name="img" id="img" value="" class="required" style="width: 500px;"/>
                    <img alt="图片预览" id="_show_pic" src="" style="position: absolute;height: 100px;width: 200px;left: 700px;top:10px;"/>
                    </dd>
                 </dl>
                 <dl>
                    <dt>活动时间</dt>
                    <dd>
                        <input type="text"  name="startime"  class="date" dateFmt="yyyy-MM-dd HH:mm:ss"/><span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span><input type="text" class="date" name="endtime" dateFmt="yyyy-MM-dd HH:mm:ss" />
                    </dd>
                </dl>
                <dl>
                    <dt>链接页面名称</dt>
                    <dd>
                         <input type="text" class="filed-text" id="uri_title" name="uri_title" value='0' onchange="setTitle();"/>
                    </dd>
                </dl>
                <dl>
                    <dt>链接地址</dt>
                    <dd>
	                    <input id="urlInput" type="file"/>
	                    <input type="text" name="uri" id="uri" value="" style="width: 500px;"/>
                    </dd>
                </dl>
                <dl>
                    <dt>显示位置</dt>
                    <dd>
                        <input type="text" class="filed-text" name="location" value='0' />
                    </dd>
                </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="addbanner" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
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

function setTitle(){
	urltitle =  $('#uri_title').val();
}
function settingTitle(){
	$("#urlInput").uploadify("settings", "formData", { 'title': urltitle }); 
}
     $.pdialog.resizeDialog({style: {height: 550}}, $.pdialog.getCurrent(), "");

     function uploadPicSuccess(file, data, response){
         $('#_show_pic').attr('src', data);
         $('#img').val(data);
     }
     function uploadPicSuccess2(file, data, response){
         $('#uri').val(data);
     }
</script>
