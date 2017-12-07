<!DOCTYPE html>
<!-- saved from url=(0094)http://api.cmibank.com/jcc/AdminAgreementsTemplate?page=check&product_id=6623&user_id=1287492 -->
<html><head lang="zh-cn"><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>用户购买协议</title>
    <link rel="stylesheet" type="text/css" src="/jcc/admin/css/agreement_template.css">
</head>
<body>
	<div id="agreement_template" style="margin-left:30px;margin-right:30px;">
	
    <title>购买协议-债权受益权转让协议</title>
    
    <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" name="viewport" id="viewport">
    <link rel="stylesheet" type="text/css" src="/jcc/admin/css/agreement_template.css">

	<div>
    	<h1>债权转让协议</h1>
    	
<p>甲方（出让方）：<?php echo isset($info['croname']) ? $info['croname'] : 'XXXX公司'; ?></p>

<p>身份证号：--</p>

<p>乙方（受让方）：<?php echo isset($info['uname']) ? $info['uname'] : 'XXXX用户'; ?></p>

<p>身份证号：<?php echo isset($info['idcard']) ? $info['idcard'] : '000000000000000000'; ?></p>

<p>丙方：（第三方服务机构）：<?php echo isset($info['ourcompany']) ? $info['ourcompany'] : '万米财富管理有限公司';?></p>

<p>网站：“易米融理财”（域名：www.cmibank.com）</p>

<p>地址：<?php echo isset($info['address']) ? $info['address'] : 'XXXX'; ?></p>

<p>法定代表人：<?php echo isset($info['faren']) ? $info['faren'] : 'XXXX'; ?></p>

<p>电话：<?php echo isset($info['tel']) ? $info['tel'] : 'XXXX'; ?></p>


<p>鉴于：
    1、易米融理财网站或易米融理财客户端（域名：www.cmibank.com）由丙方运营管理。甲乙双方均系易米融理财的注册会员。
    甲乙双方根据《中华人民共和国合伙企业法》和《中华人民共和国合同法》的规定，经协商一致，达成如下协议全部债权（包括但不限于借款本金、利息、罚息、复利等债权债务合同所约定的债权人享有的所有权利）及相关担保权利（上述债权及担保权利以下合称“信贷资产”），即标的债权。资产明细见《标的债权清单》
</p>
<article>
    <ul>
        <li><h2><strong>第一条 债权信息</strong></h2>
        1、甲方自愿将其所拥有的下列债权全部或部分对乙方进行转让：
        <table>
        	<tbody><tr>
        		<td>合同编号：</td>
        		<td><?php echo isset($info['con_number']) ? $info['con_number'] : '第（xxxx）年（xx债转xx）号'; ?></td>
        		<td>债权本金：</td>
        		<td><?php echo isset($info['con_money']) ? $info['con_money'] : 'XXXX'; ?>元</td>
        	</tr>
        	<tr>
        		<td>债权形成时间：</td>
        		<td><?php echo isset($info['buytime']) ? $info['buytime'] : 'XXXX'; ?></td>
        		<td>年化利率：</td>
        		<td><?php echo isset($info['con_income']) ? $info['con_income'] : 'XXXX'; ?></td>
        	</tr>
        	<tr>
        		<td>债权结束时间：</td>
        		<td></td>
        		<td>剩余期限：</td>
        		<td><?php echo isset($info['diff_day']) ? $info['diff_day'] : 'XXXX'; ?></td>
        	</tr>
        </tbody></table>
        2、上述债权按一次性还本付息，一年按365天计息。
        </li>
        <li><h2>第二条 乙方受让债权的本金与利息</h2>
            1、	乙方受让债权的本金：<br>
产品名称：<?php echo isset($info['pname']) ? $info['pname'] : 'XXXX'; ?>
           <table border="1">
				<tbody><tr>
					<td>申请加入时间（年月日）</td>
					<td>申购金额（单位:元）</td>
				</tr>
				
				<tr>
					<td><?php echo isset($info['buytime']) ? $info['buytime'] : 'YYYY-MM-DD'; ?></td>
					<td><?php echo isset($info['buymoney']) ? $info['buymoney'] : 'xxxx.xx元'; ?></td>
				</tr>	
				
				<tr>
					<td>申购总额</td>
					<td><?php echo isset($info['buymoney']) ? $info['buymoney'] : 'xxxx.xx'; ?>元</td>
				</tr>
			</tbody></table>
        2、	乙方的利息：
		乙方的利息= 000.00*0.00/000*00=0.00元。
若债权人提前回购，则实际计息天数为理财期限减去提前还款的天数。
        
        </li>
        <li><h2>第三条 债权转让款的支付</h2>
           1、甲乙丙三方均一致同意乙方将债权转让款支付至丙方在第三方支付机构或银行开立的资金监管账户，由丙方代甲方收取债权转让款。
