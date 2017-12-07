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
<h2 class="contentTitle">给合作方打款</h2>
<div class="pageContent">
    <form method="post" action="<?php echo OP_DOMAIN;?>remit/doremit" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
        <div class="pageFormContent nowrap" layoutH="97">
            <fieldset class="EditField"><legend>产品信息</legend>
                <dl>
                    <dt>公司名称</dt>
                    <dd>
                        <?php echo $contract['corname']; ?>
                    </dd>
                </dl>
                <dl>
                    <dt>产品名称</dt>
                    <dd>
                        <?php echo '活期产品'.$stock_product['sid']; ?>
                    </dd>
                </dl>
                <dl>
                    <dt>打款金额</dt>
                    <dd>
                        <?php echo $stock_product['stockmoney']; ?>
                    </dd>
                </dl>
            </fieldset>
            
            <fieldset class="EditField"><legend>打款信息</legend>
                <dl>    
                    <dt>账户名称</dt>
    			    <dd>
                        <?php echo $corporation['ccname']; ?>
                    </dd>
                </dl>
                <dl>    
                    <dt>账户号码</dt>
    			    <dd>
                        <?php echo $corporation['ccard']; ?>
                    </dd>
                </dl>
                <dl>
                    <dt>开户银行:</dt>
                    <dd>
                        <?php echo $corporation['bankname']; ?>
                    </dd>
                </dl>
                <dl>
                    <dt>开户支行:</dt>
                    <dd>
                        <?php echo $corporation['subbank']; ?>
                    </dd>
                </dl>
                <dl>
                    <dt>支行行号:</dt>
                    <dd>
                        <?php echo $corporation['banknum']; ?>
                    </dd>
                </dl>
            </fieldset>
            <fieldset class="EditField"><legend>实际打款</legend>
                <dl>    
                    <dt>原打款金额</dt>
    			    <dd>
                        <?php echo $stock_product['stockmoney']; ?>
                    </dd>
                </dl>
                <dl>    
                    <dt>保证金比率</dt>
    			    <dd>
                      <?php echo $contract['con_bzjbl']; ?>%
                    </dd>
                </dl>
                <dl>
                    <dt>保证金金额:</dt>
                    <dd>
                        <?php 
                            echo $stock_product['stockmoney']*$contract['con_bzjbl']/100;
                        ?>
                    </dd>
                </dl>
                <dl>
                    <dt>实际打款金额:</dt>
                    <dd>
                        <?php
                            echo $stock_product['stockmoney'];
                        ?>
                    </dd>
                </dl>
            </fieldset>
            <fieldset class="EditField"><legend>打款凭证</legend>
            <dl>
                <dt style="width:80px;">凭证图片:</dt>
                <dd>
                    <img src="<?php if(!empty($stock_product['warrant_img'])){echo $stock_product['warrant_img'];} ?>"  />
                </dd>
            </dl>
            <dl>
                <dt>备注:</dt>
                <dd>
                     <?php if(!empty($stock_product['des'])){echo $stock_product['des'];} ?>
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
