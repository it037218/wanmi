
<div class="pageContent">
    <h2></h2>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="5%">序号</th>
                <th width="6%">产品ID</th>
                <th width="6%">产品名称</th>
                <th width="6%">收益率</th>
                <th width="6%">回款收益</th>
                <th width="7%">募集资金</th>
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
                <td><?php echo $value['detail']['money']; ?></td>
                <td>
                <?php if($count>=2){?>
                    <a href="<?php echo OP_DOMAIN;?>longproduct/detail?&pid=<?php echo $value['pid'];?>" target="navtab" title="查看">查看</a>
				    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN;?>onlinelongproduct/tiaoxu?action=up&pid=<?php echo $value['pid']; ?>&ptid=<?php echo $value['ptid'];?>&odate=<?php echo $odate; ?>" target="ajaxTodo" >上调</a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN;?>onlinelongproduct/tiaoxu?action=down&pid=<?php echo $value['pid']; ?>&ptid=<?php echo $value['ptid'];?>&odate=<?php echo $odate; ?>" target="ajaxTodo" >下调</a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN;?>onlinelongproduct/totop?pid=<?php echo $value['pid']; ?>&ptid=<?php echo $value['ptid'];?>&odate=<?php echo $odate; ?>" target="ajaxTodo" >置顶</a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo OP_DOMAIN;?>onlinelongproduct/downtoline/<?php echo $value['ptid'];?>/<?php echo $value['pid'];?>" target="ajaxTodo"  title="您真的要设置为下架么?">下架</a>
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


