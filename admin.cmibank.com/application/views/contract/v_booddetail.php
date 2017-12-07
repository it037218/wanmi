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
        <div class="pageFormContent nowrap" layoutH="97">
  
           <?php foreach($list AS $key=>$value):?>
            <?php if(!empty($value['warrant_img']) and strpos($value['warrant_img'],'upload')){?>
                <dl>
                    <dt style="width:300px">打款凭证图片(<?php if($value['ctime'] == 0){ echo "暂无时间";}else{ echo date('Y-m-d',$value['ctime']);} ?>):</dt>
                    <dd>
                        <a href="<?php echo $value['warrant_img']?>" target="_blank"><img src = "<?php echo $value['warrant_img']?>" width="400" height="100"></a> 
                    </dd>
                </dl>
                <?php break; }?>
           <?php endforeach;?>
           <?php if(!empty($backmoney)){?>     
           	<?php if(!empty($backmoney[0]['warrant_img'])){?>    
                      <dl>
                    <dt style="width:300px">回款凭证图片:</dt>
                    <dd>
                        <a href="<?php echo $backmoney[0]['warrant_img']?>" target="_blank"><img src = "<?php echo $backmoney[0]['warrant_img']?>" width="400" height="100"></a> 
                    </dd>
                </dl>  
                <?php }?>
                <?php if(!empty($backmoney[0]['service_image'])){?>    
                 <dl>
                    <dt style="width:300px">服务费凭证图片:</dt>
                    <dd>
                        <a href="<?php echo $backmoney[0]['service_image']?>" target="_blank"><img src = "<?php echo $backmoney[0]['service_image']?>" width="400" height="100"></a> 
                    </dd>
                </dl>   
                <?php }?>       
           <?php }?>
              <?php if(!empty($contract['bzjimg'])){?>     
                      <dl>
                    <dt style="width:300px">缴纳保证金图片:</dt>
                    <dd>
                        <a href="<?php echo $contract['bzjimg']?>" target="_blank"><img src = "<?php echo $contract['bzjimg']?>" width="400" height="100"></a> 
                    </dd>
                </dl>  
                <?php }?>
                 <?php if(!empty($contract['returnbzjimg'])){?>     
                 <dl>
                    <dt style="width:300px">退还保证金凭证图片:</dt>
                    <dd>
                        <a href="<?php echo $contract['returnbzjimg']?>" target="_blank"><img src = "<?php echo $contract['returnbzjimg']?>" width="400" height="100"></a> 
                    </dd>
                </dl>          
           <?php }?>        
        </div>
</div>


