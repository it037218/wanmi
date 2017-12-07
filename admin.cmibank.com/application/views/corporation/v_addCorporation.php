<script src="<?php echo STATIC_DOMAIN; ?>/admin/dwz/js/ajaxupload.js"></script>

<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>corporation/addcorporation" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>公司名称</dt>
                <dd>
                <input type="text" class="filed-text required" name="cname" />
                </dd>
            </dl>
            <dl>
                <dt style="width:80px;">印章图片:</dt>
                <dd style="width:13%">
                    <input id="standardInput" type="file" name="standard_file"
                        uploaderOption="{
                        swf:'<?php echo STATIC_DOMAIN; ?>/admin/dwz/uploadify/scripts/uploadify.swf',
                        uploader:'<?php echo OP_DOMAIN; ?>/product/doUpload',
                        fileObjName:'titlepic_file',
                        formData:{'<?php echo session_name(); ?>': '<?php echo session_id(); ?>',upload_session:'1', ajax:1},
                        buttonText:'图片上传',
                        fileSizeLimit:'1024KB',
                        fileTypeDesc:'*.png;',
                        fileTypeExts:'*.png;',
                        auto:true,
                        multi:true,
                        debug:true,
                        onUploadSuccess:uploadPicSuccess,
                        onQueueComplete:uploadifyQueueComplete
                        }"
                    />
                <input type="text" name="stamp" id="img" value="" class="required" style="width: 300px;"/>
                <img alt="图片预览" id="_show_pic" src="" style="position: absolute;height: 30px;width: 30px;right: 150px;top:10px;"/>
                </dd>
             </dl>
            <dl>    
                <dt>账户名称：</dt>
                <dd>
                    <input type="text" class="filed-text required" name="ccname" />
                </dd>
            </dl>
            <dl>    
                <dt>账户号码：</dt>
                <dd>
                    <input type="text" class="filed-text required" name="ccard" />
                </dd>
            </dl>
            <dl>    
                <dt>银行名称：</dt>
                <dd>
                	<input type="text" class="filed-text required" name="bankname" />
                </dd>
            </dl>
            <dl>    
                <dt>所在省市：</dt>
			    <dd>
                    <select class="combox required" name="province"  ref="classid_group" refUrl="<?php echo OP_DOMAIN; ?>/corporation/cityInfo/{value}">
                        <option value="0" selected="" >请选择</option>
                        <?php foreach($province as $_province){?>
                        <option value="<?php echo $_province;?>" ><?php echo $_province;?></option>
                        <?php } ?>
                    </select>
                    <select class="combox required" name="city" id="classid_group"></select>
                </dd>
            </dl>
            
            <dl>    
                <dt>开户支行：</dt>
                <dd>
                    <input type="text" class="filed-text required" name="subbank" />
                </dd>
            </dl>
            <dl>    
                <dt>行号：</dt>
                <dd>
                    <input type="text" class="filed-text required" name="banknum" />
                </dd>
            </dl>
            <dl>
                <dt>担保法人：</dt>
                <dd>
                    <input type="text" class="filed-text required" name="guar_corp"/>
                </dd>
            </dl>
            <dl>
                <dt>担保人：</dt>
                <dd>
                    <input type="text" class="filed-text required" name="guarantee"/>
                </dd>
            </dl>
            <dl>
                <table>
                    <tr>
                        <td>债权人</td>
                        <td>债权人身份号码/营业执照号</td>
                        <td>债权人印章</td>
                        <td>操作</td>
                    </tr>
                    <tr class="1 upload">
                        <td><input type="text" name="creditor[]" class="filed-text required"/></td>
                        <td><input type="text" name = "identity[]" class="filed-text required"/></td>
                        <td>
                            <input id="standardInputs" type="file" name="standard_files"
                                   uploaderOption="{
                        swf:'<?php echo STATIC_DOMAIN; ?>/admin/dwz/uploadify/scripts/uploadify.swf',
                        uploader:'<?php echo OP_DOMAIN; ?>/product/doUpload',
                        fileObjName:'titlepic_file',
                        formData:{'<?php echo session_name(); ?>': '<?php echo session_id(); ?>',upload_session:'1', ajax:1},
                        buttonText:'印章上传',
                        fileSizeLimit:'1024KB',
                        fileTypeDesc:'*.png;*.jpg;*.jpeg;',
                        fileTypeExts:'*.png;*.jpeg;*.jpg;',
                        auto:true,
                        multi:true,
                        debug:true,
                        onUploadSuccess:function(file, data, response){

                            $('#image0').val(data);
                        },
                        onQueueComplete:uploadifyQueueComplete
                        }"
                            />
                            <input type="text" name="seal[]" id="image0" value="" class="required" />


                        </td>

                        <td><button type="button" onclick="addtr('1')">+</button> &nbsp;<button type="button" onclick="deltr(this)">-</button></td>

                    </tr>

                </table>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="addcorporation" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog()">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
    function addtr(e){
        //cookie
        var f; //第几个tr
        var tr_f= (localStorage.getItem("tr") );
        if(tr_f==null){
             f = 2;
        }else{
            f = Number(tr_f)+1;
        }

     //   alert($(this).prop('type'));
    var tr = "<tr class='upload "+f+"' id='tr_"+f+"'><td><input type='text' name='creditor[]' class='filed-text required'/></td>" +
        "<td><input type='text' name = 'identity[]' class='filed-text required'/></td>"+
        "<td> " +
        "<input type='file' name='seal[]' id='standardInput"+f+"'  class='required' /> " +
        "<input type='text' name='seal[]' id='image"+f+"'  class='required' />" +


//        "<td> <input id='standardInput"+f+"' type='file' name='seals' " +
//        "/> </td>"+
        "<td><button type='button' onclick='addtr("+f+")'>+</button> &nbsp;<button type='button' onclick='deltr("+f+")'>-</button></td> </tr>";
        $("."+e).after(tr);
        //设置tr
        localStorage.setItem('tr',f);
        $("#standardInput"+f).uploadify(
            {
                swf:'<?php echo STATIC_DOMAIN; ?>/admin/dwz/uploadify/scripts/uploadify.swf',
                uploader:'<?php echo OP_DOMAIN; ?>/product/doUpload',
                fileObjName:'titlepic_file',
                formData:{'<?php echo session_name(); ?>': '<?php echo session_id(); ?>',upload_session:'1', ajax:1},
                buttonText:'印章上传',
                fileSizeLimit:'1024KB',
                fileTypeDesc:'*.png;*.jpg;*.jpeg;',
                fileTypeExts:'*.png;*.jpeg;*.jpg;',
                auto:true,
                multi:true,
                debug:true,
                onUploadSuccess:function(file, data, response){

                    $('#image'+f).val(data);
                },
                onQueueComplete:uploadifyQueueComplete
            }
        );

    }


    function deltr(e){
        $("#tr_"+e).remove();

    }
    $.pdialog.resizeDialog({style: {height: 600}}, $.pdialog.getCurrent(), "");

     function uploadifyQueueComplete(queueData){}
     function uploadPicSuccess(file, data, response){
         $('#_show_pic').attr('src', data);
         $('#img').val(data);
     }
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }

    $(function(){
        localStorage.removeItem("tr");
        //获取上级上级的id
//        $("#standardInput0").uploadify(
//            {
//                swf:'<?php //echo STATIC_DOMAIN; ?>///admin/dwz/uploadify/scripts/uploadify.swf',
//                uploader:'<?php //echo OP_DOMAIN; ?>///product/doUpload',
//                fileObjName:'seals',
//                formData:{'<?php //echo session_name(); ?>//': '<?php //echo session_id(); ?>//',upload_session:'1', ajax:1},
//                buttonText:'图片上传',
//                fileSizeLimit:'1024KB',
//                fileTypeDesc:'*.png;',
//                fileTypeExts:'*.png;',
//                auto:true,
//                multi:true,
//                debug:true,
//                onUploadSuccess:uploadPicSuccess,
//                onQueueComplete:uploadifyQueueComplete
//            }
//        );

    })
</script>
