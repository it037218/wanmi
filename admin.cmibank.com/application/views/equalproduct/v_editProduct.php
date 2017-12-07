
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

<h2 class="contentTitle">上传产品</h2>
<div class="pageContent">
   
    <form method="post" action="<?php echo OP_DOMAIN;?>equalproduct/editproduct" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">

            <fieldset class="EditField"><legend>项目选择</legend>
                <dl>
                    <dt>项目选择:</dt>
                    <dd>
                        <select name="ptid" class="combox">
                            <option value="0">请选择项目</option>
                            <?php if(!empty($ptype_list)){ ?>
                                <?php foreach($ptype_list as $index => $name){ ?>
                                    
                                    <option value="<?php echo $index ;?>" <?php if($index == $detail['ptid']){ echo 'selected';}?>><?php echo $name; ?></option>
                                <?php } ?>
                            <?php } ?>
                    </select>
                    </dd>
                </dl>
            </fieldset>
            
            <fieldset class="EditField"><legend>基本信息</legend>
                <dl>    
                    <dt>业务类型：</dt>
    			    <dd>
                        <select class="combox required" name="ucid" >
                            <option value="0" selected="" >请选择</option>
                            <?php foreach($usercontract as $_ucontract){?>
                            <option value="<?php echo $_ucontract['ucid'];?>" <?php if($_ucontract['ucid'] == $detail['ucid']){ echo 'selected';}?> ><?php echo $_ucontract['tplname'] . '-' . $_ucontract['tplnumber'];?></option>
                            <?php } ?>
                        </select>
                    </dd>
                </dl>

                <dl>
                    <dt>产品名称:</dt>
                    <dd>
                        
                        <input type="text" class="required" name="pname" value="<?php echo $detail['pname'];?>"/>
                    </dd>
                </dl>
                <hr />
                <dl>
                    <dt>预期收益:</dt>
                    <dd>
                        <input type="text" class="required" name="income" value="<?php echo $detail['income'];?>"/>
                    </dd>
                </dl>
                <hr />
                <dl>
                    <dt>用户起息日</dt>
                    <dd>
                        <input type="text" class="date required" name="uistime" id="uistime" value="<?php echo $detail['uistime'];?>"/>
                        <span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span>
                        <input type="text" class="date required" name="uietime" id="uietime" onblur="count_u_diff_day()" value="<?php echo $detail['uietime'];?>" />
                        <span class="info" id="uisday">&nbsp;&nbsp;理财期限<font color="#FF0000">0</font>天</span>                 
                    </dd>
                </dl>
                <dl>
                    <dt>合作方起息日</dt>
                    <dd>
                        <input type="text" class="date required" name="cistime" id="cistime" value="<?php echo $detail['cistime'];?>"/>
                        <span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span>
                        <input type="text" class="date required"  name="cietime" id="cietime" onblur="count_c_diff_day()" value="<?php echo $detail['cietime'];?>"/>
                        <span class="info"  id="cisday" >&nbsp;&nbsp;理财期限<fontcolor="#FF0000">0</font>天</span>                 
                    </dd>
                </dl>
                <hr />
                <dl>
                    <dt>还款方式</dt>
                    <dd>
                        <select name="repaymode" class="combox">
                            <option value="1" <?php if($detail['repaymode'] == 1){ echo "selected";}?> >到期偿还</option>
                            <option value="2" <?php if($detail['repaymode'] == 2){ echo "selected";}?>>随存随取</option>
                        </select>  
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">募集金额:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="money" value="<?php echo $detail['money'];?>"/>
                    </dd>
                    <dt style="width:80px;">起购金额:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="startmoney" value="<?php echo $detail['startmoney'];?>"/>
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">累进金额:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="money_limit" value="<?php echo $detail['money_limit'];?>"/>
                    </dd>
                    <dt style="width:80px;">购买限额:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="money_max" value="<?php echo $detail['money_max'];?>"/>
                    </dd>
                </dl>
                <hr />
                <dl>
                    <dt style="width:80px;">运营标签:</dt>
                    <dd style="width:13%">
                        <input type="text" name="operation_tag" value="<?php echo $detail['operation_tag'];?>" />
                    </dd>
                    <dt>运营标签图片</dt>
                    <dd>
                        <select name="standard_icon" id="standard_icon">
                            <option value="xinshoubiao_hong" <?php if($detail['standard_icon'] == 'xinshoubiao_hong'){ echo "selected";}?> >红色</option>
                            <option value="yugao_lan" <?php if($detail['standard_icon'] == 'yugao_lan'){ echo "selected";}?>>蓝色</option>
                            <option value="tuijian_huang" <?php if($detail['standard_icon'] == 'tuijian_huang'){ echo "selected";}?>>黄色</option>
                            <option value="shouwan" <?php if($detail['standard_icon'] == 'shouwan'){ echo "selected";}?>>灰色</option>
                            <option value="" <?php if($detail['standard_icon'] == ''){ echo "selected";}?>>白色</option>
                        </select>
                    </dd>
                </dl>
                <!--
                <dl>
                    <dt style="width:80px;">标准标签图片:</dt>
                    <dd style="width:13%">
                        <input id="standardInput" type="file" name="standard_file"
                            uploaderOption="{
                            swf:'<?php echo STATIC_DOMAIN; ?>/admin/dwz/uploadify/scripts/uploadify.swf',
                            uploader:'<?php echo OP_DOMAIN; ?>/equalproduct/doUpload',
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
                    <input type="text" name="standard_icon" id="img"  class="required" style="width: 500px;" value="<?php echo $detail['standard_icon'];?>"/>
                    <img alt="图片预览" id="_show_pic" src="<?php echo $detail['standard_icon'];?>" style="position: absolute;height: 30px;width: 30px;right: 150px;top:10px;"/>
                    </dd>
                 </dl>
                 -->
                 <dl>
                    <dt style="width:80px;">标准标签:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="standard_tag" value="<?php echo $detail['standard_tag'];?>"/>
                    </dd>
                    <dt style="width:80px;">标准标签文本:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="standard_text" value="<?php echo $detail['standard_text'];?>"/>
                    </dd>
                 </dl>
                 <dl>
                    <dt style="width:80px;">文本标题:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="text_text" id="text_text" value="<?php echo $detail['text_text'];?>"/>
                    </dd>
                    <dt style="width:80px;">文本链接:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="text_url" id="text_url" value="<?php echo $detail['text_url'];?>"/>
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">买多少:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="exp_buy" id="exp_buy" value="<?php echo $detail['exp_buy'];?>"/>
                    </dd>
                    <dt style="width:80px;">送多少:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="exp_send" id="exp_send" value="<?php echo $detail['exp_send'];?>"/>
                    </dd>
                </dl>
            </fieldset>
            
            <fieldset class="EditField"><legend>发布设置</legend>
                <dl>
                    <dt>可购买用户</dt>
                    <dd >
                        <select name="canbuyuser" class="combox">
                            <option value="1"  <?php if($detail['canbuyuser'] == 1){ echo 'selected';}?>>无限制</option>
                            <option value="2"  <?php if($detail['canbuyuser'] == 2){ echo 'selected';}?>>新用户</option>
                            <option value="3"  <?php if($detail['canbuyuser'] == 3){ echo 'selected';}?>>老用户</option>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>自动上线时间:</dt>
                    <dd >
                        <input type="text" class="date" name="online_time" id='online_time' dateFmt="yyyy-MM-dd HH:mm" onclick='onlinetimechange()' value="<?php echo $detail['online_time']?>"/>
                    </dd>
                </dl>
<!--                 <dl> -->
<!--                     <dt>预告时间:</dt> -->
<!--                     <dd > -->
<!--                         <input type="text" class="date" name="yugaotime" id="yugaotime" dateFmt="yyyy-MM-dd HH:mm:ss" value="<?php echo $detail['yugaotime']?>"/> -->
<!--                     </dd> -->
<!--                 </dl> -->
                <dl>
                    <dt>允许超买:</dt>
                    <dd >
                        <label><input type="radio" name="cancm" checked="checked" value="1"  <?php if($detail['cancm'] == 1){ echo 'selected';}?>/>允许</label>
                        <label><input type="radio" name="cancm" value="2" <?php if($detail['cancm'] == 2){ echo 'selected';}?>/>不允许</label>
                    </dd>
                </dl>
            </fieldset>

        </div>
        <div class="formBar">
            <ul>

                <input type="hidden" name="op" value="saveedit" />
                <input type="hidden" name="pid" value="<?php echo $detail['pid']; ?>" />

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
