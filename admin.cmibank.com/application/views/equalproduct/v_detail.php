
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

<h2 class="contentTitle">产品详情</h2>
<div class="pageContent">
        <div class="pageFormContent nowrap" layoutH="97">

            <fieldset class="EditField"><legend>项目选择</legend>
                <dl>
                    <dt>项目类型:</dt> 
    			    <dd>
					<?php if($detail['ptid'] == $ptype['ptid']){ echo $ptype['name'];}?>
					</dd>
                </dl>
            </fieldset>
            
            <fieldset class="EditField"><legend>基本信息</legend>
                <dl>    
                    <dt>业务类型：</dt>
    			    <dd>
					<?php echo $usercontract['tplname'] . '-' . $usercontract['tplnumber'];?>
                    </dd>
                </dl>

                <dl>
                    <dt>产品名称:</dt>
                    <dd>
                        <?php echo $detail['pname'];?>
                    </dd>
                </dl>
                <hr />
                <dl>
                    <dt>预期收益:</dt>
                    <dd>
                        <?php echo $detail['income'];?>
                    </dd>
                </dl>
                <hr />
                <dl>
                    <dt>用户起息日</dt>
                    <dd>
                       <label style='width:auto;'><?php echo $detail['uistime'];?></label>
                        <label>至</label>
                        <label><?php echo $detail['uietime'];?></label>
                        <span class="info" id="uisday">&nbsp;&nbsp;理财期限<font color="#FF0000"><?php echo (strtotime($detail['uietime'])-strtotime($detail['uistime']))/86400+1;?></font>天</span>                 
                    </dd>
                </dl>
                <dl>
                    <dt>合作方起息日</dt>
                    <dd>
                       <label style='width:auto;'><?php echo $detail['cistime'];?></label>
                        <label>至</label>
                        <label><?php echo $detail['cietime'];?></label>
                        <span class="info" id="uisday">&nbsp;&nbsp;理财期限<font color="#FF0000"><?php echo (strtotime($detail['cietime'])-strtotime($detail['cistime']))/86400+1;?></font>天</span>   						
                    </dd>
                </dl>
                <hr />
                <dl>
                    <dt style="width:80px;">还款方式</dt>
                    <dd style="width:13%">
					按月偿还
                    </dd>
                    <dt style="width:80px;">每月还款日</dt>
                    <dd style="width:13%">
					<?php echo $detail['repaymentday'];?>
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">募集金额:</dt>
                    <dd style="width:13%">
                        ￥<?php echo $detail['money'];?>
                    </dd>
                    <dt style="width:80px;">起购金额:</dt>
                    <dd style="width:13%">
                        ￥<?php echo $detail['startmoney'];?>
                    </dd>
                </dl>
                <dl>
                    <dt style="width:80px;">累进金额:</dt>
                    <dd style="width:13%">
                        ￥<?php echo $detail['money_limit'];?>
                    </dd>
                    <dt style="width:80px;">购买限额:</dt>
                    <dd style="width:13%">
                        ￥<?php echo $detail['money_max'];?>
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
                <dl>
                    <dt style="width:80px;">买多少:</dt>
                    <dd style="width:13%">
                        <?php echo $detail['exp_buy'];?>
                    </dd>
                    <dt style="width:80px;">送多少:</dt>
                    <dd style="width:13%">
                        <?php echo $detail['exp_send'];?>
                    </dd>
                </dl>
            </fieldset>
            
            <fieldset class="EditField"><legend>合同信息</legend>
                <dl>
                    <dt>债权公司</dt>
                    <dd ><?php echo $contract['corname'];?></dd>
                </dl>
                 <dl>
                    <dt>合同编号</dt>
                    <dd ><?php echo $contract['con_number'];?></dd>
                </dl>
                <dl>
                    <dt>回款收益</dt>
                    <dd ><?php echo $contract['con_income'];?></dd>
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
<!--                 <dl> -->
<!--                     <dt>预告时间:</dt> -->
<!--                     <dd > -->
 <!--                       <?php echo $detail['yugaotime'];?>-->
<!--                     </dd> -->
<!--                 </dl> -->
                <dl>
                    <dt>允许超买:</dt>
                    <dd >
					<?php switch($detail['cancm']){
						case '1':
						echo '允许';break;
						case '2':
						echo '不允许';break;
					}?>
                    </dd>
                </dl>
            </fieldset>

        </div>
    </form>
</div>