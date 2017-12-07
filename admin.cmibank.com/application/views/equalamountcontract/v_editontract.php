
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>equalamountcontract/editequalamountcontract" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="57">
            <fieldset class="EditField"><legend>基本信息</legend>
                <dl>
                    <dt>公司名称</dt>
                    <dd>
                        <input type="text" class="filed-text required " readonly=true name="corname" id="corname" value="<?php echo $detail['corname'];?>"/>
                    </dd>
                </dl>
                <dl>
                    <dt>合同编号:</dt>
                    <dd>
                        <input type="text" class="filed-text required" name="con_number" value="<?php echo $detail['con_number'];?>"/>
                    </dd>
                </dl>
                <dl>
                    <dt>合同金额:</dt>
                    <dd>
                        <input type="text" class="filed-text readonly" name="con_money" value="<?php echo $detail['con_money'];?>"/>
                    </dd>
                </dl>
                 <dl>
                    <dt>保证金比例:</dt>
                    <dd>
                        <input type="text" class="filed-text required" name="con_bzjbl"  id="con_bzjbl" value="<?php echo $detail['con_bzjbl'];?>"/>%
                    </dd>
                </dl>
                <dl>
                    <dt>用户合同模板:</dt>
                    <dd>
                        <select class="combox required" name="ucid" ref="classid_group" refUrl="<?php echo OP_DOMAIN; ?>/proftype/getprofnamebyproftype/{value}">
                            <option value="0" selected="" >请选择</option>
                            <?php foreach($usercontract as $_uc){?>
                            <option value="<?php echo $_uc['ucid'];?>"  <?php if($_uc['ucid'] == $detail['ucid']){echo 'selected';}?>><?php echo $_uc['tplname'].'-'.$_uc['tplnumber'];?></option>
                            <?php } ?>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>债权利率:</dt>
                    <dd>
                        <input type="text" class="filed-text required" name="con_income"  value="<?php echo $detail['con_income'];?>"/>%
                    </dd>
                </dl>
                <dl>
                    <dt>合同期限</dt>
                    <dd>
                        <input type="text" class="date required" name="interesttime" id="interesttime" value="<?php echo $detail['interesttime'];?>"/>
                        <span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span>
                        <input type="text" class="date required" name="repaymenttime" id="repaymenttime" onblur="count_u_diff_day()" value="<?php echo $detail['repaymenttime'];?>"/>
                        <span class="info" id="uisday">&nbsp;&nbsp;理财期限<font color="#FF0000"><?php echo $diff_day;?></font>天</span>
                    </dd>
                </dl>
                <dl>
                    <dt>每月还款日</dt>
                    <dd>
                        <input type="text" class="required readonly" name="repaymentday" id="repaymentday" readonly=true value="<?php echo $detail['repaymentday'];?>"/>
                    </dd>
                </dl>
            </fieldset>
            <fieldset class="EditField"><legend>项目描述</legend>
                <dl>
                    <dt>概述:</dt>
                    <dd >
                        <textarea style="width:150%;height:200px" name="object_overview" id="object_overview"><?php echo $detail['object_overview'];?></textarea>
                    </dd>
                </dl>
                <dl>
                    <dt>详述:</dt>
                    <dd >
                        <textarea style="width:150%;height:200px" name="object_desc" id="object_desc" ><?php echo $detail['object_desc'];?></textarea>
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
                    <input type="text" name="object_img" id="object_img" value="<?php echo $detail['object_img'];?>" class="filed-text" style="width: 500px;"/>
                    <img alt="图片预览" id="_show_object" src="<?php echo $detail['object_img'];?>" style="position: absolute;height: 30px;width: 30px;right: 150px;top:10px;"/>
                    </dd>
                 </dl>
            </fieldset>
            <fieldset class="EditField"><legend>资金保障</legend>
                <dl>
                    <dt>概述:</dt>
                    <dd >
                        <textarea style="width:150%;height:200px" name="capital_overview" id="capital_overview" ><?php echo $detail['capital_overview'];?></textarea>
                    </dd>
                </dl>
                <dl>
                    <dt>详述:</dt>
                    <dd >
                        <textarea style="width:150%;height:200px" name="capital_desc" id="capital_desc" ><?php echo $detail['capital_desc'];?></textarea>
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">资金保障图片:</dt>
                    <dd style="width:13%">
                        <input id="capitalInput" type="file" name="capital_file"
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
                <input type="hidden" name="cid" value="<?php echo $detail['cid'];?>" />
                <input type="hidden" name="op" value="editcontract" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 450}}, $.pdialog.getCurrent(), "");
     function count_u_diff_day(){
   	    var s_strtime = $('#interesttime').val();
   	    var e_strtime = $('#repaymenttime').val();
   	    if(e_strtime.length == 0){
  	    	return false;
   	    }
      	var repaymentday = e_strtime;
    	s_strtime = Date.parse(new Date(s_strtime.replace(/-/g, "/")));
      	e_strtime = Date.parse(new Date(e_strtime.replace(/-/g, "/")));
      	var diff_time = e_strtime - s_strtime;
      	var day = diff_time / 86400000+1;
      	var content = '<span class="info" id="uisday">&nbsp;&nbsp;理财期限<font color="#FF0000">' + day + '</font>天</span>';
      	$('#uisday').html(content);
      	repaymentday = repaymentday.toString().substring(8, 10);
      	$('#repaymentday').val(repaymentday);
     }

     function uploadifyQueueComplete(queueData){}
     function uploadPicSuccess2(file, data, response){
         $('#_show_object').attr('src', data);
         $('#object_img').val(data);
     }
     function uploadPicSuccess3(file, data, response){
         $('#_show_capital').attr('src', data);
         $('#capital_img').val(data);
     }
</script>
