<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/klproductbuyinfo/getklproductbuyinfoBypid/<?php echo $pid;?>">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($searchpname)){?>
     <input type="hidden" name="searchpname" value="<?php echo $searchpname; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($searchtitle)){?>
     <input type="hidden" name="searchtitle" value="<?php echo $searchtitle; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
     <?php if(isset($searchtrxId)){?>
     <input type="hidden" name="searchtrxId" value="<?php echo $searchtrxId; ?>" />
     <input type="hidden" value="search" name="op">
     <?php }?>
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/klproductbuyinfo/getklproductbuyinfoBypid" method="post">
            <li><span>产品名称</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入产品名称') this.value='';" onblur="if(this.value=='') this.value='请输入产品名称';" value="<?php echo isset($searchpname)?$searchpname:'请输入产品名称'?>"  id="searchpname" name="searchpname"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/klproductbuyinfo/getklproductbuyinfoBypid" method="post">
            <li>
            <span>
              <select class="combox" name="searchtype">
				<option value="1">用户姓名</option>
				<option value="2">手机号码</option>
			</select>
			</span>
            </li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($searchtitle)?$searchtitle:'请输入搜索内容'?>"  id="searchtitle" name="searchtitle"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/klproductbuyinfo/getklproductbuyinfoBypid" method="post">
            <li><span>订单号</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入订单号') this.value='';" onblur="if(this.value=='') this.value='请输入订单号';" value="<?php echo isset($searchtrxId)?$searchtrxId:'请输入订单号'?>"  id="searchtrxId" name="searchtrxId"></li>
            <li><input type="hidden" value="search" name="op"><button type="submit" >检索</button></li>
            </form>
            <li class="line">line</li>
            
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="6%">产品名称</th>
                <th width="6%">订单号</th>
                <th width="6%">用户姓名</th>
                <th width="6%">手机号</th>
                <th width="6%">身份证号</th>
                <th width="6%">购买金额</th>
                <th width="6%">购买时间</th>

            </tr>
        </thead>
        <tbody>
        <!-- --- $banklist['01050000']['name']);-->
         <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
        <?php ?>
            <?php foreach($list AS $key=>$value):?>
            <tr>
               <td><?php echo $value['pname']?></td>
               <td><?php echo $value['trxId'];?></td>
               <td><?php echo $value['realname']?></td>
               <td><?php echo $value['account'];?></td>
               <td><?php echo $value['idCard']?></td>
               <td><?php echo $value['money']?></td>
               <td><?php echo date('Y-m-d H:i:s',$value['ctime']);?></td>
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


