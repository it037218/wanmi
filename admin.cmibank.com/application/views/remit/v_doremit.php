<style type="text/css">
    h2.contentTitle {text-align: left;}
    dd.left {text-align: left;}
    .EditField{
    	border:1px double #363636;
    }
    hr{
    	border-top:1px double #D1D1D1;
    }
</style>
<h2 class="contentTitle">给合作方打款</h2>
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>remit/doremit" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <fieldset class="EditField"><legend>产品信息</legend>
                <dl>
                    <dt>公司名称</dt>
                    <dd>
                        <?php echo $contract['corname']; ?>
                    </dd>
                </dl>
                <dl>
                    <dt>产品名称</dt>
                    <dd>
                        <?php echo $product['pname']; ?>
                    </dd>
                </dl>
                <dl>
                    <dt>募集资金</dt>
                    <dd>
                        <?php echo $product['money']; ?>
                    </dd>
                </dl>
                <dl>
                    <dt>已售资金</dt>
                    <dd>
                        <?php echo $product['sellmoney']; ?>
                    </dd>
                </dl>
                <dl>
                    <dt>打款金额</dt>
                    <dd>
                        <?php echo $product['sellmoney']; ?>
                    </dd>
                </dl>
            </fieldset>
            
            <fieldset class="EditField"><legend>打款信息</legend>
                <dl>    
                    <dt>账户名称</dt>
    			    <dd>
                        <?php echo $corporation['ccname']; ?>
                    </dd>
                </dl>
                <dl>    
                    <dt>账户号码</dt>
    			    <dd>
                        <?php echo $corporation['ccard']; ?>
                    </dd>
                </dl>
                <dl>
                    <dt>开户银行:</dt>
                    <dd>
                        <?php echo $corporation['bankname']; ?>
                    </dd>
                </dl>
                <dl>
                    <dt>开户支行:</dt>
                    <dd>
                        <?php echo $corporation['subbank']; ?>
                    </dd>
                </dl>
                <dl>
                    <dt>支行行号:</dt>
                    <dd>
                        <?php echo $corporation['banknum']; ?>
                    </dd>
                </dl>
                
            </fieldset>
            <fieldset class="EditField"><legend>实际打款</legend>
                <dl>    
                    <dt>原打款金额</dt>
    			    <dd>
                        <?php echo $product['sellmoney']; ?>
                    </dd>
                </dl>
                <dl>    
                    <dt>保证金比率</dt>
    			    <dd>
                      <?php echo $contract['con_bzjbl']; ?>%
                    </dd>
                </dl>
                <dl>
                    <dt>保证金金额:</dt>
                    <dd>
                        <?php 
                            echo $product['sellmoney']*$contract['con_bzjbl']/100;
                        ?>
                    </dd>
                </dl>
                <dl>
                    <dt>实际打款金额:</dt>
                    <dd>
                        <?php
                            echo $product['sellmoney'];
                        ?>
                    </dd>
                </dl>
            </fieldset>
            <fieldset class="EditField"><legend>打款凭证</legend>
            <dl>
                <dt>是否上传真实凭证</dt>
                <dd>
                    <label><input name="is_upload" type="radio" value="0" checked="checked"/>否 </label>
                    <label><input name="is_upload" type="radio" value="1"/>是 </label>  
                </dd>
            </dl>
            <dl>
                <dt style="width:80px;">上传图片:</dt>
                <dd style="width:13%">
                     <input id="standardInput" type="file" name="warrant_img_up"
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
                    <input type="text" name="warrant_img" id="img" value="" class="required" style="width: 500px;"/>
                </dd>
            </dl>
            <dl>
                <dt>打款时间:</dt>
                <dd>
                    <input type="text" name="ctime" value="<?php echo date('Y-m-d H:m:s', time()); ?>" class="date" dateFmt="yyyy-MM-dd HH:mm:ss"/>
                </dd>
            </dl>
            <dl>
                <dt>备注:</dt>
                <dd>
                    <input type="text" name="des" value=""/>
                </dd>
            </dl>
            </fieldset>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="pid" value="<?php echo $product['pid']; ?>" />
                <input type="hidden" name="op" value="doremit" />
                <input type="hidden" name="to" value="<?php echo $to; ?>" />
                <input type="hidden" name="cid" value="<?php echo $contract['cid']; ?>" />
                <input type="hidden" name="con_dkje" value="<?php echo $product['sellmoney']; ?>"/>
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     //$.pdialog.resizeDialog({style: {height: 500}}, $.pdialog.getCurrent(), "");
     
     function uploadifyQueueComplete(queueData){}
     function uploadPicSuccess(file, data, response){
         $('#_show_pic').attr('src', data);
         $('#img').val(data);
     }
     function uploadPicSuccess2(file, data, response){
         $('#_show_object').attr('src', data);
         $('#object_img').val(data);
     }
     function uploadPicSuccess3(file, data, response){
         $('#_show_Capital').attr('src', data);
         $('#capital_img').val(data);
     }
     function uploadPicSuccess4(file, data, response){
         $('#_show_borrower').attr('src', data);
         $('#borrower_img').val(data);
     }
</script>
