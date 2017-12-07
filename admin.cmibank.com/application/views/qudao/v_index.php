<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>qudao" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					渠道：<select name="type" id="id_type">
<!--						<option value="kh" <?php if($type == 'kh'){ echo 'selected';}?>>酷滑</option>
						<option value="hsp" <?php if($type == 'hsp'){ echo 'selected';}?>>惠锁屏</option>-->
						<option value="ledouwan" <?php if($type == 'ledouwan'){ echo 'selected';}?>>乐豆玩</option>
<!--						<option value="jrtt" <?php if($type == 'jrtt'){ echo 'selected';}?>>今日头条1</option>
						<option value="jrtt2" <?php if($type == 'jrtt2'){ echo 'selected';}?>>今日头条2</option>
						<option value="jrtt3" <?php if($type == 'jrtt3'){ echo 'selected';}?>>今日头条3</option>-->
						<option value="appStore" <?php if($type == 'appStore'){ echo 'selected';}?>>appStore</option>
						<option value="cmibank" <?php if($type == 'cmibank'){ echo 'selected';}?>>cmibank</option>
<!--						<option value="luckybag" <?php if($type == 'luckybag'){ echo 'selected';}?>>红包</option>-->
						<option value="invite" <?php if($type == 'invite'){ echo 'selected';}?>>邀请</option>
						<option value="dayonghu" <?php if($type == 'dayonghu'){ echo 'selected';}?>>渠道（大用户）</option>
						<option value="sinahls" <?php if($type == 'sinahls'){ echo 'selected';}?>>新浪广告</option>
						<option value="163hls" <?php if($type == '163hls'){ echo 'selected';}?>>网易广告</option>
						<option value="anmo" <?php if($type == 'anmo'){ echo 'selected';}?>>anmo</option>
						<option value="beizhen" <?php if($type == 'beizhen'){ echo 'selected';}?>>beizhen</option>
						<option value="lezhuan" <?php if($type == 'lezhuan'){ echo 'selected';}?>>lezhuan</option>
						<option value="shitoucun" <?php if($type == 'shitoucun'){ echo 'selected';}?>>石头村</option>
<!--						<option value="db" <?php if($type == 'db'){ echo 'selected';}?>>兑吧</option>
						<option value="hbsp" <?php if($type == 'hbsp'){ echo 'selected';}?>>红包锁屏</option>
						<option value="dayonghu" <?php if($type == 'hbsp'){ echo 'selected';}?>>红包锁屏</option>
						<option value="hbsp" <?php if($type == 'hbsp'){ echo 'selected';}?>>红包锁屏</option>
						<option value="hbsp" <?php if($type == 'hbsp'){ echo 'selected';}?>>红包锁屏</option>-->
						</select>
				</td>
				<td>
					注册日期：<input name="stime" readonly="true"  class="date"  value="<?php echo isset($stime) ? $stime : "";?>"  />&nbsp;&nbsp;至
            			 <input name="etime" readonly="true" class="date"  value="<?php echo isset($etime) ? $etime : "";?>"  />
				</td>
				<td><input type="hidden" value="search" name="op"><button type="submit">检索</button></td>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent">
    <div class="panelBar">
    	<ul class="toolBar">
            <li style="padding-top:7px">总计：<?php echo $total;?>人</li>
            <li style="padding-top:7px">未绑卡人数：<?php echo $weibangka;?>人</li>
            <li style="padding-left:20px"><span>定期购买人数：</span></li>
            <li style="padding-top:7px"><?php echo $dingqi_counts;?></li>
            <li style="padding-left:20px"><span>定期购买总额：</span></li>
            <li style="padding-top:7px"><?php echo $dingqi_total;?></li>
            <li style="padding-left:20px"><span>定期复购人数：</span></li>
            <li style="padding-top:7px"><?php echo $dingqi_fugou;?></li>
            <li style="padding-left:20px"><span>活期购买人数：</span></li>
            <li style="padding-top:7px"><?php echo $huoqi_counts;?></li>
            <li style="padding-left:20px"><span>活期购买总额：</span></li>
            <li style="padding-top:7px"><?php echo $huoqi_total;?></li>
            <li style="padding-left:20px"><span>活期存留人数：</span></li>
            <li style="padding-top:7px"><?php echo $huoqi;?></li>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="95">
        <thead>
            <tr>
            	<th width="1%">序号</th>
                <th width="5%">姓名</th>
                <th width="5%">手机号码</th>
                <th width="5%">注册时间</th>
                <th width="5%">定期购买总额</th>
                <th width="5%">定期购买笔数</th>
                <th width="5%">活期购买总额</th>
                <th width="6%">活期购买笔数</th>
                <th width="5%">当前活期总额</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                	<td><?php echo $key+1 ;?></td> 
                    <td><?php echo $value['realname'] ;?></td> 
                    <td><a href="<?php echo OP_DOMAIN?>userinfomanage/onePersonaldetail/<?php echo $value['uid']?>" target="navtab"><?php echo $value['phone'] ;?></a></td>
                    <td><?php echo date('Y-m-d H:i:s',$value['ctime']); ?></td>
                    <td><?php echo $value['productmoney'] ;?></td>
                    <td><?php echo $value['count'] ;?></td>
                    <td><?php echo $value['lproductmoney'] ;?></td>
                    <td><?php echo $value['lcount'] ;?></td>          
                    <td><?php echo $value['longmoney'] ;?></td>        
                </tr>
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
</div>


