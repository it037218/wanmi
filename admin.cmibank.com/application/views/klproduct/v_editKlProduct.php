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
<h2 class="contentTitle">上传快乐产品</h2>
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>klproduct/editKlproduct" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <fieldset class="EditField"><legend>项目选择</legend>
                <dl>
                    <dt style="width:80px;">项目选择:</dt>
                    <dd style="width:13%">
                        <select name="ptid" class="combox">
                            <option value="0">请选择项目</option>
                            <?php if(!empty($kltype_list)){ ?>
                                <?php foreach($kltype_list as $index => $name){ ?>
                                    <option value="<?php echo $index ;?>" <?php if($index == $detail['ptid']){ echo 'selected';}?>><?php echo $name; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </dd>
                    <dt style="width:100px;">快乐宝合同模板</dt>
                    <dd style="width:13%">
                        <select class="combox" id="classid_group" name="cid" onchange='ltidchange()'>
                            <option value="0">请选择快乐宝合同模板</option>
                            <?php print_r('$klproductcontract_list');?>
                            <?php if(!empty($kltype_list)){ ?>
                                <?php foreach($klproductcontract_list as $index => $name){ ?>
                                   <option value="<?php echo $index ;?>" <?php if($index == $detail['cid']){ echo 'selected';}?>><?php echo $name; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </dd>
                </dl>
            </fieldset>
            
            <fieldset class="EditField"><legend>基本信息</legend>
                <dl>
                    <dt>产品名称:</dt>
                    <dd>
                        <input type="text" class="required" name="pname" value="<?php echo $detail['pname'];?>"/>
                    </dd>
                </dl>
         
                <dl>
                    <dt>预期收益:</dt>
                    <dd>
                        <input type="text" class="required" readonly="true" name="income" value="<?php echo $detail['income'];?>"/>
                    </dd>
                </dl>
 
                
                
                <dl>
                    <dt style="width:80px;">募集金额:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="money" value="<?php echo $detail['money'];?>"/>
                    </dd>
                    <dt style="width:80px;">起购金额:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="startmoney" value="<?php echo $detail['startmoney'];?>"/>
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">累进金额:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="money_limit" value="<?php echo $detail['money_limit'];?>"/>
                    </dd>
                    <dt style="width:80px;">购买限额:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="money_max" value="<?php echo $detail['money_max'];?>"/>
                    </dd>
                </dl>
                <hr />
                <dl>
                    <dt style="width:80px;">运营标签:</dt>
                    <dd style="width:13%">
                        <input type="text" name="operation_tag" value="<?php echo $detail['operation_tag'];?>" />
                    </dd>
                    <dt>运营标签图片</dt>
                    <dd>
                        <select name="standard_icon" id="standard_icon">
                            <option value="xinshoubiao_hong" <?php if($detail['standard_icon'] == 'xinshoubiao_hong'){ echo "selected";}?> >红色</option>
                            <option value="yugao_lan" <?php if($detail['standard_icon'] == 'yugao_lan'){ echo "selected";}?>>蓝色</option>
                            <option value="tuijian_huang" <?php if($detail['standard_icon'] == 'tuijian_huang'){ echo "selected";}?>>黄色</option>
                            <option value="shouwan" <?php if($detail['standard_icon'] == 'shouwan'){ echo "selected";}?>>灰色</option>
                            <option value="" <?php if($detail['standard_icon'] == ''){ echo "selected";}?>>白色</option>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">标准标签:</dt>
                    <dd style="width:13%">
                        <input type="text" name="standard_tag" value="<?php echo $detail['standard_tag'];?>"/>
                    </dd>
                    <dt style="width:80px;">标准标签文本:</dt>
                    <dd style="width:13%">
                        <input type="text" name="standard_text" value="<?php echo $detail['standard_text'];?>"/>
                    </dd>
                 </dl>
                 <dl>
                    <dt style="width:80px;">文本标题:</dt>
                    <dd style="width:13%">
                        <input type="text" name="text_text" id="text_text" value="<?php echo $detail['text_text'];?>"/>
                    </dd>
                    <dt style="width:80px;">文本链接:</dt>
                    <dd style="width:13%">
                        <input type="text" name="text_url" id="text_url" value="<?php echo $detail['text_url'];?>"/>
                    </dd>
                </dl>
            </fieldset>
            <fieldset class="EditField"><legend>项目描述</legend>
                <dl class="nowrap">
			         <dt style="width="100px;">　</dt>
			         <dd><textarea style="width:150%;height:200px" name="object_overview" id="object_overview" cols="80" rows="10" readonly="true"></textarea></dd>
			         
		        </dl>
		        <dl>
		          <dt>项目图片</dt>
			         <dd>
			              <input type="text" name="object_img" id="object_img" class="filed-text" style="width: 500px;" readonly="true"/>
                          <img alt="图片预览" id="_show_object" style="position: absolute;height: 200px;width: 200px;right: 220px;top:-210px;"/>
			         </dd>
		        </dl>
            </fieldset>
            
            <fieldset class="EditField"><legend>资金保障</legend>
                 <dl class="nowrap">
			         <dt style="width="100px;">　</dt>
			         <dd><textarea style="width:150%;height:200px" name="capital_overview" id="capital_overview" cols="80" rows="10" readonly="true"></textarea></dd>
		        </dl>
		        <dl>
		          <dt>资金保障</dt>
			         <dd>
			              <input type="text" name="object_img" id="capital_img" class="filed-text" style="width: 500px;" readonly="true"/>
                             <img alt="图片预览" id="_show_capital" style="position: absolute;height: 200px;width: 200px;right: 220px;top:-210px;"/>
			         </dd>
		        </dl>
            </fieldset>
            
            <fieldset class="EditField"><legend>发布设置</legend>
                <dl>
                    <dt>可购买用户<?php echo $detail['canbuyuser'];?></dt>
                    <dd >
                        <select name="canbuyuser" class="combox" >
                            <option value="1"  <?php if($detail['canbuyuser'] == 1){ echo 'selected';}?>>无限制</option>
                            <option value="2"  <?php if($detail['canbuyuser'] == 2){ echo 'selected';}?>>新用户</option>
                            <option value="3"  <?php if($detail['canbuyuser'] == 3){ echo 'selected';}?>>老用户</option>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>自动上线时间:</dt>
                    <dd >
                        <input type="text" class="date" name="online_time" id='online_time' dateFmt="yyyy-MM-dd HH:mm" value="<?php echo $detail['online_time']?>"/>
                    </dd>
                </dl>
                <dl>
                    <dt>允许超买:</dt>
                    <dd >
                        <label><input type="radio" name="cancm" value="1" <?php if($detail['cancm']==1){ echo 'checked="checked"';}?>/>允许</label>
                        <label><input type="radio" name="cancm" value="2" <?php if($detail['cancm']==2){ echo 'checked="checked"';}?>/>不允许</label>
                    </dd>
                </dl>
            </fieldset>
        </div>
        <div class="formBar">
            <ul>
                 <input type="hidden" name="op" value="editKlproduct" />
                 <input type="hidden" name="pid" value="<?php echo $detail['pid']; ?>" />
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>
</div>

<script type="text/javascript">
     function ltidchange(){
    	 var cid = $('#classid_group').val();
    	 $.ajax({
             type : 'POST',
             url : '<?php echo OP_DOMAIN; ?>/klproductcontract/getKlproductcontractByCid/' + cid,
             data : '',
             dataType : 'json',
             async : false,
             success : function(data){
            	 $('#object_overview').val(data.object_overview);
            	 $('#capital_overview').val(data.capital_overview);
            	 $('#object_img').val(data.object_img);
            	 $('#capital_img').val(data.capital_img);
            	 $('#_show_object').attr('src',data.object_img);
            	 $('#_show_capital').attr('src',data.capital_img);
             }
         });
     }
     ltidchange();
</script>
