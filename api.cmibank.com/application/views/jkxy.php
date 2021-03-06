<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"/>
<title>借款协议</title>
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
	<h3>借款协议</h3>
	<div style="margin: 20px 0px 20px 0px;">
		<table id="half">
			<tr><td colspan="2">甲方(借款人 ):<?php echo empty($contract['creditor'])?'':$contract['creditor']?></td></tr>
			<tr><td colspan="2">身份证号/营业执照号: <?php echo empty($contract['identity'])?'':substr($contract['identity'],0,-4).'****'?></td></tr>
			<tr><td colspan="2">乙方(出借人): <?php echo $identity['realname']?></td></tr>
			<tr><td colspan="2">身份证号: <?php echo $identity['idCard']?></td></tr>
			<tr><td colspan="2">丙方:(平台方):<font style="color:red">万米财富管理有限公司</font></td></tr>
			<tr><td colspan="2">证件编号：91310115324536485M</td></tr>
			<tr><td colspan="2">住所：上海市黄浦区延安东路700号港泰广场1401-1402</td></tr>
			<tr><td>法人代表：张晋荣 </td><td>电话：400-080-5611</td></tr>
			<tr><td colspan="2">丁方(保证人):<?php echo empty($corp['guar_corp'])?'':$corp['guar_corp']?></td></tr>
		</table>
		<p>
		鉴于：<br />&nbsp;&nbsp;1、甲方系根据中国法律成立并存续的企业法人或居住在中国境内的完全民事行为能力人，为丙方平台的注册用户，以借款人身份在丙方平台发布借款信息。甲方承诺其提供给丙方的信息完全真实。
			  <br />&nbsp;&nbsp;2、乙方系居住在中国境内的完全民事行为能力人，为丙方平台的注册用户，以出借人身份在丙方平台投标该笔借款。乙方承诺出借款系其合法所得，具有完全支配能力，并承诺其提供给丙方的信息完全真实。
			  <br />&nbsp;&nbsp;3、丙方拥有“易米融理财”（域名：www.cmibank.com，手机APP “易米融理财”）的经营权和提供互联网金融信息服务的资质，立足于服务有资金需求的用户，为甲乙双方的借贷提供居间服务。
			  <br />&nbsp;&nbsp;4、甲乙双方同意丙方将双方提交丙方的信息（包括但不限于姓名/名称、有效证件/执照/权利证照号等）提供给本协议各方（包括单独出具担保函、承诺函等的第三方）。
			  <br />&nbsp;&nbsp;5、甲方充分认识并知晓本次借款的乙方为丙方平台的注册用户，且系由多个出借人，多笔出借资金组成本次借款。甲方同意丙方向已出资的乙方公布甲方相关信息（包括但不限于姓名/名称、身份证号码/营业执照、组织机构代码、法定代表人、邮箱、联系电话等）。
			  <br />&nbsp;&nbsp;6、丁方作为保证人与甲方对债务承担连带责任。
			  <br />&nbsp;&nbsp;为明确各方的权利、义务，根据《中华人民共和国合同法》、《中华人民共和国担保法》、《中华人民共和国物权法》及其他有关法律、法规、规章的规定，经协商一致，订立本协议，以便共同遵守执行。
		</p>
	</div>

	<h2>一、借款条款</h2>
	<div style="margin-bottom: 20px;font-size:20px">
		<table>
			<tr><td>&nbsp;&nbsp;借款合同编号: <?php echo $contract['con_number']?></td></tr>
			<tr><td>&nbsp;&nbsp;借款本金: <?php echo $userproduct['money']?>元</td></tr>
			<tr><td>&nbsp;&nbsp;约定年化收益率: <?php echo $userproduct['income']?>%</td></tr>
			<tr><td>&nbsp;&nbsp;借款形成时间: <?php echo date('Y-m-d',$userproduct['buytime']); ?></td></tr>
			<tr><td>&nbsp;&nbsp;借款起息时间: <?php echo $productDetail['uistime']; ?></td></tr>
			<tr><td>&nbsp;&nbsp;借款到期时间: <?php echo $userproduct['uietime']?></td></tr>
			<tr><td>&nbsp;&nbsp;借款期限: <?php echo (strtotime($productDetail['uietime'])-strtotime($productDetail['uistime']))/86400+1?>天</td></tr>
			<tr><td>&nbsp;&nbsp;<?php echo date('Y-m-d',strtotime($productDetail['uietime'])+86400); ?>日为合同到期还款日。</td></tr>
		</table>
	</div>
	<div style="margin-bottom: 20px;">
		<br />&nbsp;&nbsp;1.1、转款方式：本协议生效的同时，乙方即不可撤销的授权丙方委托相应的第三方支付机构或监管银行等合作机构，将本协议项下乙方出借金额划转至甲方银行收款账户或甲方指定的银行账户，划转完毕视为借款发放成功。
		<br />&nbsp;&nbsp;1.2、转款时间：自借款标期届满后的第一个工作日，借款划转至甲方银行收款账户，并于同日起计息。
		<br />&nbsp;&nbsp;1.3、期望回报计算公式：
		<br />&nbsp;&nbsp;期望回报=借款本金*期望回报率*借用天数/365
		<br />&nbsp;&nbsp;举例：张三购买（出借）金额为10000元，期望回报率为年化10%，借款期限为30天，那么到期收益为：10000元*10%*30天/365=82.19元
	</div>
	<h2>二、还款条款</h2>
	<div style="margin-bottom: 20px;">
		<br />&nbsp;&nbsp;2.1、到期一次还本付息
		<br />&nbsp;&nbsp;甲方应于本协议约定的借款期限的最后一天16:00前将本金和期望回报足额存入丙方指定的银行还款账户，并授权委托丙方在借款期限届满后的第一个工作日内将上述款项划转至乙方的账户；
		<br />&nbsp;&nbsp;2.2、提前还本付息
		<br />&nbsp;&nbsp;2.2.1、甲方提前还款的，需提前7个工作日通知丙方。乙方委托丙方按协议1.6条款与甲方进行结算。
		<br />&nbsp;&nbsp;2.2.2、甲方提前还款，丙方应当在7个工作日内完成结算，并把本金和期望回报转入乙方银行账户。
		<br />&nbsp;&nbsp;2.2.3、丁方与甲方对提前还款之情形一并承担连带责任。
		<br />&nbsp;&nbsp;2.3、乙方账户收到全部本金和期望回报时即视为甲方还款成功。因银行原因造成资金逾期到账的，丙方无责。
	</div>
	<h2>三、违约责任</h2>
	<div style="margin-bottom: 20px;">
		<br />&nbsp;&nbsp;3.1、甲方出现下列情形之一者，即视为已发生违约事件，乙方有权要求甲方立即偿还全部借款本息，丙方有权要求甲方立即偿还相关费用。
		<br />&nbsp;&nbsp;3.1.1、向乙方提供虚假情况，或隐瞒真实的重要情况，不配合乙方的调查审查和检查，乙方要求甲方在合理的期限内予以改正，甲方逾期仍不改正损害乙方利益的；
		<br />&nbsp;&nbsp;3.1.2、借款用于非法活动，或未按合同约定用途使用借款，不接受或逃避乙方对其资金情况的监督的；
		<br />&nbsp;&nbsp;3.1.3、未按期偿还借款本金或未按期支付期望回报或不按借款借据特别约定的还款方式归还借款本息的；
		<br />&nbsp;&nbsp;3.1.4、在其他金融机构出现违约行为或信用出现问题的；
		<br />&nbsp;&nbsp;3.1.5、对外有资金拆借行为或涉及高利贷的；
		<br />&nbsp;&nbsp;3.1.6、擅自将本协议项下债务转让给第三人的；
		<br />&nbsp;&nbsp;3.1.7、发生实施承包、租赁、股份制改造、联营、合并，兼并、分立、合资、资产转让、经营变动等任何改变，且出借人依其自主判断认为可能危及本协议项下债权实现的行为；
		<br />&nbsp;&nbsp;3.1.8、甲方有怠于管理和追索其到期债权，或以无偿或及其他不适当方式处分现有主要财产等转移财产或其他逃避债务行为的；
		<br />&nbsp;&nbsp;3.1.9、甲方或甲方实际控制人存在其他资信发生重大变化的情况，乙方依其自主判断认为足以危及本协议项下债权实现的；
		<br />&nbsp;&nbsp;3.1.10、其他甲方认为会影响到债权和债权担保的情形。
		<br />&nbsp;&nbsp;3.2、甲方未能按本协议第二条约定时间归还借款本金及期望回报，视为逾期，应当承担违约责任，对乙方支付违约金，对丙方支付逾期管理费，直至本息清偿完毕之日止。
		<br />&nbsp;&nbsp;违约金计算：逾期归还的本金、期望回报之和×1‰ ／天×逾期天数
		<br />&nbsp;&nbsp;逾期管理费计算：逾期归还的本金、期望回报之和×1‰ ／天×逾期天数
		<br />&nbsp;&nbsp;3.3、如甲方逾期还款超过15天或在逾期后出现逃避、拒绝沟通或拒绝承认欠款事实等恶意行为，乙方有权授权丙方将甲方违约失信的相关信息及甲方的其他信息向包括但不限于互联网网站、媒体、用人单位、公安机关、检查机关、法律机关及有关逾期款项催收服务机构披露。
		<br />&nbsp;&nbsp;3.4、任何一方的违约行为给守约方造成其它损失的（该损失包括但不限于：守约方为履行本协议项下义务而实际发生的费用以及因违约行为受到的应得利益损失、守约方由此进行诉讼或仲裁而产生的诉讼费／仲裁费、律师费、公证费、调查费、评估费、保全费、拍卖费和强制执行费用等），由违约方承担赔偿责任。
	</div>
	<h2>四、借款担保</h2>
	<div style="margin-bottom: 20px;">
		<br />&nbsp;&nbsp;4.1、本协议项下甲方的借款本息及其他一切相关费用由丁方作为保证人，向乙方承担保证责任。
		<br />&nbsp;&nbsp;4.2、保证方式：连带责任保证。
		<br />&nbsp;&nbsp;4.3、保证期限：甲方主债务履行期届满之日起两年内。
		<br />&nbsp;&nbsp;4.4、若甲方未按本协议约定履行还款义务，则保证人应在本协议约定的还款日次日起的1个工作日内履行保证责任（含违约金和逾期管理费）。
	</div>
	<h2>五、权利义务的转让</h2>
	<div style="margin-bottom: 20px;">
		<br />&nbsp;&nbsp;未经丙方事先书面（包括但不限于电子邮件等方式）同意，甲方、乙方不得将本协议项下的任何权利和义务转让给任何第三方。
	</div>
	<h2>六、各方权利和义务</h2>
	<div style="margin-bottom: 20px;">
		<br />&nbsp;&nbsp;6.1、甲方的权利和义务
		<br />&nbsp;&nbsp;6.1.1、甲方必须按协议约定向乙方及时、足额归还本金和期望回报；
		<br />&nbsp;&nbsp;6.1.2、甲方承诺所借款项不用于任何违法用途；
		<br />&nbsp;&nbsp;6.1.3、甲方同意丙方有权将甲方自行提供的或丙方自行收集的资料、信息在互联网上为媒介借款产品做有选择地披露或展示，或向有关的合作机构提供必要的资料等；
		<br />&nbsp;&nbsp;6.1.4、甲方同意并确认，本协议甲方如系两人以上的共同借款，任一借款人均须同等履行本协议项下之义务，对全部借款承担连带清偿责任，乙方有权向任一借款人追索本息及其他相关费用。
		<br />&nbsp;&nbsp;6.2、乙方权利和义务
		<br />&nbsp;&nbsp;6.2.1、乙方享有其所出借款项所带来的收益，但应主动缴纳相关税款；
		<br />&nbsp;&nbsp;6.2.2、甲方还款不足以偿还约定的本金、期望回报及违约金、预期罚息等款项的，乙方同意各自按照其出借金额与借款总额之比例接受清偿；
		<br />&nbsp;&nbsp;6.2.3、乙方承诺对依据本协议获得的甲方信息或资料予以保密，除用于本协议目的进行出具与合理催收外，不得向外转让或披露。
		<br />&nbsp;&nbsp;6.3、丙方的权利和义务
		<br />&nbsp;&nbsp;6.3.1、丙方委托第三方支付机构或监管银行负责处理甲乙双方借贷款过程中发生的资金往来事宜；
		<br />&nbsp;&nbsp;6.3.2、丙方在借款标招标满额后或借款标期届满后的第一个工作日，负责将借款款项划转至甲方的银行卡收款账户；
		<br />&nbsp;&nbsp;6.3.3、甲方偿还借款本息后，丙方应在到款之日起的3个工作日内将款项划转至乙方的银行收款账户；
		<br />&nbsp;&nbsp;6.3.4、甲乙双方同意丙方有权代乙方在必要时对甲方进行借款的违约提醒及督促工作，包括但不限于电话通知、发律函，对乙方提起诉讼的。乙方再次确认委托丙方为其进行以上工作，并授权丙方可以将此工作委托其他方进行，甲方对前述乙方委托丙方的事项已明确知晓，愿意积极配合。
		<br />&nbsp;&nbsp;丙方接受甲乙双方的委托行为，所产生的法律后果由委托方承担，如因甲方或乙方或其他方因包括但不限于技术问题造成的延误或错误，丙方不承担责任。
		</div>
	<h2>七、其他</h2>
	<div style="margin-bottom: 20px;">
		<br />&nbsp;&nbsp;7.1、本借款协议中的甲方与所有乙方之间的借款均是互相独立的，一旦甲方逾期未归还借款本息，所有乙方均有权单独向甲方追索或者提起诉讼。如甲方逾期支付服务费，丙方亦可单独向甲方追索或提起诉讼。
		<br />&nbsp;&nbsp;7.2、本协议项下各方同意并承诺，各方提供的信息均应在提供给本协议各方的同时提供给丙方。本协议各方认可丙方提供的协议文本效力，各方授权丙方根据本协议任意一方的要求向其提供各方所有信息。
		<br />&nbsp;&nbsp;7.3、甲乙丙三方同意并确认，因第三方支付机构或监管银行等合作机构对其受托管账户采取相关行为所产生的法律后果由甲乙双方各自承担，与丙方无涉。
		<br />&nbsp;&nbsp;7.4、乙方在此委托并不可撤销地授权丙方作为乙方代表，行使乙方在本协议项下所有的权利义务。委托范围包括但不限于将乙方出借资金划转至甲方银行收款账户或甲方指定银行收款账户；代为通知担保方履行保证责任；代为进行贷后催收管理；在甲方未按本协议还款时代为通知担保方履行保证责任等。乙方全权委托丙方，受托人丙方有转委托权。
		<br />&nbsp;&nbsp;7.5、甲乙双方确认并同意，委托丙方负责计算本协议项下的任何金额数据，在无明显错误的情况下，甲乙双方认可丙方确定或证明的本协议项下任何金额数据。
		<br />&nbsp;&nbsp;7.6、乙方自行通过网络在丙方“易米融理财”平台上在线点击协议确认按钮，即视为电子协议签署成功。乙方通过丙方下载对应电子协议，并委托丙方保管线下协议文本。
		<br />&nbsp;&nbsp;7.7、本协议各方直接的书面通知或文件往来可以采用任何一种方式送达：
		<br />&nbsp;&nbsp;1）邮件送达方式，发送人以协议载明邮箱发出邮件即视为送达；
		<br />&nbsp;&nbsp;2）快递送达方式，以协议载明各方地址为准，若各方地址变更未履行通知义务的，即视为送达；
		<br />&nbsp;&nbsp;3）传真送达方式，以协议载明传真号为准；
		<br />&nbsp;&nbsp;7.8、如本协议中的任何一条或多条违反适用的法律法规，则该条将被视为无效，但该无效条款并不影响本协议其他条款的效力。
		<br />&nbsp;&nbsp;7.9、本协议项下的附件和补充协议构成本协议不可分割的一部分。协议各方均应严格履行已方义务，非经各方协商一致或，依照本协议约定，任何一方不得提前解除本协议。
		</div>
	<div style="margin-bottom: 20px;">
		甲方（借款人 ）： <?php echo empty($contract['creditor'])?'':$contract['creditor']?>
		<br />
		<span class="img_fixed">印章：<img class="seal"  src="<?php echo empty($contract['seal'])?'':$contract['seal']; ?>"></span>
		<br />
		日期： <?php echo date('Y-m-d',$userproduct['buytime']); ?>
		<br />
		乙方（出借人 ）： <?php echo $identity['realname']?>
		<br />
		日期： <?php echo date('Y-m-d',$userproduct['buytime']); ?>
		<br />
		丙方（平台方）：<font  style="color:red">万米财富管理有限公司</font>
		<br />
		<span class="img_fixed">法人代表（或者全权代表）(签章）：
			<img class="company" src="http://upload1.cmibank.com/upload/16/79/6/20171015/150804722428686_0.png"></span>
		<br />
		日期：<?php echo date('Y-m-d',$userproduct['buytime']); ?>
		<br />
		丁方（保证人 ）：<?php echo empty($corp['guar_corp'])?'':$corp['guar_corp']?>
		<br />
		自愿为甲方承担连带责任保证
		<br />
		日期：	<?php echo date('Y-m-d',$userproduct['buytime']); ?>
	</div>
	</body>
</html>