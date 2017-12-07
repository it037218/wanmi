<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>contract/addcontract" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <fieldset class="EditField"><legend>基本信息</legend>
                <dl>
                    <dt>公司名称</dt>
                    <dd>
<!--                        改成下拉框的-->
                        <select id="corid" name="corid">
                            <option>
                                请选择
                            </option>
                            <?php foreach($company as $k=>$v){?>
                            <option value="<?php echo $v['corid']?>"><?php echo $v['cname']?></option>
                            <?php }?>
                        </select>
<!--                        <input type="text" class="filed-text required" name="cname" id="cname"/><span onclick="getcnnamelistBycname()" class="info">&nbsp;&nbsp;&nbsp;&nbsp;点击提示</span>-->
                    </dd>
                    <?php if(!empty($creditorList)){?>

                        <dl id="cre" style="display: none">
                            <dt>债权人</dt>
                            <dd>

                                <select id="creditor" name="creid">
                                    <option>请选择</option>
                                    <?php foreach($creids as $key => $value){?>
                                        <option value="<?php echo $value['id']?>" <?php if($value['id']==$detail['creid']){echo 'selected=selected';}?>>
                                            <?php echo $value['creditor']?>
                                        </option>
                                    <?php }?>
                                </select>
                            </dd>
                        </dl>

                        <dl id="seal_dl" style="display: none">
                            <dt>债权人印章</dt>
                            <td> <input  value = "<?php if(isset($creditor['seal'])){echo $creditor['seal'];}?>" type="text" class="filed-text  creditor_f"  id="seal" /></td>
                        </dl>
                        <dl id="identity_dl" style="display: none">
                            <dt>身份证号/营业执照号</dt>
                            <dd>
                                <input  value = "<?php if(isset($creditor['identity'])){echo $creditor['identity'];}?>" type="text" class="filed-text  creditor_f" id="identity" />
                            </dd>
                        </dl>

                    <?php }?>
                </dl>

                 <dl>
                    <dt>公司名称提示:</dt>
                    <dd >
                     <div id="tishi" name="tishi"></div>
                    </dd>
                </dl>
                <dl>
                    <dt>真实借款人:</dt>
                    <dd>
                        <input type="text" class="filed-text required" name="mortgagor" id="mortgagor"/>
                    </dd>
                </dl>
                <dl>
                    <dt>合同编号:</dt>
                    <dd>
                        <input type="text" class="filed-text required" name="con_number" id="con_number"/>
                    </dd>
                </dl>
                <dl>
                    <dt>合同金额:</dt>
                    <dd>
                        <input type="text" class="filed-text required" name="con_money"  id="con_money" onblur="changnumber()"/><span class="info" id="num"></span>
                    </dd>
                </dl>
                <dl>
                    <dt>保证金比例:</dt>
                    <dd>
                        <input type="text" class="filed-text required" name="con_bzjbl"  id="con_bzjbl" readonly="readonly"/>%
                    </dd>
                </dl>
                <dl>
                    <dt>用户合同模板:</dt>
                    <dd>
                        <select class="combox required" name="ucid" ref="classid_group" refUrl="<?php echo OP_DOMAIN; ?>/proftype/getprofnamebyproftype/{value}">
                            <option value="0" selected="" >请选择</option>
                            <?php foreach($usercontract as $_uc){?>
                            <option value="<?php echo $_uc['ucid'];?>" ><?php echo $_uc['tplname'].'-'.$_uc['tplnumber'];?></option>
                            <?php } ?>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>债权利率:</dt>
                    <dd>
                        <input type="text" class="filed-text required" name="con_income" id="con_income"/>%
                    </dd>
                </dl>
                <dl>
                    <dt>合同期限</dt>
                    <dd>
                        <input type="text" class="date required" name="interesttime" id="interesttime"/>
                        <span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span>
                        <input type="text" class="date required" name="repaymenttime" id="repaymenttime" onblur="count_u_diff_day()"/>
                        <span class="info" id="uisday">&nbsp;&nbsp;理财期限<font color="#FF0000">0</font>天</span>
                    </dd>
                </dl>
                <dl>
                    <dt>备注:</dt>
                    <dd>
                        <input type="text" class="filed-text" name="remark"/>
                    </dd>
                </dl>
            </fieldset>
            <fieldset class="EditField"><legend>项目描述</legend>
                <dl>
                    <dt>概述:</dt>
                    <dd >
                        <textarea style="width:150%;height:200px" name="object_overview" id="object_overview"></textarea>
                    </dd>
                </dl>
                <dl>
                    <dt>详述:</dt>
                    <dd >
                        <textarea style="width:150%;height:200px" name="object_desc" id="object_desc"></textarea>
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
                    <input type="text" name="object_img" id="object_img" value="" class="filed-text" style="width: 500px;"/>
                    <img alt="图片预览" id="_show_object" src="" style="position: absolute;height: 30px;width: 30px;right: 150px;top:10px;"/>
                    </dd>
                 </dl>
            </fieldset>
            <fieldset class="EditField"><legend>资金保障</legend>
                <dl>
                    <dt>概述:</dt>
                    <dd >
                        <textarea style="width:150%;height:200px" name="capital_overview" id="capital_overview"></textarea>
                    </dd>
                </dl>
                <dl>
                    <dt>详述:</dt>
                    <dd >
                        <textarea style="width:150%;height:200px" name="capital_desc" id="capital_desc"></textarea>
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">资金保障图片:</dt>
                    <dd style="width:13%">
                        <input id="CapitalInput" type="file" name="capital_file"
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
                    <input type="text" name="capital_img" id="capital_img" value="" class="filed-text" style="width: 500px;"/>
                    <img alt="图片预览" id="_show_Capital" src="" style="position: absolute;height: 30px;width: 30px;right: 150px;top:10px;"/>
                    </dd>
                 </dl>
            </fieldset>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="addcontract" />
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
    	s_strtime = Date.parse(new Date(s_strtime.replace(/-/g, "/")));
      	e_strtime = Date.parse(new Date(e_strtime.replace(/-/g, "/")));
      	var diff_time = e_strtime - s_strtime;
      	var day = diff_time / 86400000+1;
      	var content = '<span class="info" id="uisday">&nbsp;&nbsp;理财期限<font color="#FF0000">' + day + '</font>天</span>';
      	$('#uisday').html(content);
     }

     function uploadifyQueueComplete(queueData){}
     function uploadPicSuccess2(file, data, response){
         $('#_show_object').attr('src', data);
         $('#object_img').val(data);
     }
     function uploadPicSuccess3(file, data, response){
         $('#_show_Capital').attr('src', data);
         $('#capital_img').val(data);
     }
     function changnumber(){
 		//以下为测试输出
         var day = DX($('#con_money').val());
         var content = '<span class="info" id="num">' + day + '</span>';
         $('#num').html(content);
     }
     //主函数
     function DX(n) {
         if (!/^(0|[1-9]\d*)(\.\d+)?$/.test(n))
         return "数据非法";
         var unit = "千百拾亿千百拾万千百拾元角分", str = "";
         n += "00";
         var p = n.indexOf('.');
         if (p >= 0)
         n = n.substring(0, p) + n.substr(p+1, 2);
         unit = unit.substr(unit.length - n.length);
         for (var i=0; i < n.length; i++)
         str += '零壹贰叁肆伍陆柒捌玖'.charAt(n.charAt(i)) + unit.charAt(i);
         return str.replace(/零(千|百|拾|角)/g, "零").replace(/(零)+/g, "零").replace(/零(万|亿|元)/g, "$1").replace(/(亿)万|壹(拾)/g, "$1$2").replace(/^元零?|零分/g, "").replace(/元$/g, "元整");
     }
     function getcnnamelistBycname(){
    	 var cname = $('#cname').val();
    	 if(cname==''){
        	 alert("没有数据");
        	 return false;
         }
    	 $.ajax({
             type : 'POST',
             url : '<?php echo OP_DOMAIN; ?>/corporation/getcnnamelistBycname/',
             data : 'cname='+cname,
             dataType : 'json',
             async : false,
             success : function(data){
            	 var content = '<span class="info">'+data+'</span>';
                 $('#tishi').html(content);
             }
         });
     }

     $("#con_number").on("change",function(){
    	 var id = $("#corid").val();
    	 if(id==7){
        	 var con_number = $(this).val();
        	 if(con_number.indexOf('F')>0){
	         	$('#con_income').val('18');
           		}
         }
         });
    $("#corid").on("change",function(){
        var id = $(this).val();
        if(id==2){
        	$('#con_bzjbl').val('3');
	        $('#con_income').val('16');
        }else if(id==3){
        	$('#con_bzjbl').val('3');
        	$('#con_income').val('16');
        }else if(id==4){
        	$('#con_bzjbl').val('3');
        	$('#con_income').val('16');
        }else if(id==5){
        	$('#con_bzjbl').val('3');
        	$('#con_income').val('16');
        }else if(id==6){
        	$('#con_bzjbl').val('3');
        	$('#con_income').val('16');
        }else if(id==7){
        	$('#con_bzjbl').val('3');
        	$('#con_income').val('16');
        }
        else if(id==8){
        	$('#con_bzjbl').val('3');
        	$('#con_income').val('16');
        }
        else if(id==9){
        	$('#con_bzjbl').val('3');
        	$('#con_income').val('16');
        }
        else if(id==10){
        	$('#con_bzjbl').val('3');
        	$('#con_income').val('16');
        }
        $("#creditor").html('');
        $.ajax({
            url:'<?php echo OP_DOMAIN; ?>/corporation/getCreditorByid/',
            type:'post',
            data:{cid:id},
            dataType:'json',
            success:function(data){

                var div = '<option>请选择</option>';
                var num = 0;
                for (var i in data) {
                    div += '' +
                        '<option value="'+data[i].id+'">'+data[i].creditor+'</option>'
                    num++;
                }
                if(num == 0){

                    $("#cre").hide();
                    $("#identity_dl").hide();
                    $("#seal_dl").hide();
                }else{
                    $("#cre").show();
                    $("#identity_dl").show();
                    $("#seal_dl").show();
                }

                $("#creditor").html(div);
                $(".creditor_f").val('');
            }
        });
    })
    
     $("#creditor").on("change",function(){
         var creid = $(this).val();
         $.ajax({
             url: '<?php echo OP_DOMAIN; ?>/corporation/getCreditorBycreid/',
             type: 'post',
             data: {creid: creid},
             dataType: 'json',
             success: function (data) {
//                alert(data);return false;
                 $("#seal").val(data.seal);
                 $("#identity").val(data.identity);
             }
         });
     })
</script>
