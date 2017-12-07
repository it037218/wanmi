
<style>
.pageFormContent dl.nowrap dd, .nowrap dd {
    width: 500px;
}
</style>
<script type="text/javascript">

$(function() {
	var type = <?php echo $detail['type'] ;?>;
	if(type==2){
		$("#coupondiv").css({ "display": "inline" });
		$("#luckbag").css({ "display": "none" });
		$('#_show_goods').attr('src', 'http://static1.cmibank.com/images/icon_jifen_dyq.png');
        $('#goods_img').val('http://static1.cmibank.com/images/icon_jifen_dyq.png');
	}else{
		$("#coupondiv").css({ "display": "none" });
		$("#luckbag").css({ "display": "inline" });
		if($(this).val()==1){
			$('#_show_goods').attr('src', 'http://static1.cmibank.com/images/icon_jifen_tyj.png');
	        $('#goods_img').val('http://static1.cmibank.com/images/icon_jifen_tyj.png');
		}else if($(this).val()==3){
			$('#_show_goods').attr('src', 'http://static1.cmibank.com/images/icon_jifen_hb.png');
	        $('#goods_img').val('http://static1.cmibank.com/images/icon_jifen_hb.png');
		}
	}
	$('input:radio').click( function () {
		if($(this).val()==2){
			$("#coupondiv").css({ "display": "inline" });
			$("#luckbag").css({ "display": "none" });
			$('#_show_goods').attr('src', 'http://static1.cmibank.com/images/icon_jifen_dyq.png');
	        $('#goods_img').val('http://static1.cmibank.com/images/icon_jifen_dyq.png');
		}else{
			$("#coupondiv").css({ "display": "none" });
			$("#luckbag").css({ "display": "inline" });
			if($(this).val()==1){
				$('#_show_goods').attr('src', 'http://static1.cmibank.com/images/icon_jifen_tyj.png');
		        $('#goods_img').val('http://static1.cmibank.com/images/icon_jifen_tyj.png');
			}else if($(this).val()==3){
				$('#_show_goods').attr('src', 'http://static1.cmibank.com/images/icon_jifen_hb.png');
		        $('#goods_img').val('http://static1.cmibank.com/images/icon_jifen_hb.png');
			}
		}
	});
});

</script>
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>goods/editGoods" class="pageForm required-validate" onsubmit="return validateCallback(this,closedialog)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>商品名称：</dt>
                <dd>
                	<input type="text" class="filed-text required" name="name" value="<?php echo $detail['name'] ;?>"/>
                </dd>
            </dl>	
            <dl>    
                <dt>销售价格：</dt>
                <dd>
                    <input type="text" class="required digits" name="jifeng" value="<?php echo $detail['jifeng'] ;?>"/>
                </dd>
            </dl>
            <dl>    
                <dt>原始价格：</dt>
                <dd>
                    <input type="text" class="required digits" name="yuanjifeng" value="<?php echo $detail['yuanjifeng'] ;?>"/>
                </dd>
            </dl>
            <dl>    
                <dt>库存：</dt>
                <dd>
                    <input type="text" class="required digits" name="stock" value="<?php echo $detail['stock']-$detail['sold'] ;?>"/>
                </dd>
            </dl>
            <dl>    
                <dt>商品类型：</dt>
                 <dd>
                    <table>
                    	<tr style="height: 25px">
                    		<td><input name="type" type="radio" id="type_radio" style="float:left" value="1" <?php if($detail['type'] == 1){ echo 'checked';}?>/>体验金</td>
                    		<td><input name="type" type="radio" id="type_radio" style="float:left" value="2" <?php if($detail['type'] == 2){ echo 'checked';}?>/>抵用券</td>
                    	</tr>
                    	<tr style="height: 25px">
	                    	<td><input name="type" type="radio" id="type_radio" style="float:left" value="3" <?php if($detail['type'] == 3){ echo 'checked';}?>/>邀请红包</td>
	                    	<td><input name="type" type="radio" id="type_radio" style="float:left" value="4" <?php if($detail['type'] == 4){ echo 'checked';}?>/>实物</td>
                    	</tr>
                    </table>
                </dd>
            </dl>
            <dl id="coupondiv">    
            	<dt>&nbsp&nbsp</dt>
                 <dd style="border: solid 1px #b8d0d6">
                 	<div id='couponList' >
	                	 <?php if(!empty($couponList)){?>
	            			<?php foreach($couponList AS $key=>$value){?>
			                	<div style="height: 25px">
		                			<label style="width: 100%"><input type="radio" name="coupon_jifeng" value="<?php echo $value['id'] ;?>" <?php if($detail['wid'] == $value['id']){ echo 'checked';}?>/><?php echo $value['name'].'， '.$value['sendmoney'].'元，起购'.$value['minmoney'].'，适用于'.$value['pnames'];?></label>
			                	</div>
							<?php }?>
						<?php }?>
                 	</div>
                </dd>
            </dl>
            <dl id="luckbag">    
                <dt>金额：</dt>
                <dd>
                    <input type="text"  name="money" value="<?php echo $detail['money'] ;?>"/>
                </dd>
            </dl>
            <dl>    
                <dt style="width:80px;">商品图片:</dt>
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
                                onUploadSuccess:uploadPicSuccess,
                                onQueueComplete:uploadifyQueueComplete
                            }"
                        />
                    <input type="text" name="img" id="goods_img" value="<?php echo $detail['img'] ;?>" class="filed-text" style="width: 300px;" />
                    <img alt="图片预览" id="_show_goods" src="<?php echo $detail['img'] ;?>" style="position: absolute;height: 30px;width: 30px;right: 150px;top:10px;"/>
                    </dd>
            </dl>
            <dl>    
                <dt>排序：</dt>
                <dd>
                    <input type="text" class="required" name="rank" value="<?php echo $detail['rank'] ;?>"/>
                </dd>
            </dl>
            <dl>
				<dt>商品描述:</dt>
                    <dd >
                        <textarea style="width:100%;height:50px" name="desc" id="desc"><?php echo $detail['desc'] ;?></textarea>
                    </dd>
                </dl>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="editGoods" />
                <input type="hidden" name="id" value="<?php echo $detail['id'] ;?>" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     $.pdialog.resizeDialog({style: {height: 600}}, $.pdialog.getCurrent(), "");
     $.pdialog.resizeDialog({style: {width: 700}}, $.pdialog.getCurrent(), "");
     $.pdialog.resizeDialog({style: {top: 50}}, $.pdialog.getCurrent(), "");

     function uploadifyQueueComplete(queueData){}
     function uploadPicSuccess(file, data, response){
         $('#_show_goods').attr('src', data);
         $('#goods_img').val(data);
     }
	 
	 function closedialog(json){
	  		$.pdialog.closeCurrent();	
	  		navTabAjaxDone(json);
 	 }
</script>