2、乙方按照易米融理财平台的规则将与债权转让款数额相对应的资金支付至丙方在银行或第三方支付机构开立的资金监管账户中，即视为乙方履行了支付债权转让款义务。

        </li>
        <li><h2>第四条 债权的取得</h2>
            乙方自本协议生效之日即取得本协议项下的债权，有权按照本协议约定的条件受领债权的债务人归还的借款本金及利息。
        </li>
        <li><h2>第五条 债权的实现</h2>
            债权回购日为    <?php echo isset($info['repaymenttime']) ? $info['repaymenttime'] : 'YYYY-MM-DD'; ?>  ，根据甲丙之间签订的《债权委托转让协议》（<?php echo isset($info['con_number']) ? $info['con_number'] : '第（xxxx）年（xx债转xx）号'; ?>），甲方将借款本息支付至丙方账户，丙方应当于收到借款本息后的三个工作日内向乙方支付债权本息，乙方的债权因受偿而得到实现。
若甲方无力归还借款本息，则根据《债权委托转让协议》（编号:<?php echo isset($info['con_number']) ? $info['con_number'] : '第（xxxx）年（xx债转xx）号'; ?>）约定，浙江保和汇金融服务外包有限公司自愿在2个工作日内以自有全部资产对未清偿的标的债权本息提供连带保证责任。
丙方应在收到借款本息后一个工作日内向乙方支付债权本息，乙方的债权因受偿而得到实现。

        </li>
        <li><h2>第六条 其他权利义务</h2>
            1、标的债权的价值有可能并非甲方对原债务人的全部债权的价值，乙方对此充分理解和认可。而且乙方已经充分了解基础债权的全部情况，并且同意从甲方处受让债权。
            2、甲方保证本协议项下转让给乙方的债权为甲方合法拥有，甲方拥有完全、有效的处分权。
            3、乙方声明与保证其所用于受让标的债权的资金来源合法，乙方是该资金的合法所有人，如果第三方对资金归属、合法性问题发生争议，由乙方自行负责解决。
            4、因战争、动乱、自然灾害等不可抗力或国家法律政策变动、电信网络服务终止、黑客攻击等客观因素出现，导致协议内容延迟履行或不能履行，甲、乙、丙三方互不追究责任。
            5、甲乙丙三方确认，本协议的签订、生效和履行以不违反中国的法律法规为前提。如果本协议中的任何一条或多条违反现行的法律法规，则该条将被视为无效，但该无效条款并不影响本协议其他条款的效力。
        </li>
        <li><h2>第七条 协议的变更</h2>
            本协议的任何修改、补充均须以易米融理财平台电子文本形式作出。
        </li>
        <li><h2>第八条 争议解决</h2>
           如果甲乙丙三方在本协议履行过程中发生任何争议，应友好协商解决；如协商不成，则须提交丙方所在地人民法院进行诉讼。
        </li>
        <li><h2>第九条 协议的保管</h2>
            甲乙双方一致同意委托丙方保管所有与本协议有关的书面文件或电子信息。
        </li>
        <li><h2>协议的解释</h2>
            本协议中所使用的定义，除在上下文中另有定义外，以易米融理财平台公布的《易米融理财用户使用协议》中的定义含义。本协议中，除非另有规定，否则应适用易米融理财平台公布的《易米融理财用户使用协议》规定的释义规则。
        </li>
        <li><h2>第十一条 本协议的成立与生效</h2>
          乙方按照“易米融理财平台”的规则，通过对甲方的债权（“标的债权”）转让需求点击“购买”或“立即抢购”按钮，进入投资理财详细信息界面，填写完成乙方的投资理财信息后并点击同意《债权转让协议》，则本协议成立，乙方在线完成债权转让款支付后本协议即成立并立即生效。
        </li>
        
    </ul>
</article>
<p>甲方：<?php echo isset($info['corname']) ? $info['corname'] : '某债权公司';?> </p>
	
			 <p class="ti" style="position:relative;"><label>印章:</label><img style="position:absolute;left:2rem;top:-4rem;" height="150" width="150" src="<?php echo isset($info['stamp']) ? $info['stamp'] : STATIC_DOMAIN . 'sample/stamp_sample.png'; ?>">
			</p>
			
			
	

<p>日期：2015-05-13</p>

<p>乙方：<?php echo isset($info['uname']) ? $info['uname'] : '某用户';?></p>

<p>日期：2015-05-13</p>

	<div style="position:relative;">
			<p>
				<lable>丙方：<?php echo isset($info['ourcompany']) ? $info['ourcompany'] : '万米财富管理有限公司';?></lable>
				<img style="position:absolute;left:2rem;top:-2rem;" height="150" width="150" src="<?php echo isset($info['stamp']) ? $info['stamp'] : STATIC_DOMAIN . 'sample/stamp_sample.png'; ?>">
			</p>
		<p>法定代表人(或授权代表)(签章)：</p>
		<p>日期： <?php echo isset($info['buytime']) ? $info['buytime'] : 'YYYY-MM-DD';?></p>
    </div>
    </div>
	</div>
</body></html>