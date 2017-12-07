
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>klproductcontract/addKlproductcontract" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="57">
            <fieldset class="EditField"><legend>基本信息</legend>
                <dl>
                    <dt>公司名称</dt>
                    <dd>
                        <input type="text" class="filed-text required " name="lpcidname" id="lpcidname"/>
                    </dd>
                </dl>
                <dl>
                    <dt>预期收益</dt>
                    <dd>
                        <input type="text" class="filed-text required " min="1" name="income" id="income"/><span class="info">请输入一个大于1的数值</span>
                    </dd>
                </dl>
            </fieldset>
            <fieldset class="EditField"><legend>项目描述</legend>
                <dl>
                    <dt>描述:</dt>
                    <dd >
                        <textarea style="width:150%;height:200px" name="object_overview" id="object_overview"><?php echo $detail['object_overview'];?></textarea>
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">项目图片:</dt>
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
                    <input type="text" name="object_img" id="object_img" class="filed-text" style="width: 500px;"/>
                    <img alt="图片预览" id="_show_object"  style="position: absolute;height: 30px;width: 30px;right: 150px;top:10px;"/>
                    </dd>
                 </dl>
            </fieldset>
            <fieldset class="EditField"><legend>资金保障</legend>
                <dl>
                    <dt>描述:</dt>
                    <dd >
                        <textarea style="width:150%;height:200px" name="capital_overview" id="capital_overview"> </textarea>
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">资金保障图片:</dt>
                    <dd style="width:13%">
                        <input id="CapitalInput" type="file" name="Capital_file"
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
                    <input type="text" name="capital_img" id="capital_img" value="<?php echo $detail['capital_img'];?>" class="filed-text" style="width: 500px;"/>
                    <img alt="图片预览" id="_show_capital" src="<?php echo $detail['capital_img'];?>" style="position: absolute;height: 30px;width: 30px;right: 150px;top:10px;"/>
                    </dd>
                 </dl>
            </fieldset>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="lpcid" value="<?php echo $detail['lpcid'];?>" />
                <input type="hidden" name="op" value="addklproductcontract" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     function uploadifyQueueComplete(queueData){}
     function uploadPicSuccess2(file, data, response){
         $('#_show_object').attr('src', data);
         $('#object_img').val(data);
     }
     function uploadPicSuccess3(file, data, response){
         $('#_show_Capital').attr('src', data);
         $('#capital_img').val(data);
     }
</script>
