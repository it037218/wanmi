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
            <?php if(!empty($value['warrant_img'])){?>
                <dl>
                    <dt style="width:300px">凭证图片(<?php if($value['ctime'] == 0){ echo "暂无时间";}else{ echo date('Y-m-d',$value['ctime']);} ?>):</dt>
                    <dd>
                        <a href="<?php echo $value['warrant_img']?>" target="_blank"><img src = "<?php echo $value['warrant_img']?>" width="400" height="100"></a> 
                    </dd>
                </dl>
                <?php }?>
           <?php endforeach;?>
                                      
                         
                      
        </div>
</div>


