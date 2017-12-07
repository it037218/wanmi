<div class="pageContent">
	<form method="post" action="<?php echo OP_DOMAIN;?>stockproduct/StockLongmoney" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="56">
		<fieldset class="EditField"><legend>合同信息</legend>
              <p>
				<label>公司名称：</label>
               <?php echo $contract['corname'];?>
			</p>
			<p>
				<label>合同编号：</label>
               <?php echo $contract['con_number'];?>
			</p>
			<p>
				<label>合同资金：</label>
               <?php echo $contract['money'];?> 
			</p>
            </fieldset>
             <?php $oldname = ''; ?>
            <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <?php switch ($value['status']){
                case 0: $status = '末上架';break;
                case 1: $status = '已上架';break;
                case 2: $status = '已下架';break;
                case 3: $status = '售罄';break;
                case 4: $status = '停售';break;
                default: $status = '末知状态'; break;
            }?>
            <fieldset class="EditField"><legend>产品信息</legend>
              <p>
				<label>产品名称：</label>
				<?php echo $value['pname'];?>
			</p>
			<p>
				<label>募集资金：</label>
				<?php echo $value['money'];?>
			</p>
			<p>
				<label>产品状态：</label>
				<?php echo $status;?>
			</p>
            </fieldset>
            <?php endforeach;?>
		    <?php endif;?>

		</div>
		<div class="formBar">
			<ul>
			    <input type="hidden" name="op" value="stock" />
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>


