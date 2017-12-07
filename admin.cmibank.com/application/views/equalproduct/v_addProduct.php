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
    <form method="post" action="<?php echo OP_DOMAIN;?>equalproduct/addproduct" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <fieldset class="EditField"><legend>项目选择</legend>
                <dl>
                    <dt>项目选择:</dt>
                    <dd>
                        <select name="ptid" id="ptid" class="required">
                            <option value="0">请选择项目</option>
                            <?php if(!empty($ptype_list)){ ?>
                                <?php foreach($ptype_list as $index => $name){ ?>
                                    <option value="<?php echo $index ;?>"><?php echo $name; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>上线状态:</dt>
                    <dd>
                        <select name="recommend" id="recommend"  class="required">
                            <option value="0">正常上线</option>
                            <option value="1">首页置顶</option>
                        </select>
                        
                    </dd>
                </dl>
            </fieldset>
            
            <fieldset class="EditField"><legend>基本信息</legend>
                <dl>    
                    <dt>业务类型：</dt>
    			    <dd>
                        <select class="required" name="ucid" id="ucid" >
                            <option value="0" selected="" >请选择</option>
                            <?php foreach($usercontract as $_ucontract){?>
                            <option value="<?php echo $_ucontract['ucid'];?>" ><?php echo $_ucontract['tplname'] . '-' . $_ucontract['tplnumber'];?></option>
                            <?php } ?>
                        </select>
                    </dd>
                </dl>
                
                <dl>
                    <dt>产品名称:</dt>
                    <dd>
                        <input type="text" class="required" name="pname" id="pname" />
                        <span class="info" onclick="autotianchong();">自动填充</span>
                    </dd>
                </dl>
                <hr />
                <dl>
                    <dt>预期收益:</dt>
                    <dd>
                        <input type="text" class="required" name="income" id="income"  max="20" min="1"/>
                    </dd>
                </dl>
                <hr />
                <dl>
                    <dt>用户起息日</dt>
                    <dd>
                        <input type="text" class="date required" name="uistime" id="uistime" mindate="<?php echo $mindate?>"/>
                        <span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span>
                        <input type="text" class="date required" name="uietime"  id="uietime" onblur="count_u_diff_day()" mindate="<?php echo $mindate?>"/>
                        <span class="info" id="uisday">&nbsp;&nbsp;理财期限<font color="#FF0000">0</font>天</span>                 
                    </dd>
                </dl>
                <dl>
                    <dt>合作方起息日</dt>
                    <dd>
                        <input type="text" class="date required" name="cistime" id="cistime"/>
                        <span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span>
                        <input type="text" class="date required"  name="cietime" id="cietime" onblur="count_c_diff_day()"/>
                        <span class="info"  id="cisday" >&nbsp;&nbsp;理财期限<fontcolor="#FF0000">0</font>天</span>                 
                    </dd>
                </dl>
                <hr />
                <dl>
                    <dt style="width:80px;">还款方式</dt>
                    <dd style="width:13%">
                        <select name="repaymode" id="repaymode" class="combox">
                            <option value="1">每月还款</option>
                        </select>
                    </dd>
                    <dt style="width:80px;">还款日</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="repaymentday" id="repaymentday" />
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">募集金额:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="money" id="money" onblur="changnumber();"/><span class="info" id="num"></span>
                    </dd>
                    <dt style="width:80px;">起购金额:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="startmoney" id="startmoney" />
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">累进金额:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="money_limit" id="money_limit"/>
                    </dd>
                    <dt style="width:80px;">购买限额:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="money_max" id="money_max"/>
                    </dd>
                </dl>
                <hr />
<!--                 <dl> -->
<!--                     <dt style="width:80px;">运营标签图片:</dt>
<!--                     <dd style="width:13%">
<!--                        <select class="combox required" name="operation_image" > -->
<!--                          <option value="tuijian_huang">推荐标</option> -->
<!--                          <option value="xinshoubiao_hong">新手标</option> -->
<!--                          <option value="yugao_lan">预告标</option> -->
<!--                        </select> -->
<!--                     </dd> -->
<!--                  </dl> -->
                <dl>
                    <dt style="width:80px;">运营标签:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="operation_tag" id="operation_tag"/>
                    </dd>
                     <dt>运营标签图片:</dt>
                    <dd>
                        <select name="standard_icon" id="standard_icon">
                             <option value="xinshoubiao_hong" >红色</option>
                            <option value="yugao_lan">蓝色</option>
                            <option value="tuijian_huang">黄色</option>
                            <option value="shouwan">灰色</option>
                            <option value="">空白</option>
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
                    <input type="text" name="standard_icon" id="img" value="" class="required" style="width: 500px;"/>
                    <img alt="图片预览" id="_show_pic" src="" style="position: absolute;height: 30px;width: 30px;right: 150px;top:10px;"/>
                    </dd>
                 </dl>
                 -->
                 <dl>
                   <dt style="width:80px;">标准标签:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="standard_tag" id="standard_tag"/>
                    </dd>
                     <dt style="width:80px;">标准标签文本:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="standard_text" id="standard_text" />
                    </dd>
                 </dl>
                 <dl>
                    <dt style="width:80px;">文本标题:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="text_text" id="text_text"/>
                    </dd>
                    <dt style="width:80px;">文本链接:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="text_url" id="text_url"/>
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">买多少:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="exp_buy" id="exp_buy"/>
                    </dd>
                    <dt style="width:80px;">送多少:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="exp_send" id="exp_send"/>
                    </dd>
                </dl>
            </fieldset>
            
            </fieldset>
            <fieldset class="EditField"><legend>合同信息</legend>
                <dl>
                    <dt>债权公司:</dt>
                    <dd >
                        <select class="combox required" name="corcid" id="corcid" ref="classid_group" refUrl="<?php echo OP_DOMAIN; ?>/equalamountcontract/getContractByCorid/{value}">
                        <option value="0" selected="" >请选择</option>
                        <?php foreach($corporation as $_corpor){?>
                        <option value="<?php echo $_corpor['corid'];?>" ><?php echo $_corpor['cname'];?></option>
                        <?php } ?>
                    </select>
                    
                    </dd>
                </dl>
                <dl>
                    <dt>合同编号:</dt>
                    <dd >
                       <select class="combox required" name="cid" id="classid_group" onchange='cidchange()'>
                       <option value ='0'>请选择</option>
                       </select>
                    </dd>
                </dl>
                <dl>
                    <dt>回款收益:</dt>
                    <dd >
                        <input type="text" class="filed-text" name="con_income" id="con_income" readonly=true/>
                    </dd>
                </dl>
                <dl>
                    <dt>剩余库存:</dt>
                    <dd >
                        <input type="text" class="filed-text" name="remain_money" id="remain_money" readonly=true/>
                    </dd>
                </dl>
            </fieldset>
            
            <fieldset class="EditField"><legend>发布设置</legend>
                <dl>
                    <dt>可购买用户</dt>
                    <dd >
                        <select name="canbuyuser" id="canbuyuser">
                            <option value="1">无限制</option>
                            <option value="2">新用户</option>
                            <option value="3">老用户</option>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>自动上线时间:</dt>
                    <dd >
                      <input type="text" class="date" name="online_time" id='online_time' dateFmt="yyyy-MM-dd HH:mm" onclick='onlinetimechange()'/>
                    </dd>
                </dl>
<!--                 <dl> -->
<!--                     <dt>预告时间:</dt> -->
<!--                     <dd > -->
<!--                         <input type="text" class="date" name="yugaotime" id="yugaotime" dateFmt="yyyy-MM-dd HH:mm:ss"/> -->
<!--                     </dd> -->
<!--                 </dl> -->
                <dl>
                    <dt>允许超买:</dt>
                    <dd >
                        <label><input type="radio" name="cancm" checked="checked" value="1" />允许</label>
                        <label><input type="radio" name="cancm" value="2" />不允许</label>
                    </dd>
                </dl>
            </fieldset>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="addproduct" />
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
        var repaymentday = e_strtime;
        s_strtime = Date.parse(new Date(s_strtime.replace(/-/g, "/")));
        e_strtime = Date.parse(new Date(e_strtime.replace(/-/g, "/")));
        var diff_time = e_strtime - s_strtime;
        var day = diff_time / 86400000+1;
        var content = '<span class="info" id="cisday">&nbsp;&nbsp;理财期限<font color="#FF0000">' + day + '</font>天</span>';
        $('#cisday').html(content);
        repaymentday = repaymentday.toString().substring(8, 10);
      	$('#repaymentday').val(repaymentday);
     }

     function cidchange(){
    	 var cid = $('#classid_group').val();
    	 $.ajax({
             type : 'POST',
             url : '<?php echo OP_DOMAIN; ?>/equalamountcontract/getContractByCid/' + cid,
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
             url : '<?php echo OP_DOMAIN; ?>/equalproduct/autotianchong/',
             data : 'pname='+pname,
             dataType : 'json',
             async : false,
             success : function(data){
                 
                 if(data == ''){
                	 alert('没有找到数据');
                	 return;
                 }
            	 $('#income').val(data.income);
            	 $('#ptid').val(data.ptid);
            	 $('#recommend').val(data.recommend);
            	 $('#ucid').val(data.ucid);
            	 $('#pname').val(data.pname);
            	 $('#uistime').val(data.uistime);
            	 $('#uietime').val(data.uietime);
            	 $('#cistime').val(data.cistime);
            	 $('#repaymode').val(data.repaymode);
            	 $('#repaymentday').val(data.repaymentday);
            	 $('#money').val(data.money);
            	 $('#startmoney').val(data.startmoney);
            	 $('#money_limit').val(data.money_limit);
            	 $('#operation_tag').val(data.operation_tag);
            	 $('#standard_tag').val(data.standard_tag);
            	 $('#standard_icon').val(data.standard_icon);
            	 $('#standard_text').val(data.standard_text);
            	 $('#canbuyuser').val(data.canbuyuser);
            	 $('#cancm').val(data.cancm);
            	 $('#cietime').val(data.cietime);
           	     $('#text_text').val(data.text_text);
              	 $('#text_url').val(data.text_url);
                 $('#exp_buy').val(data.exp_buy);
                 $('#exp_send').val(data.exp_send);
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
     function changnumber(){
   		//以下为测试输出
           var day = DX($('#money').val())
           var content = '<span class="info" id="num">' + day + '</span>'
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
</script>
