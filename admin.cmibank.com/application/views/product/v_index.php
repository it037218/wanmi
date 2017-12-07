<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN.$func_name; ?>">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <input type="hidden" name="status" value="<?php echo $status; ?>" />
    
    <?php if(isset($searchpname)){?>
     <input type="hidden" name="searchpname" value="<?php echo $searchpname; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
</form>
<div class="pageContent">
	<!--  <div class="panelBar" style="display:<?php if($status==1){echo 'none';}else{echo 'black';}?>">-->
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN.$func_name; ?>" method="post" style="display:<?php if($status==0){echo 'none';}else{echo 'black';}?>">
            <li><span>产品名称</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchpname)?$searchpname:'请输入搜索内容'?>"  id="searchpname" name="searchpname"></li>
            <li><span>起息日期</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入开始日期') this.value='';" onblur="if(this.value=='') this.value='请输入开始日期';" value="<?php echo isset($searchstart)?"请输入开始日期":'请输入开始日期'?>"  id="searchstart" name="searchstart" class="date">&nbsp;&nbsp;至</li>
            <li><input type="text" onfocus="if(this.value=='请输入结束日期') this.value='';" onblur="if(this.value=='') this.value='请输入结束日期';" value="<?php echo isset($searchend)?'请输入结束日期':'请输入结束日期'?>"  id="searchend" name="searchend" class="date"></li>
            <li><span>截止日期</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入开始日期') this.value='';" onblur="if(this.value=='') this.value='请输入开始日期';" value="<?php echo isset($searchuietime)?'请输入开始日期':'请输入开始日期'?>"  id="searchuietime" name="searchuietime" class="date">&nbsp;&nbsp;至</li>
            <li><input type="text" onfocus="if(this.value=='请输入结束日期') this.value='';" onblur="if(this.value=='') this.value='请输入结束日期';" value="<?php echo isset($searchenduietime)?'请输入结束日期':'请输入结束日期'?>"  id="searchenduietime" name="searchenduietime" class="date"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <?php if($editable==1){?>
	            <li style="display:<?php if($status==1){echo 'none';}else{echo 'black';}?>"><a title="添加产品"  href="<?php echo OP_DOMAIN; ?>product/addproduct" target="navtab"  class="icon"><span>添加产品</span></a></li>
	            <li class="line">line</li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="10%">项目名称</th>
                <th width="5%">产品ID</th>
                <th width="10%">产品名称</th>
                <th width="5%">收益率</th>
                <th width="5%">回款收益</th>
                <th width="5%">期限</th>
                <th width="10%">起息日期</th>
                <th width="10%">截止日期</th>
                <th width="5%">募集资金</th>
                <th width="5%">售出金额</th>
                <th width="5%">购买人数</th>
                <th width="10%">状态</th>
                <th width="25%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                <td><?php echo $ptype_list[$value['ptid']];?></td>
                <td><?php echo $value['pid'] ;?></td>
                <td><?php echo $value['pname'] ;?></td>
                <td><?php echo $value['income'] ;?></td>
                <td><?php echo $contract[$value['cid']] ;?></td>
                <td><?php echo diff_days($value['uistime'], $value['uietime']) ;?>天</td>
                <td><?php echo $value['uistime'] ;?></td>
                <td><?php echo $value['uietime'] ;?></td>
                <td><?php echo $value['money'] ;?></td>
                <td><?php echo $value['sellmoney'] ;?></td>
                <td><?php echo !empty($value['count_people'])? $value['count_people'] : 0 ;?></td>
                <td><?php
                    if($status == 0){
                        echo '未上架';
                    }else{
                        if($value['uistime'] == date('Y-m-d',time()+86400)){
                            if($value['status'] == 1){
                                echo "上架";
                            }else if($value['status'] == 2){
                                echo "售罄(已下架)";
                            }else{
                                echo "售罄";
                            } 
                        }else{
                            if($value['money'] == $value['sellmoney']){
                                echo "售罄";
                            }else{
                                echo "售罄(已下架)";
                            }
                        }
                        
                    }    
                    
                ?></td>
                <td>
				
                <a href="<?php echo OP_DOMAIN;?>product/detail? &pid=<?php echo $value['pid'];?>" target="navtab" title="查看">查看</a>
                  <?php if(!$value['status']){?>
	                <?php if($editable==1){?>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo OP_DOMAIN;?>product/editproduct/<?php echo $value['pid'];?>" target="navtab" title="编辑">基本编辑</a>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo OP_DOMAIN;?>product/uptoline/<?php echo $value['pid'];?>/<?php echo $value['ptid'];?>" target="dialog" title="发布上线" title="您真的要上线吗?">上线</a>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="<?php echo OP_DOMAIN;?>product/delproduct/<?php echo $value['pid']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
	                  <?php }?>
                  <?php }else{ ?>
                  &nbsp;&nbsp;|&nbsp;&nbsp;
                  <a href="<?php echo OP_DOMAIN;?>product/getProductBuyInfoByPid/<?php echo $value['pid'];?>" target="navtab" title="查看定期用户购买记录">查看购买记录</a>
<!--                         &nbsp;&nbsp;|&nbsp;&nbsp; -->
                   <!--      <a href="<?php echo OP_DOMAIN;?>recommend/addtoCompetitive/<?php echo $value['pid']?>" target="ajaxTodo" title="您真的要推荐这个产品吗?">推荐</a>--> 
                  <?php } ?>
                </td>
                </tr>
            <?php endforeach;?>
		    <?php endif;?>
		      <?php if(!empty($countSellmoney)){?>
		        <tr>
		          <td>统计</td>
		          <td>已销售金额：<?php echo $countSellmoney;?></td>
		       </tr>  
		      <?php }?>
		      
		      <?php if(!empty($rtnproduct)){?>
		        <tr>
		          <td>统计共销售:<?php echo $rtnproduct['count_sellmoney'];?></td>
		          <?php foreach ($rtnproduct as $key=>$val){ if($key != 'count_sellmoney'){?>
		          <td><?php echo $key;?>销售：<?php echo $val['sellmoney']?></td>
		          <?php }} ?>
		       </tr>  
		      <?php }?>
		       
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
        <?php if(!empty($countSellmoney)){?>
        
        <?php }else if(!empty($rtnproduct)){?>
        
        <?php }else {?>
         <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
        <?php }?>
        
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

