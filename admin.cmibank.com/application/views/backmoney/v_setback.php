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
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>backmoney/setback" class="pageForm required-validate" onsubmit="return validateCallback(this,closedialog)">
        <div class="pageFormContent nowrap" layoutH="97">
        <table width="100%" border="1" style="text-align:center" class="list">
              <tr>
                <td rowspan="2"  width="7%">公司名称</td>
                <td rowspan="2" width="7%">合同编号</td>
                <td rowspan="2" width="3%">合同金额</td>
                <td rowspan="2" width="3%">保证金比例</td>
                <td rowspan="2" width="2%">债权利率(%)</td>
                <td rowspan="2" width="8%">产品名称</td>
                <td rowspan="2" width="2%">产品利率(%)</td>
                <td rowspan="2" width="5%">打款金额</td>
                <td colspan="3" width="15%">回款金额</td>
                <td rowspan="2" width="5%">打款计算回款服务费</td>
				<td rowspan="2" width="5%">起息日</td>
                <td rowspan="2" width="2%">期限(天)</td>
                <td rowspan="2" width="2%">打款期限(天)</td>
                <td rowspan="2" width="5%">截止日</td>
              </tr>
              <tr>
                <td>总本息</td>
                <!--<td>回款本金</td>-->
                <td>定期本息</td>
                <td>活期本息</td>
              </tr>
              <tr>
                <td><?php echo $detail['corname'];?></td>
                <td><?php echo $detail['con_number'];?></td>
                <td><?php echo $detail['con_money'];?></td>
                <td><?php echo $detail['con_bzjbl'];?></td>
                <td><?php echo $detail['con_income'];?></td>
                <td>
                <?php if(!empty($detail['product'])){?>
                <?php foreach ($detail['product'] as $key=>$val):?>
                <?php echo $val['pname'];?><br>
                <?php endforeach;?>
                <?php }?>
                <?php if($detail['is_stock'] == 1){?>
                    <?php foreach ($detail['longproduct'] as $key=>$val):?>
                    <?php echo '活期产品'.$val['sid'];?><br>
                    <?php endforeach;?>
                <?php }?>
                </td>
                <td>
                <?php if(!empty($detail['product'])){?>
                    <?php foreach ($detail['product'] as $key=>$val):?>
                    <?php echo $val['income'];?><br>
                    <?php endforeach;?>
                <?php }?>
                <?php if($detail['is_stock'] == 1){?>
                    <?php foreach ($detail['longproduct'] as $key=>$val):?>
                    <?php echo 9;?><br>
                    <?php endforeach;?>
                <?php }?>
                </td>
                <td>
                <?php if(!empty($detail['product'])){?>
                    <?php foreach ($detail['product'] as $key=>$val):?>
                    <?php echo $val['sellmoney'];?><br>
                    <?php endforeach;?>
                <?php }?>
                <?php if($detail['is_stock'] == 1){?>
                    <?php foreach ($detail['longproduct'] as $key=>$val):?>
                    <?php echo $val['stockmoney'];?><br>
                    <?php endforeach;?>
                <?php }?>
                </td>
                <td>
                	总：<?php echo $detail['totalmoney'];?><br/>本金：<?php echo $detail['con_money'];?><br/>利息：<?php echo round($detail['totalmoney']-$detail['con_money'],2);?>
                </td>
				<td>
				   	总:<?php echo $detail['money_plus_profit'];?><br/>本金:<?php echo round($detail['con_money']-$detail['longmoney'],2);?><br/>利息:<?php echo round($detail['money_plus_profit']-$detail['con_money']+$detail['longmoney'],2);?><br/>
				</td>
				<td>
				 	 总:<?php echo round($detail['longmoney']+$detail['longmoneyProfit'],2);?><br/>本金：<?php echo $detail['longmoney'];?><br/>利息：<?php echo round($detail['longmoneyProfit'],2);?>
				</td>
                <td>
                   <?php echo round($detail['moneyServer'],2);?>
                </td>
                <td>
                    <?php if(!empty($detail['product'])){?>
                        <?php foreach ($detail['product'] as $key=>$val):?>
                        <?php echo $val['cistime'];?><br>
                        <?php endforeach;?>
                    <?php }?>
                    <?php if(!empty($detail['longproduct'])){?>
                        <?php foreach ($detail['longproduct'] as $key=>$val):?>
                        <?php echo date('Y-m-d',$val['ctime']);?><br>
                        <?php endforeach;?>
                    <?php }?>
                </td>
                 <td>
                  <?php if(!empty($detail['product'])){?>
                    <?php foreach ($detail['product'] as $key=>$val):?>
                    <?php echo diff_days($val['cistime'], $detail['cietime']);?>天<br>
                    <?php endforeach;?>
                 <?php }?>
                 
                  <?php if(!empty($detail['longproduct'])){?>
                        <?php foreach ($detail['longproduct'] as $key=>$val):?>
                        <?php echo diff_days(date('Y-m-d',$val['ctime']), $detail['cietime']);?>天<br>
                        <?php endforeach;?>
                  <?php }?>
                </td>
                <td><?php echo diff_days(date('Y-m-d',$detail['remittime']), $detail['cietime']);?></td>
                <td><?php echo $detail['cietime'];?></td>
              </tr>
            </table>
            <fieldset class="EditField"><legend>回款本加息基本信息</legend>  
			<dl>
				<dt>回款本加息凭证</dt>
				<dd>
				 <input id="warrant_img_Input" type="file" name="warrant_img_file"
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
                                onUploadSuccess:uploadPicSuccess1,
                                onQueueComplete:uploadifyQueueComplete
                            }"
                        />
                    <input type="text" name="warrant_img" id="warrant_img" value="<?php echo $detail['warrant_img'];?>" class="filed-text" style="width: 500px;"/>
                    <a href='' id="_open_warrant_img" target="_blank"><img alt="图片预览" id="_show_warrant_img" src="" style="position: absolute;height: 30px;width: 30px;right: 150px;top:10px;"/></a>
				</dd>
			</dl>
			</fieldset>
			<fieldset class="EditField"><legend>服务费基本信息</legend>
			<dl>
				<dt>服务费金额：</dt>
				<dd><input name="service_money" type="text" size="30" value="<?php echo $detail['service_money'];?>" class="number"/>元</dd>
			</dl>
			<dl style="height: 150px;">
				<dt>上传服务费凭证：</dt>
				<dd>
				 <input id="service_image_Input" type="file" name="service_image_file"
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
                    <input type="text" name="service_image" id="service_image" value="<?php echo $detail['service_image'];?>" style="width: 400px;"/>
                    <a href='' id="_open_service_image" target="_blank"><img alt="图片预览" id="_show_service_image" src="<?php echo $detail['service_image'];?>" style="position: absolute;height:80px;width: 150px;right: 99px;top:76px;"/></a>
				</dd>
			</dl>
			<dl>
				<dt>服务费备注</dt>
				<dd><textarea name="service_note" cols="40" rows="4"><?php echo $detail['service_note'];?></textarea></dd>
			</dl>
			</fieldset>	
				
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="update" />
                <input type="hidden" name="bid" value="<?php echo $bid;?>" />
                <input type="hidden" name="cietime" value="<?php echo $detail['cietime'];?>" />
                <input type="hidden" name="repaymenttime" value="<?php echo $detail['repaymenttime'];?>" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>
