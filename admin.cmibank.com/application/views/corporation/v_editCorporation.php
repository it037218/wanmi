
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>corporation/editcorporation" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>公司名称</dt>
                <dd>
                <input type="text" class="filed-text required" name="cname" value="<?php echo $detail['cname'];?>" />
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
                <input type="text" name="stamp" id="img" value="<?php echo $detail['stamp']?>" class="required" style="width: 300px;"/>
                <img alt="图片预览" id="_show_pic" src="<?php echo $detail['stamp']?>" style="position: absolute;height: 30px;width: 30px;right: 150px;top:10px;"/>
                </dd>
             </dl>
            <dl>    
                <dt>账户名称：</dt>
                <dd>
                    <input type="text" class="filed-text required" name="ccname" value="<?php       $num = 0; echo $detail['ccname'];?>"/>
                </dd>
            </dl>
            <dl>    
                <dt>账户号码：</dt>
                <dd>
                    <input type="text" class="filed-text required" name="ccard" value="<?php echo $detail['ccard'];?>"/>
                </dd>
            </dl>
            <dl>    
                <dt>银行名称：</dt>
                <dd>
                	<input type="text" class="filed-text required" name="bankname" value="<?php echo $detail['bankname'];?>"/>
                </dd>
            </dl>
            <dl>    
                <dt>所在省市：</dt>
			    <dd>
                    <select class="combox required" name="province"  ref="classid_group" refUrl="<?php echo OP_DOMAIN; ?>/corporation/cityInfo/{value}">
                        <option value="0" selected="" >请选择</option>
                        <?php foreach($province as $_province){?>
                        <option value="<?php echo $_province;?>" <?php if($_province == $detail['province']){ echo 'selected';}?>><?php echo $_province;?></option>
                        <?php } ?>
                    </select>
                    <select class="combox required" name="city" id="classid_group">
                        <option value="0" selected="" >请选择</option>
                        <?php foreach($subprovince as $_subprovince){?>
                        <option value="<?php echo $_subprovince;?>" <?php if($_subprovince == $detail['city']){ echo 'selected';}?>><?php echo $_subprovince;?></option>
                        <?php } ?>
                    </select>
                </dd>
            </dl>
            
            <dl>    
                <dt>开户支行：</dt>
                <dd>
                    <input type="text" class="filed-text required" name="subbank" value="<?php echo $detail['subbank'];?>"/>
                </dd>
            </dl>
            <dl>    
                <dt>行号：</dt>
                <dd>
                    <input type="text" class="filed-text required" name="banknum" value="<?php echo $detail['banknum'];?>"/>
                </dd>
            </dl>
            <dl>
                <dt>担保法人：</dt>
                <dd>
                    <input type="text" class="filed-text required" name="guar_corp" value="<?php echo $detail['guar_corp'];?>"/>
                </dd>
            </dl>
            <dl>
                <dt>担保人：</dt>
                <dd>
                    <input type="text" class="filed-text required" name="guarantee" value="<?php echo $detail['guarantee'];?>"/>
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
                    <?php if(empty($creditor)){$num ++?>
                        <tr class="<?php echo $num?>">
                            <td><input type="text" name="creditor[]" class="filed-text required" /></td>
                            <td><input type="text" name = "identity[]" class="filed-text required" /></td>
                            <td>  <input id="standardInputs" type="file" name="standard_files"
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
                                /><input type="text"  name = "seal[]" class="filed-text required" id="image0"/></td>
                            <td> <button type="button" onclick="addtr('1')">+</button></td>

                        </tr>
                    <?php }else{ ?>

                    <?php foreach($creditor as $k => $v){ $num ++;?>

                    <tr id="tr_<?php echo $v['id']?>" class="<?php echo $num?>">

                        <td>
                            <input type="hidden" name="id[]" class="filed-text required" value="<?php echo $v['id']?>" />
                            <input type="text" name="creditor[]" class="filed-text required" value="<?php echo $v['creditor']?>"/>
                        </td>
                        <td><input type="text" name = "identity[]" class="filed-text required" value="<?php echo $v['identity'] ?>"/></td>
                        <td>
                            <input id="standardInputs<?php echo $num?>" type="file" name="standard_files"
                                   uploaderOption="{
                        swf:'<?php echo STATIC_DOMAIN; ?>/admin/dwz/uploadify/scripts/uploadify.swf',
                        uploader:'<?php echo OP_DOMAIN; ?>/product/doUpload',
                        fileObjName:'titlepic_file',
                        formData:{'<?php echo session_name(); ?>': '<?php echo session_id(); ?>',upload_session:'1', ajax:1},
                        buttonText:'重新上传',
                        fileSizeLimit:'1024KB',
                        fileTypeDesc:'*.png;*.jpg;*.jpeg;',
                        fileTypeExts:'*.jpeg; *.jpg; *.png',
                        auto:true,
                        multi:true,
                        debug:true,
                        onUploadSuccess:function(file, data, response){

                            $('#image<?php echo $num?>').val(data);
                        },
                        onQueueComplete:uploadifyQueueComplete
                        }"
                            />
                            <input type="text"  name = "seal[]" class="filed-text required" value="<?php echo $v['seal'] ?>" id="image<?php echo $num?>"/></td>
                        <td>

                            <button type="button" onclick="addtr(<?php echo $v['id']?>)">+</button>
                            <?php if($k>0){?>   &nbsp;<button type="button"  onclick="deltr(<?php echo $v['id']?>,1)">-</button><?php }?>

                           </td>

                    </tr>
                    <?php }}?>
                </table>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="editcorporation" />
                <input type="hidden" name="corid" value="<?php echo $detail['corid']; ?>" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit" onclick="closedialog()">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function(){
        localStorage.removeItem("tr");
    });
    function addtr(){
        //第几个num
         var e = '<?php echo $num;?>';

        var f;
        var tr_f= localStorage.getItem("tr");
     
        if(tr_f==null){

            if(e!=""){
                f = Number(e)+1;
            }else{
                e = 1;
                f = 2;
            }

        }else{
            f = Number(tr_f)+1;
        }

        var tr = "<tr class='upload "+f+"' id='tr_"+f+"'><td>" +
            "<input type='hidden' name='id[]' value='0' class='filed-text required'/>" +
            "<input type='text' name='creditor[]' class='filed-text required'/></td>" +
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
        //alert(tr);
    }
    function deltr(e,f){
        if(f==1){
            $.ajax({
                url: '<?php echo OP_DOMAIN; ?>/contract/getContrctByCreid/',
                data:{creid:e},
                type:'post',
                dataType:'json',
                success:function(data){
                    if(data.code==1){
                        alert('该债权人不能删除')
                    }else if(data.code==2){
                        $("#tr_"+e).remove();
                        alert('从数据库删除成功')
                    }else{
                        alert('删除失败')
                    }
                }
            });
        }else{
            $("#tr_"+e).remove();
        }


    }
     $.pdialog.resizeDialog({style: {height: 530}}, $.pdialog.getCurrent(), "");
     function uploadifyQueueComplete(queueData){}
     function uploadPicSuccess(file, data, response){
         alert(response);
         $('#_show_pic').attr('src', data);
         $('#img').val(data);
     }
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }
</script>
