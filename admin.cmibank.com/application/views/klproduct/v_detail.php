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
<h2 class="contentTitle">上传活期产品</h2>
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>klproduct/addKlproduct" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <fieldset class="EditField"><legend>项目选择</legend>
                <dl>
                    <dt style="width:80px;">项目:</dt>
                    <dd style="width:13%">
                           
                            <?php if(!empty($ltype_list)){ ?>
                                <?php foreach($ltype_list as $index => $name){ ?>
                                    <?php if($index == $detail['ptid']){ echo $name;}?>
                                <?php } ?>
                            <?php } ?>
                      
                    </dd>
                    <dt style="width:80px;">活期合同模板</dt>
                    <dd style="width:13%">
                            <?php if(!empty($ltype_list)){ ?>
                                <?php foreach($klproductcontract_list as $index => $name){ ?>
                                 <?php if($index == $detail['cid']){ echo $name;}?>
                                <?php } ?>
                            <?php } ?>
                       
                    </dd>
                </dl>
            </fieldset>
            
            <fieldset class="EditField"><legend>基本信息</legend>
                <dl>
                    <dt>产品名称:</dt>
                    <dd>
                        <?php echo $detail['pname'];?>
                    </dd>
                </dl>
         
                <dl>
                    <dt>预期收益:</dt>
                    <dd>
                        <?php echo $detail['income'];?>
                    </dd>
                </dl>
                
                <dl>
                    <dt style="width:80px;">募集金额:</dt>
                    <dd style="width:13%">
                        <?php echo $detail['money'];?>
                    </dd>
                    <dt style="width:80px;">起购金额:</dt>
                    <dd style="width:13%">
                        <?php echo $detail['startmoney'];?>
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">累进金额:</dt>
                    <dd style="width:13%">
                        <?php echo $detail['money_limit'];?>
                    </dd>
                    <dt style="width:80px;">购买限额:</dt>
                    <dd style="width:13%">
                        <?php echo $detail['money_max'];?>
                    </dd>
                </dl>
                <hr />
                <dl>
                    <dt style="width:80px;">运营标签:</dt>
                    <dd style="width:13%">
                        <?php echo $detail['operation_tag'];?>
                    </dd>
                    <dt style="width:80px;">标准标签图片:</dt>
                    <dd style="width:13%">
                        <?php 
                            if($detail['standard_icon'] =='xinshoubiao_hong'){
                                echo '红色';
                            }else if($detail['standard_icon'] =='yugao_lan'){
                                echo '蓝色';
                            }else if($detail['standard_icon'] =='tuijian_huang'){
                                echo '黄色';
                            }else if($detail['standard_icon'] ==''){
                                echo '白色';
                            }
                        ?>
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">标准标签:</dt>
                    <dd style="width:13%">
                        <?php echo $detail['standard_tag'];?>
                    </dd>
                    <dt style="width:80px;">标准标签文本:</dt>
                    <dd style="width:13%">
                        <?php echo $detail['standard_text'];?>
                    </dd>
                 </dl>
                 <dl>
                    <dt style="width:80px;">文本标题:</dt>
                    <dd style="width:13%">
                        <?php echo $detail['text_text'];?>
                    </dd>
                    <dt style="width:80px;">文本链接:</dt>
                    <dd style="width:13%">
                       <?php echo $detail['text_url'];?>
                    </dd>
                </dl>
                
                 
            </fieldset>
            
            </fieldset>
            <fieldset class="EditField"><legend>项目描述</legend>
                <dl class="nowrap">
			         <dt style="width="100px;">　</dt>
			         <dd><textarea style="width:150%;height:200px" name="object_overview" id="object_overview" cols="80" rows="10" readonly="true"></textarea></dd>
			         
		        </dl>
		        <dl class="nowrap">
			         <dt style="width="100px;">　</dt>
			         <dd><textarea style="width:150%;height:200px" name="object_desc" id="object_desc" cols="80" rows="10" readonly="true"></textarea></dd>
			         
		        </dl>
		        <dl>
		          <dt>项目图片</dt>
			         <dd>
			              <input type="text" name="object_img" id="object_img" class="filed-text" style="width: 500px;"/>
                          <img alt="图片预览" id="_show_object" style="position: absolute;height: 200px;width: 200px;right: 220px;top:-210px;"/>
			         </dd>
		        </dl>
            </fieldset>
            
            <fieldset class="EditField"><legend>资金保障</legend>
                 <dl class="nowrap">
			         <dt style="width="100px;">　</dt>
			         <dd><textarea style="width:150%;height:200px" name="capital_overview" id="capital_overview" cols="80" rows="10" readonly="true"></textarea></dd>
		        </dl>
		        <dl class="nowrap">
			         <dt style="width="100px;">　</dt>
			         <dd><textarea style="width:150%;height:200px" name="capital_desc" id="capital_desc" cols="80" rows="10" readonly="true"></textarea></dd>
		        </dl>
		        <dl>
		          <dt>资金保障</dt>
			         <dd>
			              <input type="text" name="object_img" id="capital_img" class="filed-text" style="width: 500px;"/>
                             <img alt="图片预览" id="_show_capital" style="position: absolute;height: 200px;width: 200px;right: 220px;top:-210px;"/>
			         </dd>
		        </dl>
            </fieldset>
            
            <fieldset class="EditField"><legend>发布设置</legend>
                <dl>
                    <dt>可购买用户</dt>
                    <dd >
                       <?php switch($detail['canbuyuser']){
						case '1':
						echo '无限制';break;
						case '2':
						echo '新用户';break;
						default: '老用户';
					}?>
                    </dd>
                </dl>
                <dl>
                    <dt>自动上线时间:</dt>
                    <dd >
                        <?php echo $detail['online_time'];?>
                    </dd>
                </dl>
                <dl>
                    <dt>允许超买:</dt>
                    <dd >
                        <?php switch($detail['canbuyuser']){
						case '1':
						echo '允许';break;
						case '2':
						echo '不允许';break;
						default: '允许';
					}?>
                    </dd>
                </dl>
            </fieldset>
        </div>
        <div class="formBar">
            <ul>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     function chang(){
    	 var cid = <?php echo $detail['cid'];?>;
    	 $.ajax({
             type : 'POST',
             url : '<?php echo OP_DOMAIN; ?>/klproductcontract/getKlproductcontractByCid/' + cid,
             data : '',
             dataType : 'json',
             async : false,
             success : function(data){
            	 $('#object_overview').val(data.object_overview);
            	 $('#capital_overview').val(data.capital_overview);
            	 $('#object_desc').val(data.object_desc);
            	 $('#capital_desc').val(data.capital_desc);
            	 $('#object_img').val(data.object_img);
            	 $('#capital_img').val(data.capital_img);
            	 $('#_show_object').attr('src',data.object_img);
            	 $('#_show_capital').attr('src',data.capital_img);
             }
         });
     }
     chang();
</script>
