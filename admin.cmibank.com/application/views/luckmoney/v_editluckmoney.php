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
<h2 class="contentTitle">修改</h2>
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>luckmoney_list/editLuckmoney" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <fieldset class="EditField"><legend>红包信息</legend>
                <dl>
                    <dt>红包名称:</dt>
                    <dd >
                        <input name="lname" type="text" size="30" value="<?php echo $detail['lname']?>" class="required"/>
                    </dd>
                </dl>
                <dl>
                    <dt>红包金额:</dt>
                    <dd >
                        <input name="lmoney" type="text" size="30" value="<?php echo $detail['lmoney']?>" class="required"/>
                    </dd>
                </dl>
                <dl>
                    <dt>用户群体:</dt>
                    <dd >
                        <select name="ltarget" id="ltarget"  class="required">
                            <option value="0" <?php if($detail['ltarget'] == 0){ echo "selected";}?> >全部用户</option>
                            <option value="1" <?php if($detail['ltarget'] == 1){ echo "selected";}?>>投资用户</option>
                            <option value="2" <?php if($detail['ltarget'] == 2){ echo "selected";}?>>定期用户</option>
                        </select>
                    </dd>
                </dl>
            </fieldset>
            <fieldset class="EditField"><legend>红包权重</legend>
                <?php  
   
                    $lweight1_money = explode("-",$detail['lweight1_money']);
                    $lweight1_money_1 = $lweight1_money[0];
                    $lweight1_money_2 = $lweight1_money[1];
                    
                    $lweight2_money = explode("-",$detail['lweight2_money']);
                    $lweight2_money_1 = $lweight2_money[0];
                    $lweight2_money_2 = $lweight2_money[1];
                    
                    $lweight3_money = explode("-",$detail['lweight3_money']);
                    $lweight3_money_1 = $lweight3_money[0];
                    $lweight3_money_2 = $lweight3_money[1];
                
                ?>
                <dl>
                    <dt>权重1金额:</dt>
                    <dd >
                       <input name="lweight1_money_1" type="text" size="15" value="<?php echo $lweight1_money_1?>" class="required"/>
                       <span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span>
                       <input name="lweight1_money_2" type="text" size="15" value="<?php echo $lweight1_money_2?>" class="required"/>
                    </dd>
                </dl>
                <dl>
                    <dt>红包占比:</dt>
                    <dd >
                       <input name="lproportion1" id="lproportion1" type="text" size="30" value="<?php echo $detail['lproportion1']?>" class="required"/>%
                    </dd>
                </dl>
                <dl>
                    <dt>权重2金额:</dt>
                    <dd >
                       <input name="lweight2_money_1" type="text" size="15" value="<?php echo $lweight2_money_1?>" class="required"/>
                       <span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span>
                       <input name="lweight2_money_2" type="text" size="15" value="<?php echo $lweight2_money_2?>" class="required"/>
                    </dd>
                </dl>
                <dl>
                    <dt>红包占比:</dt>
                    <dd >
                       <input name="lproportion2" id="lproportion2" type="text" size="30" value="<?php echo $detail['lproportion2']?>" class="required"/>%
                    </dd>
                </dl>
                <dl>
                    <dt>权重3金额:</dt>
                    <dd >
                       <input name="lweight3_money_1" type="text" size="15" value="<?php echo $lweight3_money_1?>" class="required"/>
                       <span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span>
                       <input name="lweight3_money_2" type="text" size="15" value="<?php echo $lweight3_money_2?>" class="required"/>
                    </dd>
                </dl>
                <dl>
                    <dt>红包占比:</dt>
                    <dd >
                       <input name="lproportion3" id="lproportion3"  type="text" size="30" value="<?php echo $detail['lproportion3']?>" class="required" onBlur="is_100()"/>%<span id="lproportion" style="color:red;"></span>
                    </dd>
                </dl>
               
            </fieldset>
            
            <fieldset class="EditField"><legend>红包设置</legend>
                <dl>
                    <dt>预告时间</dt>
                    <dd >
                         <input type="text" class="date" name="yugaotime" id='yugaotime' dateFmt="yyyy-MM-dd HH:mm" value="<?php echo date('Y-m-d H:i',$detail['yugaotime'])?>" readonly="true"/>
                    </dd>
                </dl>
                <dl>
                    <dt>自动上线时间:</dt>
                    <dd >
                      <input type="text" class="date" name="lstime" id='lstime' dateFmt="yyyy-MM-dd HH:mm" value="<?php echo date('Y-m-d H:i',$detail['lstime'])?>" readonly="true"/>
                    </dd>
                </dl>
                <dl>
                    <dt>红包发完延续时间:</dt>
                    <dd >
                      <input name="delaytime" type="text" size="30" value="<?php echo $detail['delaytime']?>" class="required" />
                      <span class="info">&nbsp;&nbsp;分钟&nbsp;&nbsp;</span>
                    </dd>
                </dl>
                <dl>
                    <dt>获得金额红包权重:</dt>
                    <dd >
                      <input name="ltoweight" type="text" size="30" value="<?php echo $detail['ltoweight']?>" class="required"/>%
                    </dd>
                </dl>
                <dl>
                    <dt>获得金额红包下降权重:</dt>
                    <dd >
                      <input name="ltoweightdown" type="text" size="30" value="<?php echo $detail['ltoweightdown']?>" class="required"/>%
                    </dd>
                </dl>
                <dl>
                    <dt>文字显示:</dt>
                    <dd >
                      <input name="ltext" type="text" size="30" value="<?php echo $detail['ltext']?>" class="required"/>
                    </dd>
                </dl>
                <dl>
                    <dt>获奖红包祝福语:</dt>
                    <dd >
                      <input name="bless_text" type="text" size="30" value="<?php echo $detail['bless_text']?>" class="required"/>
                    </dd>
                </dl>
                <dl>
                    <dt>非获奖红包祝福语:</dt>
                    <dd >
                      <input name="nobless_text" type="text" size="30" value="<?php echo $detail['nobless_text']?>" />
                    </dd>
                </dl>
            </fieldset>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="edit" />
                <input type="hidden" name="lmid" value="<?php echo $detail['lmid']?>" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     //$.pdialog.resizeDialog({style: {height: 500}}, $.pdialog.getCurrent(), "");
     
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

     
</script>
