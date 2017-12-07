<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"/>
<title>债权转让协议</title>
<style type="text/css">
	* {	font-size: 12px; line-height: 1.5em;}
	h3 { text-align: center; margin-bottom: 0px;}
	table{ width: 100%; border: 0; border-collapse: collapse;}
	#half td{ width: 50%; border: 0px solid;}
	#nohalf td{ text-align: center; border: 1px solid;}
	input{ border: 0; text-align: center;}

	.img_fixed{ position: relative; display: inline-block; width: 100%; height: 20px;}
	.img_fixed img{ position: absolute; top: -50%; opacity: 0.6; width: 20%; height: 50px;}
	.seal{ left: 20%;}
	.company{ right: 25%;}

</style>
</head>
<body>
	<h3>债权转让协议</h3>
	<div style="margin: 20px 0px 20px 0px;">
		<table id="half">
			<tr><td colspan="2">甲方(出让方):<?php echo empty($contract['creditor'])?'':$contract['creditor']?></td></tr>
			<tr><td colspan="2">身份证号/营业执照号:<?php echo empty($contract['identity'])?'':substr($contract['identity'],0,-4).'****'?></td></tr>
			<tr><td colspan="2">乙方(受让方):<?php echo $identity['realname']?></td></tr>
			<tr><td colspan="2">身份证号:<?php echo $identity['idCard']?></td></tr>
			<tr><td colspan="2">丙方:(第三方服务机构):<font style="color:red">万米财富管理有限公司</font></td></tr>
			<tr><td colspan="2">网站:“易米融”(域名：www.cmibank.com)</td></tr>
			<tr><td colspan="2">地址:上海市黄浦区延安东路700号港泰广场1401-1402</td></tr>
			<tr><td>法人代表：张晋荣 </td><td>电话：400-080-5611</td></tr>
		</table>
		<p>
		鉴于：<br />1、易米融网站或易米融客户端（域名：www.cmibank.com）由丙方运营管理。甲乙双方均系易米融的注册会员。根据《中华人民共和国合同法》、
		《中华人民共和国民法通则》等有关法律，甲乙丙三方本着诚实信用的基本原则，经平等友好协商，自愿就甲方通过丙方平台向乙方转让债权的相关事宜达成如下协议：
		</p>
	</div>

	<h2>第一条 债权信息</h2>
	<div style="margin-bottom: 20px;">
		<table>
			<tr><td>1、甲方自愿将其持有的下列债权全部或部分向乙方转让：</td></tr>
			<tr><td>&nbsp;&nbsp;合同编号:<?php echo $contract['con_number']?></td></tr>
			<tr><td>&nbsp;&nbsp;债权本金:<?php echo $userproduct['money']?>元</td></tr>
			<tr><td>&nbsp;&nbsp;债权形成时间:<?php echo date('Y-m-d',$userproduct['buytime']); ?></td></tr>
			<tr><td>&nbsp;&nbsp;历史年化收益率:<?php echo $userproduct['income']?>%</td></tr>
			<tr><td>&nbsp;&nbsp;债权到期时间:<?php echo $userproduct['uietime']?></td></tr>
			<tr><td>&nbsp;&nbsp;债权转让天数:<?php echo (strtotime($productDetail['uietime'])-strtotime($productDetail['uistime']))/86400+1?>天</td></tr>
			<tr><td>&nbsp;&nbsp;<?php echo date('Y-m-d',strtotime($productDetail['uietime'])+86400); ?>为合同到期还款日。</td></tr>
			<tr><td>&nbsp;&nbsp;</td></tr>
			<tr><td>2、上述债权到期一次性偿还本金和收益</td></tr>
		</table>
	</div>

	<h2>第二条 乙方受让债权的本金与期望总收益</h2>
	<div style="margin-bottom: 20px;">
		<table id="nohalf">
			<tr>
				<td colspan="3" style="border:0; text-align: left;">1、 产品名称:<?php echo $userproduct['pname']?></td>
			</tr>
			<tr>
				<td style="width: 36%">受让债权本金(单位:元)</td>
				<td style="width: 36%">受让债权时间(年月日)</td>
				<td style="width: 28%">历史年化收益率</td>
			</tr>
			<tr>
				<td><?php echo $userproduct['money']?></td>
				<td><?php echo date('Y-m-d',$userproduct['buytime']); ?></td>
				<td><?php echo $userproduct['income']?>%</td>
			</tr>
			<tr><td colspan="3" style="border:0">&nbsp;&nbsp;</td></tr>
			<tr><td colspan="3" style="border: 0; text-align: left;">2.若原债权人提前回购，则实际计算天数为理财天数减去提前还款的天数。</td></tr>
		</table>
	</div>

	<h2>第三条 债权转让之受让本金的支付</h2>
	<div style="margin-bottom: 20px;">
		1、甲乙丙三方均一致同意乙方将受让本金支付至丙方在第三方支付机构或者银行开立的资金监管账户，由丙方代甲方收取债权转让款。
		<br /><br />
		2、乙方按照上述第一条之规定支付了受让本金后，即视为履行了义务。
	</div>

	<h2>第四条 债权的取得</h2>
	<div style="margin-bottom: 20px;">
		自本协议生效后，乙方始取得本协议项下的债权，有权按照本协议约定的条件享有约定收益。
	</div>

	<h2>第五条 债权的实现</h2>
	<div style="margin-bottom: 20px;">
		收益的计算方式:
		<br />
		&nbsp;  期望总收益=受让债权金额*历史年化收益率*受让天数/365
		<br />
		&nbsp; 举例：张三购买（受让）金额为10000元，历史年化收益率为10%，受让天数为30天，那么他到期收益为：10000元*10%*30天/365=82.19元
		<br /><br />
		1、提前还款
		<br />
		&nbsp; 若债务人提前还款，则实际天数为理财天数减去提前还款的天数。
		<br /><br />
		2、债权回购日为 <?php echo $userproduct['uietime']?>，根据甲丙直接签订的《债权委托转让协议》(<?php echo $contract['con_number']?>)，
		甲方将债权本金和期望总收益支付至丙方在第三方支付平台开具的托管账户，丙方应当于收到上述款项后的三个工作日内向乙方支付债权本金和收益，
		乙方的债权因受偿而得到实现。若甲方无力履行回购义务，则根据《债权委托转让协议》(编号：<?php echo $contract['con_number']?>)约定，丙方有
		权以甲方的还款保证金先行垫付给乙方，并要求相关担保方<?php $patterns = array();
