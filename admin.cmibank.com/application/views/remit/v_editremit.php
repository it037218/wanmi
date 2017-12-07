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
    <form method="post" action="<?php echo OP_DOMAIN;?>remit/editremit" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
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
                            echo $product['sellmoney']- $product['sellmoney']*$contract['con_bzjbl']/100;
                        ?>
                    </dd>
                </dl>
            </fieldset>
            <fieldset class="EditField"><legend>打款凭证</legend>
            <dl>
                <dt>是否上传真实凭证</dt>
                <dd>
                    <label><input name="is_upload" type="radio" value="0" <?php if($product['is_upload'] == 0){ echo "checked";}?> />否 </label>
                    <label><input name="is_upload" type="radio" value="1" <?php if($product['is_upload'] == 1){ echo "checked";}?>/>是 </label>  
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
                    <input type="text" name="warrant_img" id="img" value="<?php echo $remit['warrant_img']?>" class="required" style="width: 500px;"/>
                </dd>
            </dl>
            <dl>
                <dt>打款时间:</dt>
                <dd>
                    <input type="text" name="ctime" value="<?php echo date('Y-m-d H:m:s', $remit['ctime']); ?>" class="date" dateFmt="yyyy-MM-dd HH:mm:ss"/>
                </dd>
            </dl>
            <dl>
                <dt>备注:</dt>
                <dd>
                    <input type="text" name="des" value="<?php echo $remit['des']?>"/>
                </dd>
            </dl>
            </fieldset>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="rid" value="<?php echo $remit['rid']; ?>" />
                <input type="hidden" name="pid" value="<?php echo $product['pid']; ?>" />
                <input type="hidden" name="op" value="editremit" />
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

     function count_u_diff_day(){
 	    var s_strtime = $('#uistime').val();
 	    var e_strtime = $('#uietime').val();
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


     function count_c_diff_day(){
        var s_strtime = $('#cistime').val();
        var e_strtime = $('#cietime').val();
        if(e_strtime.length == 0){
        	return false;
        }
        s_strtime = Date.parse(new Date(s_strtime.replace(/-/g, "/")));
        e_strtime = Date.parse(new Date(e_strtime.replace(/-/g, "/")));
        var diff_time = e_strtime - s_strtime;
        var day = diff_time / 86400000+1;
        var content = '<span class="info" id="cisday">&nbsp;&nbsp;理财期限<font color="#FF0000">' + day + '</font>天</span>';
        $('#cisday').html(content);
     }

     function cidchange(){
    	 var cid = $('#classid_group').val();
    	 $.ajax({
             type : 'POST',
             url : '<?php echo OP_DOMAIN; ?>/contract/getContractByCid/' + cid,
             data : '',
             dataType : 'json',
             async : false,
             success : function(data){
            	 $('#con_income').val(data.con_income);
            	 $('#remain_money').val(data.con_money - data.money);
             }
         });
     }
     
     function autotianchong(){
    	 var pname = $('#pname').val();
    	 if(pname==''){
        	 alert("没有数据");
        	 return false;
         }
    	 
    	 $.ajax({
             type : 'POST',
             url : '<?php echo OP_DOMAIN; ?>/product/autotianchong/',
             data : 'pname='+pname,
             dataType : 'json',
             async : false,
             success : function(data){
            	 $('#income').val(data.income);
            	 $('#ptid').val(data.ptid);
            	 $('#recommend').val(data.recommend);
            	 $('#ucid').val(data.ucid);
            	 $('#pname').val(data.pname);
            	 $('#uistime').val(data.uistime);
            	 $('#uietime').val(data.uietime);
            	 $('#cistime').val(data.cistime);
            	 $('#repaymode').val(data.repaymode);
            	 $('#money').val(data.money);
            	 $('#startmoney').val(data.startmoney);
            	 $('#money_limit').val(data.money_limit);
            	 $('#money_max').val(data.money_max);
            	 $('#operation_tag').val(data.operation_tag);
            	 $('#standard_tag').val(data.standard_tag);
            	 $('#standard_icon').val(data.standard_icon);
            	 $('#standard_text').val(data.standard_text);
            	 $('#canbuyuser').val(data.canbuyuser);
            	 $('#cancm').val(data.cancm);
            	 $('#cietime').val(data.cietime);
            	 alert("已经自动填充完毕");
             }
         });
    	 
     }
     function ChangeSelector(){
  		$("#ucid").val("1001");
  		alert('123');
  	 }
     function onlinetimechange(){
    	 var onlinetime = $('#online_time').val();
    	 if(onlinetime == ''){
        	 return false;
    	 }
    	 var date=new Date();
    	 date.setFullYear(onlinetime.substring(0,4));
    	 date.setMonth(onlinetime.substring(5,7)-1);
    	 date.setDate(onlinetime.substring(8,10));
    	 date.setHours(onlinetime.substring(11,13));
    	 date.setMinutes(onlinetime.substring(14,16));
    	 date.setSeconds(onlinetime.substring(17,19));
    	 var uninxonlinetime = Date.parse(date)/1000;
    	 var yugaotime = uninxonlinetime - 1800;
    	 var tt= UnixToDate(yugaotime, true, 8);
    	 $('#yugaotime').val(tt);
     }


     function UnixToDate(unixTime, isFull, timeZone) {
         if (typeof (timeZone) == 'number')
         {
             unixTime = parseInt(unixTime) + parseInt(timeZone) * 60 * 60;
         }
         var time = new Date(unixTime * 1000);
         var ymdhis = "";
         ymdhis += time.getUTCFullYear() + "-";
         month = time.getUTCMonth()+1;
         if(month < 10){
        	 month = '0' + month;
         }
         ymdhis += month + "-";
         var day = time.getUTCDate();
         if(day < 10){
        	 day = '0' + day;
         }
         ymdhis += time.getUTCDate();
         if (isFull === true)
         {
        	 var hours = time.getUTCHours();
        	 if(hours < 10){
        		 hours = '0' + hours;
        	 }
        	 var minutes = time.getUTCMinutes();
        	 if(minutes < 10){
        		 minutes = '0' + minutes;
        	 }
        	 var seconds = time.getUTCSeconds();
        	 if(seconds < 10){
        		 seconds = '0' + seconds;
        	 }
             ymdhis += " " + hours + ":";
             ymdhis += minutes + ":";
             ymdhis += seconds;
         }
         return ymdhis;
     }
</script>
