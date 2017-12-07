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
    <form method="post" action="<?php echo OP_DOMAIN;?>longproduct/addLongproduct" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <fieldset class="EditField"><legend>项目选择</legend>
                <dl>
                    <dt style="width:80px;">项目选择:</dt>
                    <dd style="width:13%">
                        <select name="ptid" id="ptid">
                            <option value="0">请选择项目</option>
                            <?php if(!empty($ltype_list)){ ?>
                                <?php foreach($ltype_list as $index => $name){ ?>
                                    <option value="<?php echo $index ;?>"><?php echo $name; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </dd>
                    <dt style="width:80px;">活期合同模板</dt>
                    <dd style="width:13%">
                        <select class="combox" onchange='ltidchange()' id="classid_group" name="cid">
                            <option value="0">请选择活期合同模板</option>
                            <?php if(!empty($ltype_list)){ ?>
                                <?php foreach($longproductcontract_list as $index => $name){ ?>
                                    <option value="<?php echo $index ;?>"><?php echo $name; ?></option>
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
                        <input type="text" class="required" name="pname" id="pname"/><span class="info" onclick="autotianchong();">自动填充</span>
                    </dd>
                </dl>
         
                <dl>
                    <dt>预期收益:</dt>
                    <dd>
                        <input type="text" class="required" readonly="true" name="income" id="income" />
                    </dd>
                </dl>

                <dl>
                    <dt style="width:80px;">募集金额:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="money" id="money" onblur="changnumber()"/><span class="info" id="num"></span>
                    </dd>
                    <dt style="width:80px;">起购金额:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="startmoney" id="startmoney"/>
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">累进金额:</dt>
                    <dd style="width:13%">
                        <input type="text" class="required" name="money_limit" id="money_limit"/>
                    </dd>
                    <dt style="width:80px;">购买限额:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="money_max" id="money_max"/>
                    </dd>
                </dl>
                <hr />
                <dl>
                    <dt style="width:80px;">运营标签:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="operation_tag" id="operation_tag"/>
                    </dd>
                     <dt>运营标签图片:</dt>
                    <dd>
                        <select name="standard_icon" id="standard_icon">
                             <option value="xinshoubiao_hong" >红色</option>
                            <option value="yugao_lan">蓝色</option>
                            <option value="tuijian_huang">黄色</option>
                            <option value="shouwan">灰色</option>
                            <option value="">空白</option>
                        </select>
                    </dd>
                </dl>
                <dl>
                   <dt style="width:80px;">标准标签:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="standard_tag" id="standard_tag"/>
                    </dd>
                     <dt style="width:80px;">标准标签文本:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="standard_text" id="standard_text" />
                    </dd>
                 </dl>
                 <dl>
                    <dt style="width:80px;">文本标题:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="text_text" id="text_text"/>
                    </dd>
                    <dt style="width:80px;">文本链接:</dt>
                    <dd style="width:13%">
                        <input type="text"  name="text_url" id="text_url"/>
                    </dd>
                </dl>
            </fieldset>
            
            </fieldset>
            <fieldset class="EditField"><legend>项目描述</legend>
                <dl class="nowrap">
			         <dt style="width="100px;">　</dt>
			         <dd><textarea style="width:150%;height:200px" name="object_overview" id="object_overview" cols="80" rows="10" readonly="true"></textarea></dd>
			         
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
                        <select name="canbuyuser" id="canbuyuser" class="combox">
                            <option value="1">无限制</option>
                            <option value="2">新用户</option>
                            <option value="3">老用户</option>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>自动上线时间:</dt>
                    <dd >
                        <input type="text" class="date" name="online_time" id='online_time' dateFmt="yyyy-MM-dd HH:mm" onclick='onlinetimechange()'/>
                    </dd>
                </dl>
                <dl>
                    <dt>允许超买:</dt>
                    <dd >
                        <label><input type="radio" name="cancm" id="cancm" checked="checked" value="1" />允许</label>
                        <label><input type="radio" name="cancm" value="2" />不允许</label>
                    </dd>
                </dl>
            </fieldset>
        </div>
        <div class="formBar">
            <ul>
                <input type="hidden" name="op" value="addlongproduct" />
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
             url : '<?php echo OP_DOMAIN; ?>/longproductcontract/getLongproductcontractByCid/' + cid,
             data : '',
             dataType : 'json',
             async : false,
             success : function(data){
            	 $('#object_overview').val(data.object_overview);
            	 $('#capital_overview').val(data.capital_overview);
            	 $('#object_img').val(data.object_img);
            	 $('#income').val(data.income);
            	 $('#capital_img').val(data.capital_img);
            	 $('#_show_object').attr('src',data.object_img);
            	 $('#_show_capital').attr('src',data.capital_img);
             }
         });
     }
     function autotianchong(){
    	 var pname = $('#pname').val();
    	 if(pname==''){
        	 alert("没有数据");
        	 return false;
         }
    	 
    	 $.ajax({
             type : 'POST',
             url : '<?php echo OP_DOMAIN; ?>/longproduct/autotianchong/',
             data : 'pname='+pname,
             dataType : 'json',
             async : false,
             success : function(data){
            	 $('#ptid').val(data.ptid);
            	 $('#pname').val(data.pname);
            	 $('#money').val(data.money);
            	 $('#startmoney').val(data.startmoney);
            	 $('#money_limit').val(data.money_limit);
            	 $('#money_max').val(data.money_max);
            	 $('#standard_icon').val(data.standard_icon);
            	 $('#standard_text').val(data.standard_text);
            	 $('#operation_tag').val(data.operation_tag);
            	 $('#standard_tag').val(data.standard_tag);
            	 $('#text_text').val(data.text_text);
            	 $('#text_url').val(data.text_url);
            	 alert("数据填充完毕");
             }
         });
    	 
     }
     function changnumber(){
 		//以下为测试输出
         var day = DX($('#money').val())
         var content = '<span class="info" id="num">' + day + '</span>'
         $('#num').html(content);
   }
     //主函数
     function DX(n) {
         if (!/^(0|[1-9]\d*)(\.\d+)?$/.test(n))
         return "数据非法";
         var unit = "千百拾亿千百拾万千百拾元角分", str = "";
         n += "00";
         var p = n.indexOf('.');
         if (p >= 0)
         n = n.substring(0, p) + n.substr(p+1, 2);
         unit = unit.substr(unit.length - n.length);
         for (var i=0; i < n.length; i++)
         str += '零壹贰叁肆伍陆柒捌玖'.charAt(n.charAt(i)) + unit.charAt(i);
         return str.replace(/零(千|百|拾|角)/g, "零").replace(/(零)+/g, "零").replace(/零(万|亿|元)/g, "$1").replace(/(亿)万|壹(拾)/g, "$1$2").replace(/^元零?|零分/g, "").replace(/元$/g, "元整");
   }
</script>
