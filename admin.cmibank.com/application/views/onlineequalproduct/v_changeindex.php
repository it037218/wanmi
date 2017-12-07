
<div class="pageContent">
    <h2></h2>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="5%">序号</th>
                <th width="5%">产品ID</th>
                <th width="5%">产品名称</th>
                <th width="5%">收益率</th>
                <th width="5%">回款收益</th>
                <th width="5%">期限</th>
                <th width="5%">起息日期</th>
                <th width="5%">截止日期</th>
                <th width="5%">募集资金</th>
                <th width="25%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $count = 1;

        ?>
        <?php if(!empty($list)){?>
            <?php foreach($list AS $key=>$value){?>
                <tr>
                <td><?php echo $value['rindex'];?></td>      
                <td><?php echo $value['pid'];?></td>
                <td><?php echo $value['detail']['pname']; ?></td>
                <td><?php echo $value['detail']['income']; ?></td>
                <td><?php echo $value['detail']['income']; ?></td>                
                <td><?php echo diff_days($value['detail']['uistime'], $value['detail']['uietime']) ;?></td>
                <td><?php echo $value['detail']['uistime'] ;?></td>
                <td><?php echo $value['detail']['uietime'] ;?></td>
                <td><?php echo $value['detail']['money']; ?></td>
                <td>
                <?php if($count>=2){?>
                    <a href="<?php echo OP_DOMAIN;?>equalproduct/detail? &pid=<?php echo $value['pid'];?>" target="navtab" title="查看">查看</a>
				    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN;?>onlineequalproduct/tiaoxu?action=up&pid=<?php echo $value['pid']; ?>&ptid=<?php echo $value['ptid'];?>&odate=<?php echo $odate; ?>" target="ajaxTodo" >上调</a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN;?>onlineequalproduct/tiaoxu?action=down&pid=<?php echo $value['pid']; ?>&ptid=<?php echo $value['ptid'];?>&odate=<?php echo $odate; ?>" target="ajaxTodo" >下调</a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN;?>onlineequalproduct/totop?pid=<?php echo $value['pid']; ?>&ptid=<?php echo $value['ptid'];?>&odate=<?php echo $odate; ?>" target="ajaxTodo" >置顶</a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN;?>onlineequalproduct/downtoline/<?php echo $value['ptid'];?>/<?php echo $value['pid'];?> " target="ajaxTodo" title="下架" title="您真的要下架吗?">下架</a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN;?>recommend/addtoCompetitive/<?php echo $value['pid'];?>" target="ajaxTodo" title="真的要推荐至推荐吗?" title="您真的要推荐至推荐吗?">推荐</a>
                <?php }?>
                </td>
                </tr>
            <?php
                $count++;
            };
            ?>
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

