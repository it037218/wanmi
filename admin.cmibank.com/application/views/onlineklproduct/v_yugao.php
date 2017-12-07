
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
                <th width="5%">募集资金</th>
                <th width="5%">上线时间</th>
                <th width="5%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($list)){?>
            <?php foreach($list AS $key=>$value){?>
                <tr>
                <td><?php echo $key+1;?></td>      
                <td><?php echo $value['pid'];?></td>
                <td><?php echo $value['detail']['pname']; ?></td>
                <td><?php echo $value['detail']['income']; ?></td>
                <td><?php echo $value['detail']['income']; ?></td>                
                <td><?php echo $value['detail']['money']; ?></td>
                <td><?php echo $value['detail']['online_time']; ?></td>
                <td>
                    <a href="<?php echo OP_DOMAIN;?>klproduct/detail?&pid=<?php echo $value['pid'];?>" target="navtab" title="查看">查看</a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN;?>klproduct/delKlproduct/<?php echo $value['pid']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN;?>klproduct/editKlproduct/<?php echo $value['pid'];?>" target="navtab" title="基本编辑">基本编辑</a>
                </td>
                </tr>
		<?php };?>
		<?php }?>
		</tbody>
	</table>

</div>
<?php 
function diff_days($start, $end){
    list($a_year, $a_month, $a_day) = explode('-', $start);
    list($b_year, $b_month, $b_day) = explode('-', $end);
    $a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
    $b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
    return abs(($a_new-$b_new)/86400);
}
?>
