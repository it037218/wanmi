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
    <form method="post" action="" class="pageForm required-validate" onSubmit="return validateCallback(this,navTabAjaxDone)">
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
                    <?php echo $detail['longProductIncome'];?><br>
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
				   	总:<?php echo $detail['money_plus_profit'];?><br/>本金:<?php echo round($detail['con_money']-$detail['longmoney'],2); //print_r($detail); ?><br/>利息:<?php echo round($detail['money_plus_profit']-$detail['con_money']+$detail['longmoney'],2);?><br/>
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
                    <?php echo diff_days($val['cistime'], $detail['cietime']);?><br>
                    <?php endforeach;?>
                 <?php }?>
                 
                  <?php if(!empty($detail['longproduct'])){?>
                        <?php foreach ($detail['longproduct'] as $key=>$val):?>
                        <?php echo diff_days(date('Y-m-d',$val['ctime']), $detail['cietime']);?><br>
                        <?php endforeach;?>
                  <?php }?>
                </td>
                <td><?php echo diff_days(date('Y-m-d',$detail['remittime']), $detail['cietime']);?></td>
                <td><?php echo $detail['cietime'];?></td>
              </tr>
            </table>
            <fieldset class="EditField" style="display:<?php if(empty($detail['warrant_img'])){echo 'none';}else{ echo 'black';} ?>"><legend>回款本加息基本信息</legend>
			<dl >
				<dt>回款本加息凭证</dt>
				<dd>
                    <a href="<?php echo $detail['warrant_img']?>" target="_bank"><img alt="图片预览" id="_show_warrant_img" src="<?php echo $detail['warrant_img']?>" style="height: 50px;width: 50px;"/></a>
				</dd>
			</dl>
			</fieldset>	
			<fieldset class="EditField" style="display:<?php if(empty($detail['service_image'])){echo 'none';}else{ echo 'black';} ?>"><legend>服务费基本信息</legend>
			<dl>
				<dt>服务费金额：</dt>
				<dd><?php echo $detail['service_money'];?></dd>
			</dl>
			<dl >
				<dt>服务费凭证</dt>
				<dd>
                    <a href="<?php echo $detail['service_image']?>" target="_bank"><img alt="图片预览" id="_show_service_image" src="<?php echo $detail['service_image']?>" style="height: 50px;width: 50px;"/></a>
				</dd>
			</dl>
			<dl>
				<dt>服务费备注</dt>
				<dd><?php echo $detail['service_note'];?></dd>
			</dl>
			</fieldset>	  
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="addproduct" />
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
     $.pdialog.resizeDialog({style: {height: 400}}, $.pdialog.getCurrent(), "");
     $.pdialog.resizeDialog({style: {width: 1300}}, $.pdialog.getCurrent(), "");
     $.pdialog.resizeDialog({style: {left: 50}}, $.pdialog.getCurrent(), "");
     $('.shadow').css('display','none');
</script>