$patterns[0] = '/中啸/';
$patterns[1] = '/车宏/';
$patterns[2] = '/霖欣/';
$patterns[3] = '/多币宝/';
$patterns[4] = '/倾信/';
$patterns[5] = '/尔业/';
$replacements = array();
$replacements[0] = '**';
$replacements[1] = '**';
$replacements[2] = '**';
$replacements[3] = '***';
$replacements[4] = '**';
$replacements[5] = '**'; echo preg_replace($patterns, $replacements, $corp['guar_corp']);?>及担保人<?php echo mb_substr($corp['guarantee'],0,1,'utf-8').'**'?>对甲方的回购
		义务承担连带担保责任。丙方在收到债权本金和期望总收益后即向乙方支付债权本金和收益，乙方的债权因受偿而得到实现。
	</div>


	<h2>第六条 其他权利义务</h2>
	<div style="margin-bottom: 20px; text-index: 2em">
		1.甲方保证本协议项下转让给乙方的债权为甲方合法拥有、完全有效的处分权。
		<br /><br />
		2.乙方声明与保证其所用于受让标的债权的资金来源合法，乙方是该资金的合法所有人，如果第三方对资金归属、合法性问题发生争议，由乙方自行负责解决。
		<br /><br />
		3.因战争、动乱、自然灾害等不可抗拒力或国家法律政策变动、电信网络服务终止、黑客攻击等客观因素出现，导致协议内容延迟履行或者不能履行，甲、乙、丙三方互不追究责任。
		<br /><br />
		4.甲乙丙三方确认，本协议的签订、生效和履行以不违反中国的法律法规为前提。如果本协议中的任何一条或者多条违反现行的法律法规，则该条将被视为无效，但该无效条款并不影响本协议其他条款的效力。
	</div>


	<h2>第七条 协议的变更</h2>
	<div style="margin-bottom: 20px;">
		本协议的任何修改、补充均须以易米融平台电子文本形式作出。
	</div>


	<h2>第八条 争议解决</h2>
	<div style="margin-bottom: 20px;">
		如果甲乙丙三方在本协议履行过程中发生任何争议，应友好协商解决；如协商不成，则须提交丙方所在地人民法院进行诉讼。
	</div>



	<h2>第九条 协议的保管</h2>
	<div style="margin-bottom: 20px;">
		甲乙双方一致同意委托丙方保管所有与本协议有关的书面文件或者电子信息。
	</div>



	<h2>第十条 协议的解释</h2>
	<div style="margin-bottom: 20px;">
		本协议中所使用的定义，除在上下文中另有定义外，以易米融平台公布的《易米融用户使用协议》中的定义含义。本协议中，除非另有规定，否则应适用易米融平台公布的《易米融用户使用协议》规定的释义规则。
	</div>



	<h2>第十一条 本协议的成立与生效</h2>
	<div style="margin-bottom: 20px;">
		乙方按照“易米融平台”的规则，通过对甲方的债权（“标的债权”）转让需求点击“购买”“立即购买”按钮，进入投资理财详细信息界面，
		填写完成乙方的投资理财信息后并点击同意《债权转让协议》，则本协议成立，乙方在线完成受让本金支付后本协议即成立并立即生效。
	</div>


	<div style="margin-bottom: 20px;">
		甲方：<?php echo empty($contract['creditor'])?'':$contract['creditor']?>
		<br />
		<span class="img_fixed">印章：<img class="seal"  src="<?php echo empty($contract['seal'])?'':$contract['seal']; ?>"></span>
		<br />
		日期：<?php echo date('Y-m-d',$userproduct['buytime']); ?>
		<br />
		乙方：<?php echo $identity['realname']?>
		<br />
		日期：<?php echo date('Y-m-d',$userproduct['buytime']); ?>
		<br />
		丙方：<font  style="color:red">万米财富管理有限公司</font>
		<br />
		<span class="img_fixed">法人代表（或者全权代表）(签章）：
			<img class="company" src="http://upload1.cmibank.com/upload/16/79/6/20171015/150804722428686_0.png"></span>
		<br />
		日期：<?php echo date('Y-m-d',$userproduct['buytime']); ?>
	</div>
	</body>
</html>