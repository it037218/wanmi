
<div class="pageContent">

	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="8%">项目名称</th>
                <th width="5%">产品ID</th>
                <th width="5%">产品名称</th>
                <th width="5%">状态</th>
                <th width="5%">收益率</th>
                <th width="5%">回款收益</th>
                <th width="5%">期限</th>
                <th width="7%">起息日期</th>
                <th width="7%">截止日期</th>
                <th width="5%">募集资金</th>
                <th width="7%">已募集金额</th>
                <th width="5%">可售金额</th>
                <th width="5%">下个产品</th>
                <th width="16%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)){?>
            <?php foreach($list AS $key=>$value){?>
            <?php
            $current_pid = $current_pname = $current_income = $current_uistime = $status =
            $current_uietime = $diff_days  = $money = $sellmoney = $canbuymoney = $nextpname = '--';

            if(isset($value['plist'])){
                switch ($value['plist'][0]['status']){
                    case 0: $status = '末上架';break;
                    case 1: $status = '已上架';break;
                    case 2: $status = '已下架';break;
                    case 3: $status = '售罄';break;
                    case 4: $status = '停售';break;
                    default: $status = '末知状态'; break;
                }
                $current_pid = $value['plist'][0]['pid'] ? $value['plist'][0]['pid'] : '--';
                $current_pname = $value['plist'][0]['pname'] ? $value['plist'][0]['pname'] : '--';
                $current_income = $value['plist'][0]['income'] ? $value['plist'][0]['income'] : '--';
                $current_uistime = $value['plist'][0]['uistime'] ? $value['plist'][0]['uistime'] : '--';
                $current_uietime = $value['plist'][0]['uietime'] ? $value['plist'][0]['uietime'] : '--';
                $diff_days = 0;
                if($current_uistime != '--'){
                    $diff_days = diff_days($current_uistime, $current_uietime);
                }
                $diff_days .= '天';
                $money = $value['plist'][0]['money'] ? $value['plist'][0]['money'] : 0;
                $sellmoney = $value['plist'][0]['sellmoney'] ? $value['plist'][0]['sellmoney'] : 0  ;
                $canbuymoney = $money - $sellmoney;
                $nextpname = isset($value['plist'][1]['pname']) ? $value['plist'][1]['pname'] : '--';
               
            }
            
            ?>
                <tr>
                <td><?php echo $value['name'];?></td>      
                <td><?php echo $current_pid;?></td>
                <td><?php echo $current_pname ;?></td>
                <td><?php echo $status ?></td>
                <td><?php echo $current_income ;?></td>                
                <td><?php echo $current_income ;?></td>
                <td><?php echo $diff_days;?></td>
                <td><?php echo $current_uistime ;?></td>
                <td><?php echo $current_uietime ;?></td>
                <td><?php echo $money; ?></td>
                <td><?php echo $sellmoney; ?></td>
                <td><?php echo $canbuymoney; ?></td>
                <td><?php echo $nextpname; ?></td>
                <td>
                    <?php if($value['type'] == 'changping'){?>
                      <?php if($current_pid != '--'){?>
                        <a href="<?php echo OP_DOMAIN;?>product/detail?&pid=<?php echo $current_pid;?>" target="navtab" title="查看">查看</a>
                        	<?php if($editable==1){?>
                            <?php if($nextpname != '--'){?>
                                 &nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="<?php echo OP_DOMAIN;?>onlineproduct/changeindex?ptid=<?php echo $value['ptid']?>&odate=<?php echo $odate; ?>" target="navtab" title="上线产品调序">调序</a>
                            <?php }?> 
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo OP_DOMAIN;?>onlineproduct/Soldout/<?php echo $value['ptid']?>/<?php echo $current_pid;?>/<?php echo $odate?>" target="ajaxTodo"  title="售罄" title="真的售罄了么？">售罄</a>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                       <a href="<?php echo OP_DOMAIN;?>recommend/addtoCompetitive/<?php echo $current_pid;?>" target="ajaxTodo" title="真的要推荐至推荐吗?" title="您真的要推荐至推荐吗?">推荐</a>
                      <?php }?>  
                      <?php }?>
                    <?php }else{?>
                        <?php if($current_pid != '--'){?>
                            <a href="<?php echo OP_DOMAIN;?>product/detail?&pid=<?php echo $current_pid;?>" target="navtab" title="查看">查看</a>
                            	<?php if($editable==1){?>
	                                <?php if($nextpname != '--'){?>
	                                &nbsp;&nbsp;|&nbsp;&nbsp;
	                                <a href="<?php echo OP_DOMAIN;?>onlineproduct/yugao?ptid=<?php echo $value['ptid']?>&odate=<?php echo $odate; ?>" target="navtab" title="预告产品">调序</a>
                                	<?php }?>
		                            &nbsp;&nbsp;|&nbsp;&nbsp;    
		                            <a href="<?php echo OP_DOMAIN;?>product/delproduct/<?php echo $current_pid?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>	
                            	<?php }?>
    					 <?php }?>  
					<?php }?>
                </td>
                </tr>
            <?php };?>
		<?php };?>
		</tbody>
	</table>
</div>
<?php 
function diff_days($start, $end){
    list($a_year, $a_month, $a_day) = explode('-', $start);
    list($b_year, $b_month, $b_day) = explode('-', $end);
    $a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
    $b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
    return abs(($b_new-$a_new)/86400+1);
}
?>

