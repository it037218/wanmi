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
<h2 class="contentTitle">新增</h2>
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>luckmoney_list/addluckmoney" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <fieldset class="EditField"><legend>红包信息</legend>
                <dl>
                    <dt>红包名称:</dt>
                    <dd >
                        <input name="lname" id="lname" type="text" size="30" value="" class="required"/>
                        <span class="info" onclick="autotianchong();">自动填充</span>
                    </dd>
                </dl>
                <dl>
                    <dt>红包金额:</dt>
                    <dd >
                        <input name="lmoney" id="lmoney" type="text" size="30" value="" class="required"/>
                    </dd>
                </dl>
                <dl>
                    <dt>用户群体:</dt>
                    <dd >
                        <select name="ltarget" id="ltarget"  class="required">
                            <option value="0">全部用户</option>
                            <option value="1">投资用户</option>
                            <option value="2">定期用户</option>
                        </select>
                    </dd>
                </dl>
            </fieldset>
            <fieldset class="EditField"><legend>红包权重</legend>
                <dl>
                    <dt>权重1金额:</dt>
                    <dd >
                       <input name="lweight1_money_1" id="lweight1_money_1" type="text" size="15" value="" class="required"/>
                       <span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span>
                       <input name="lweight1_money_2" id="lweight1_money_2" type="text" size="15" value="" class="required"/>
                    </dd>
                </dl>
                <dl>
                    <dt>红包占比:</dt>
                    <dd >
                       <input name="lproportion1" id="lproportion1" type="text" size="30" value="" class="required"/>%
                    </dd>
                </dl>
                <dl>
                    <dt>权重2金额:</dt>
                    <dd >
                       <input name="lweight2_money_1" id="lweight2_money_1" type="text" size="15" value="" class="required"/>
                       <span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span>
                       <input name="lweight2_money_2" id="lweight2_money_2" type="text" size="15" value="" class="required"/>
                    </dd>
                </dl>
                <dl>
                    <dt>红包占比:</dt>
                    <dd >
                       <input name="lproportion2" id="lproportion2" type="text" size="30" value="" class="required"/>%
                    </dd>
                </dl>
                <dl>
                    <dt>权重3金额:</dt>
                    <dd >
                       <input name="lweight3_money_1" id="lweight3_money_1" type="text" size="15" value="" class="required"/>
                       <span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span>
                       <input name="lweight3_money_2" id="lweight3_money_2" type="text" size="15" value="" class="required"/>
                    </dd>
                </dl>
                <dl>
                    <dt>红包占比:</dt>
                    <dd >
                       <input name="lproportion3" id="lproportion3" type="text" size="30" value="" class="required" onBlur="is_100()"/>%<span id="lproportion" style="color:red;"></span>
                    </dd>
                </dl>
               
            </fieldset>
            
            <fieldset class="EditField"><legend>红包设置</legend>
                <dl>
                    <dt>预告时间</dt>
                    <dd >
                         <input type="text" class="date required" name="yugaotime" id='yugaotime' dateFmt="yyyy-MM-dd HH:mm" readonly="true"/>
                    </dd>
                </dl>
                <dl>
                    <dt>自动上线时间:</dt>
                    <dd >
                      <input type="text" class="date required" name="lstime" id='lstime' dateFmt="yyyy-MM-dd HH:mm" readonly="true"/>
                    </dd>
                </dl>
                <dl>
                    <dt>红包发完延续时间:</dt>
                    <dd >
                      <input name="delaytime" id="delaytime" type="text" size="30" value="" class="required"/>
                      <span class="info">&nbsp;&nbsp;分钟&nbsp;&nbsp;</span>
                    </dd>
                </dl>
                <dl>
                    <dt>获得金额红包权重:</dt>
                    <dd >
                      <input name="ltoweight" id="ltoweight" type="text" size="30" value="" class="required"/>%
                    </dd>
                </dl>
                <dl>
                    <dt>获得金额红包下降权重:</dt>
                    <dd >
                      <input name="ltoweightdown" id="ltoweightdown" type="text" size="30" value="" class="required"/>%
                    </dd>
                </dl>
                <dl>
                    <dt>文字显示:</dt>
                    <dd >
                      <input name="ltext" id="ltext" type="text" size="30" value="" class="required"/>
                    </dd>
                </dl>
                <dl>
                    <dt>获奖红包祝福语:</dt>
                    <dd >
                      <input name="bless_text" id="bless_text" type="text" size="30" value="" class="required"/>
                    </dd>
                </dl>
                <dl>
                    <dt>非获奖红包祝福语:</dt>
                    <dd >
                      <input name="nobless_text" id="nobless_text" type="text" size="30" value="" />
                    </dd>
                </dl>
            </fieldset>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="addluckmoney" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">

function is_100(){
    var lproportion1 = $('#lproportion1').val();
    var lproportion2 = $('#lproportion2').val();
    var lproportion3 = $('#lproportion3').val();
    if(Number(lproportion1)+Number(lproportion2)+Number(lproportion3) == 100){
    	$('#lproportion').html(" ");
    }else{
    	$('#lproportion').html("三个红包占比相加不等于100%");
    }
}
function autotianchong(){
	 var lname = $('#lname').val();
	 if(lname==''){
  	 alert("没有数据");
  	 return false;
   }
	 
	 $.ajax({
       type : 'POST',
       url : '<?php echo OP_DOMAIN; ?>/luckmoney_list/autotianchong/',
       data : 'lname='+lname,
       dataType : 'json',
       async : false,
       success : function(data){
    	 lweight1_money=data.lweight1_money;
    	 lweight2_money=data.lweight2_money;
    	 lweight3_money=data.lweight3_money;
    	 var lweight1_moneys= new Array();
    	 var lweight2_moneys= new Array();
    	 var lweight3_moneys= new Array();
    	 
    	 lweight1_moneys=lweight1_money.split("-");
    	 lweight2_moneys=lweight2_money.split("-");
    	 lweight3_moneys=lweight3_money.split("-");
  
      	 $('#lname').val(data.lname);
      	 $('#lmoney').val(data.lmoney);
      	 $('#ltarget').val(data.ltarget);
      	 
      	 $('#lweight1_money_1').val(lweight1_moneys['0']);
       	 $('#lweight1_money_2').val(lweight1_moneys['1']);
       	 
      	 $('#lproportion1').val(data.lproportion1);
      	 
      	 $('#lweight2_money_1').val(lweight2_moneys['0']);
       	 $('#lweight2_money_2').val(lweight2_moneys['1']);
       	
      	 $('#lproportion2').val(data.lproportion2);
      	 
      	 $('#lweight3_money_1').val(lweight3_moneys['0']);
       	 $('#lweight3_money_2').val(lweight3_moneys['1']);
      	 
      	 $('#lproportion3').val(data.lproportion3);
      	 $('#ltext').val(data.ltext);
      	 $('#bless_text').val(data.bless_text);
      	 $('#nobless_text').val(data.nobless_text);
      	 $('#ltoweight').val(data.ltoweight);
      	 $('#ltoweightdown').val(data.ltoweightdown);
      	 $('#delaytime').val(data.delaytime);
      	 $('#etime').val(data.etime);
      	 alert("已经自动填充完毕");
       }
   });
	 
}

     
    

     
</script>
