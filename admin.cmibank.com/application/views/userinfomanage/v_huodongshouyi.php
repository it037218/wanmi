<div class="pageContent">
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="6%">体验金收益</th>
                <th width="5%">邀请好友收益</th>
                <th width="9%">邀请红包收益</th>
                <th width="9%">微信红包收益</th>
                <th width="6%">返现收益</th>
                <th width="6%">抵用券收益</th>
                <th width="6%">实物兑换收益</th>
            </tr>
        </thead>
         
        <tbody>
            <tr> 
                <td><?php echo  $tymoney; ?></td>
                <?php if(!empty($invite_money)){?>
                	<td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getUserLogDetailByType/1/<?php echo $uid;?>" target="navtab" title="邀请好友收益" ><?php echo  $invite_money;  ?></a></td>
                <?php }else{?>
                    <td>0</td>
                <?php }?>
                <?php if(!empty($luckyBag)){?>
                    <td><a href="<?php echo OP_DOMAIN?>/luckybag/getLuckybagforUser/<?php echo $uid;?>" target="navtab" title="分享红包" ><?php echo  $luckyBag; ?></a></td>
                <?php }else{?>
                    <td>0</td>
                <?php }?>
                <?php if(!empty($redBag)){?>
                    <td><a href="<?php echo OP_DOMAIN?>/redbag/getListByAccount/<?php echo $account;?>" target="navtab" title="微信红包" ><?php echo  $redBag; ?></a></td>
                <?php }else{?>
                    <td>0</td>
                <?php }?>
                
                <?php if(!empty($fanxin_money)){?>
                    <td><a href="<?php echo OP_DOMAIN?>/userinfomanage/getUserLogDetailByType/2/<?php echo $uid;?>" target="navtab" title="返现收益" ><?php echo  $fanxin_money; ?></a></td>
                <?php }else{?>
                    <td>0</td>
                <?php }?>
                <?php if(!empty($coupon)){?>
                    <td><a href="<?php echo OP_DOMAIN?>/couponstatistics/getuserCouponShouyiDetails/<?php echo $uid;?>" target="navtab" title="体验金" ><?php echo  $coupon; ?></a></td>
                <?php }else{?>
                    <td>0</td>
                <?php }?>
                <?php if(!empty($duihuan)){?>
                    <td><a href="<?php echo OP_DOMAIN?>/duihuan/getuserDuihuanList/<?php echo $uid;?>" target="navtab" title="积分兑换" ><?php echo  $duihuan; ?></a></td>
                <?php }else{?>
                    <td>0</td>
                <?php }?>
               </tr> 
		</tbody>
	</table>
</div>
    