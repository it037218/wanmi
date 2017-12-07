<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>tiqianrepayment/editcontract" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="57">
            <fieldset class="EditField"><legend>基本信息</legend>
                <dl>
                    <dt>公司名称</dt>
                    <dd>
                        <input type="text" class="filed-text required" readonly=true name="corname" id="corname" value="<?php echo $detail['corname'];?>"/>
                    </dd>
                </dl>
                <dl>
                    <dt>合同编号:</dt>
                    <dd>
                        <input type="text" class="filed-text required" readonly=true name="con_number" value="<?php echo $detail['con_number'];?>"/>
                    </dd>
                </dl>
                <dl>
                    <dt>合同金额:</dt>
                    <dd>
                        <input type="text" class="filed-text required" readonly=true name="con_money" value="<?php echo $detail['con_money'];?>"/>
                    </dd>
                </dl>
                 <dl>
                    <dt>保证金比例:</dt>
                    <dd>
                        <input type="text" class="filed-text required" name="con_bzjbl" readonly="readonly" id="con_bzjbl" value="<?php echo $detail['con_bzjbl'];?>"/>%
                    </dd>
                </dl>
                <dl>
                    <dt>债权利率:</dt>
                    <dd>
                        <input type="text" class="filed-text required"  readonly="readonly" name="con_income"  value="<?php echo $detail['con_income'];?>"/>%
                    </dd>
                </dl>
                <dl>
                    <dt>合同期限</dt>
                    <dd>
                        <input type="text" class="date required" readonly="readonly" name="interesttime" id="interesttime" value="<?php echo $detail['interesttime'];?>"/>
                        <span class="info">&nbsp;&nbsp;至&nbsp;&nbsp;</span>
                        <input type="text" class="date required" name="repaymenttime" id="repaymenttime" onblur="count_u_diff_day()" value="<?php echo $detail['repaymenttime'];?>"/>
                        <span class="info" id="uisday">&nbsp;&nbsp;理财期限<font color="#FF0000"><?php echo $diff_day;?></font>天</span>
                    </dd>
                </dl>
                <dl>
                    <dt>备注:</dt>
                    <dd>
                        <input type="text" class="filed-text" name="remark" value="<?php echo $detail['remark'];?>"/>
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
         $('#_show_capital').attr('src', data);
         $('#capital_img').val(data);
     }
</script>
