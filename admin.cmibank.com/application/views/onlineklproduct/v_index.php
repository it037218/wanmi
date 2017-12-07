
<div class="pageContent">

	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="8%">项目名称</th>
                <th width="8%">产品ID</th>
                <th width="8%">产品名称</th>
                <th width="8%">状态</th>
                <th width="8%">收益率</th>
                <th width="8%">回款收益</th>
                <th width="8%">募集资金</th>
                <th width="8%">已募集金额</th>
                <th width="8%">可售金额</th>
                <td width="8%">上线时间</td>
                <th width="8%">下个产品</th>
                <th width="12%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)){?>
            <?php foreach($list AS $key=>$value){?>
            <?php
            $current_pid = $current_uptime = $current_online_time =  $current_pname = $current_income = $status =
            $current_uietime = $diff_days  = $money = $sellmoney = $canbuymoney = $nextpname = '--';
            if(isset($value['lplist'])){
                switch ($value['lplist'][0]['status']){
                    case 0: $status = '末上架';break;
                    case 1: $status = '已上架';break;
                    case 2: $status = '已下架';break;
                    case 3: $status = '售罄';break;
                    case 4: $status = '停售';break;
                    default: $status = '末知状态'; break;
                }
                
                $current_pid = $value['lplist'][0]['pid'] ? $value['lplist'][0]['pid'] : '--';
                $current_pname = $value['lplist'][0]['pname'] ? $value['lplist'][0]['pname'] : '--';
                $current_income = $value['lplist'][0]['income'] ? $value['lplist'][0]['income'] : '--';
                $money = $value['lplist'][0]['money'] ? $value['lplist'][0]['money'] : 0;
                $sellmoney = $value['lplist'][0]['sellmoney'] ? $value['lplist'][0]['sellmoney'] : 0  ;
                $canbuymoney = $money - $sellmoney;
                $nextpname = isset($value['lplist'][1]['pname']) ? $value['lplist'][1]['pname'] : '--';
                $current_uptime = $value['lplist'][0]['uptime'] ? date('Y-m-d H:i:s',$value['lplist'][0]['uptime']) : '--';  
                $current_online_time = $value['lplist'][0]['online_time'] ? date($value['lplist'][0]['online_time']) : ''; 
            }
            ?>
                <tr>
                <td><?php echo $value['name'];?></td>      
                <td><?php echo $current_pid;?></td>
                <td><?php echo $current_pname ;?></td>
                <td><?php echo $status ?></td>
                <td><?php echo $current_income ;?></td>                
                <td><?php echo $current_income ;?></td>
                <td><?php echo $money; ?></td>
                <td><?php echo $sellmoney; ?></td>
                <td><?php echo $canbuymoney; ?></td>
                <td><?php echo !empty($current_online_time) ? $current_online_time: $current_uptime;?></td>
                <td><?php echo $nextpname; ?></td>
                <td>
                    <?php if($value['type'] == 'changping'){?>
                        <?php if($current_pid != '--'){?>
                            <a href="<?php echo OP_DOMAIN;?>klproduct/detail?&pid=<?php echo $current_pid;?>" target="navtab" title="查看">查看</a>
                                <?php if($nextpname != '--'){?>
                                    &nbsp;&nbsp;|&nbsp;&nbsp;
                                    <a href="<?php echo OP_DOMAIN;?>onlineklproduct/changeindex?ptid=<?php echo $value['ptid']?>&odate=<?php echo $odate; ?>" target="navtab" title="上线产品调序">调序</a>
                                <?php }?> 
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                            <a href="<?php echo OP_DOMAIN;?>onlineklproduct/Soldout/<?php echo $value['ptid']?>/<?php echo $current_pid;?>/<?php echo $value['lplist'][0]['odate']; ?>" target="ajaxTodo" title="你真的要设置为售罄么？">售罄</a>
                        <?php }?> 
                    <?php }else{?>
                        <?php if($current_pid != '--'){?>
                            <a href="<?php echo OP_DOMAIN;?>klproduct/detail?&pid=<?php echo $current_pid;?>" target="navtab" title="查看">查看</a>
                                <?php if($nextpname != '--'){?>
                                    &nbsp;&nbsp;|&nbsp;&nbsp;
                                    <a href="<?php echo OP_DOMAIN;?>onlineklproduct/yugao?ptid=<?php echo $value['ptid']?>&odate=<?php echo $odate; ?>" target="navtab" title="预告产品">调序</a>
                                 <?php }?> 
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                            <a href="<?php echo OP_DOMAIN;?>klproduct/delKlproduct/<?php echo $current_pid?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
                         <?php }?> 
					<?php }?>
                </td>
                </tr>
            <?php };?>
		<?php };?>
		</tbody>
	</table>

</div>


