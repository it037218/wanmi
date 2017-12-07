<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/contractmanage">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($searchcorname)){?>
     <input type="hidden" name="searchcorname" value="<?php echo $searchcorname; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?> 
     
     <?php if(isset($searchcon_number)){?>
     <input type="hidden" name="searchcon_number" value="<?php echo $searchcon_number; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>   
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <li><span>公司名称</span></li>
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/contractmanage" method="post">
             <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchcorname)?$searchcorname:'请输入搜索内容'?>"  id="searchcorname" name="searchcorname"></li>
             <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
             </form>
             
             <li><span>合同编号</span></li>
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/contractmanage" method="post">
             <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchcon_number)?$searchcon_number:'请输入搜索内容'?>"  id="searchcon_number" name="searchcon_number"></li>
             <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
             </form>
             
             <li><span>销售状态</span></li>
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/contractmanage" method="post">
             <li>
                <select name="searchstatus" id="searchstatus">
                    <option value="0">全部</option>
                    <option value="1">在售</option>
                    <option value="2">停止销售</option>
                    <option value="3">售罄</option>
                </select>
             </li>
             <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
             </form>
            <li class="line">line</li>
            
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="8%">公司名称</th>
                <th width="8%">合同编号</th>
                <th width="8%">合同金额</th>
                <th width="8%">已售总额</th>
                <th width="8%">定期已售金额</th>
                <th width="8%">活期已售金额</th>
                <th width="8%">库存金额</th>
                <th width="8%">销售状态</th>
                <th width="8%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
            <?php
            $sellmoney = isset($rtnsellmoney[$value['cid']]) ? $rtnsellmoney[$value['cid']] : 0;
            $stock_money = isset($count_stock_money[$value['cid']]) ? $count_stock_money[$value['cid']] : 0;
            $kucun = $value['con_money'] - $sellmoney - $stock_money;
            $count_money = $sellmoney + $stock_money;
            
            ?>
                <tr>
                <td><?php echo $value['corname'];?></td>
                <td><?php echo $value['con_number'];?></td>
                <td><?php echo $value['con_money'];?></td>
                <td><?php echo $count_money?></td>
                <td><?php echo $sellmoney?></td>
                <td><?php echo $stock_money;?></td>
                <td><?php echo $kucun;?></td>
                <td>
                    <?php 
                        if($value['status'] == 1){
                           if(!empty($rtncid[$value['cid']])){
                              foreach ($rtncid[$value['cid']] as $val){
                                  echo $val.'<br/>';
                              }
                           }else{
                               echo '无产品';
                           }
                        }else if($value['status'] == 2){
                            echo "停售";
                        }else{
                            echo "未知";
                        }
                    ?>
                </td>
                <td>
                <?php if($value['status'] == 2):?>
                <a href="<?php echo OP_DOMAIN?>/contractmanage/uptoline/<?php echo $value['cid'];?>" target="ajaxTodo" title="确认开启销售">开启</a>
                <?php elseif ($kucun<=0):?>
                <span>----</span>
                <?php else:?>
                <a href="<?php echo OP_DOMAIN?>/contractmanage/downtoline/<?php echo $value['cid'];?>" target="ajaxTodo" title="确定设置为停止销售吗？">停止</a>
                <?php endif;?>   
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
    return abs(($b_new-$a_new)/86400+1);
}
?>
