<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN.$func_name; ?>">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <input type="hidden" name="status" value="<?php echo $status; ?>" />
</form>
<div class="pageContent">
	<div class="panelBar" >
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/klproductbuyinfo" method="post" style="display:<?php if($status==0){echo 'none';}else{echo 'black';}?>">
            <li><span>产品名称</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchpname)?$searchpname:'请输入搜索内容'?>"  id="searchpname" name="searchpname"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/klproductbuyinfo" method="post" style="display:<?php if($status==0){echo 'none';}else{echo 'black';}?>">
            <li><span>起息日期</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入开始日期') this.value='';" onblur="if(this.value=='') this.value='请输入开始日期';" value="<?php echo isset($searchstart)?$searchstart:'请输入开始日期'?>"  id="searchstart" name="searchstart" class="date">&nbsp;&nbsp;至</li>
            <li><input type="text" onfocus="if(this.value=='请输入结束日期') this.value='';" onblur="if(this.value=='') this.value='请输入结束日期';" value="<?php echo isset($searchend)?$searchend:'请输入结束日期'?>"  id="searchend" name="searchend" class="date"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <li style="display:<?php if($status==1){echo 'none';}else{echo 'black';}?>"><a title="添加产品" href="<?php echo OP_DOMAIN; ?>klproduct/addKlproduct" target="navtab"  class="icon"><span>添加产品</span></a></li>
            <li class="line">line</li>
            
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="8%">项目名称</th>
                <th width="8%">产品ID</th>
                <th width="8%">产品名称</th>
                <th width="8%">收益率</th>
                <th width="8%">回款收益</th>
                <th width="10%">募集资金</th>
                <th width="10%">起息时间</th>
                <th width="10%">状态</th>
                <th width="25%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <?php switch ($value['status']){
                case 0: $status = '末上架';break;
                case 1: $status = '已上架';break;
                case 2: $status = '（售罄）已下架';break;
                case 3: $status = '售罄';break;
                case 4: $status = '停售';break;
                default: $status = '末知状态'; break;
            }?>
                <tr>
                <td><?php echo $kltype_list[$value['ptid']];?></td>
                <td><?php echo $value['pid'] ;?></td>
                <td><?php echo $value['pname'] ;?></td>
                <td><?php echo $value['income'] ;?></td>
                <td><?php echo $value['income'] ;?></td>
                <td><?php echo $value['money'] ;?></td>
                <td><?php 
                    if(empty($value['online_time'])){
                        echo $value['odate'];
                    }else{
                        echo date('Y-m-d',strtotime($value['online_time']));
                    }
               ?></td>
                <td><?php echo $status ?></td>
                <td>
                <a href="<?php echo OP_DOMAIN;?>klproduct/detail?&pid=<?php echo $value['pid'];?>" target="navtab" title="查看">查看</a>
                  <?php if(!$value['status']){?>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo OP_DOMAIN;?>klproduct/uptoline/<?php echo $value['pid'];?>/<?php echo $value['ptid'];?>" target="dialog"  title="您真的要上线吗?">上线</a>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo OP_DOMAIN;?>klproduct/editKlproduct/<?php echo $value['pid'];?>/<?php echo $value['ptid'];?>" target="navtab" title="基本编辑">编辑</a>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo OP_DOMAIN;?>klproduct/delKlproduct/<?php echo $value['pid']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
                  <?php }else{ ?>
                  &nbsp;&nbsp;|&nbsp;&nbsp;
                  <a href="<?php echo OP_DOMAIN;?>klproductbuyinfo/getklproductbuyinfoBypid/<?php echo $value['pid'];?>" target="navtab" title="查看快乐宝用户购买记录">查看购买记录</a>
                       <!--   &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo OP_DOMAIN;?>recommend/addtoCompetitive/<?php echo $value['pid']?>" target="ajaxTodo" title="您真的要推荐这个产品吗?">推荐</a>-->
                  <?php } ?>
                </td>
                </tr>
            <?php endforeach;?>
		<?php endif;?>
		</tbody>
	</table>
	<div class="panelBar">
        <div class="pages">
            <span>显示</span>
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
                <option value="30" <?php echo $numPerPage == 30 ? 'selected' : ''; ?>>30</option>
                <option value="50" <?php echo $numPerPage == 50 ? 'selected' : ''; ?>>50</option>
            </select>
            <span>条，共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
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