<?php 
function diff_days($start, $end){
    list($a_year, $a_month, $a_day) = explode('-', $start);
    list($b_year, $b_month, $b_day) = explode('-', $end);
    $a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
    $b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
    return abs(($a_new-$b_new)/86400) + 1;
}
?>
<script type="text/javascript">
	$.pdialog.resizeDialog({style: {height: 550}}, $.pdialog.getCurrent(), "");
	$.pdialog.resizeDialog({style: {width: 1300}}, $.pdialog.getCurrent(), "");
	$.pdialog.resizeDialog({style: {left: 50}}, $.pdialog.getCurrent(), "");
	$.pdialog.resizeDialog({style: {top: 20}}, $.pdialog.getCurrent(), "");
	$('.shadow').css('display','none');
     function uploadPicSuccess1(file, data, response){
         $('#_show_warrant_img').attr('src', data);
         $('#warrant_img').val(data);
         $('#_open_warrant_img').attr('href', data);
     }

     function uploadPicSuccess2(file, data, response){
         $('#_show_service_image').attr('src', data);
         $('#service_image').val(data);
         $('#_open_service_image').attr('href', data);
     }
	 function closedialog(){
 		$.pdialog.closeCurrent();	
 	 }
	 function closedialog(json){
  		DWZ.ajaxDone(json);	
  		var $pagerForm = $("#pagerForm", navTab.getCurrentPanel());
		var args = $pagerForm.size()>0 ? $pagerForm.serializeArray() : {}
		navTabPageBreak(args, '');
  		$.pdialog.closeCurrent();
	 }
</script>
