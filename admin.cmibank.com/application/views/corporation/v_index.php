<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/corporation">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">
	<div class="panelBar">
        <ul class="toolBar">
        <?php if($editable==1){?>
            <li><a title="新增债权公司"    href="<?php echo OP_DOMAIN; ?>corporation/addcorporation" target="dialog"  class="icon"><span>新增债权公司</span></a></li>
            <li class="line">line</li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="55">
        <thead>
            <tr>
                <th width="8%">公司名称</th>
                <th width="6%">印章</th>
                <th width="5%">账户名称</th>
                <th width="5%">账户号码</th>
                <th width="8%">银行名称</th>
                <th width="4%">所在省份</th>
                <th width="5%">城市</th>
                <th width="5%">开户支行</th>
                <th width="8%">行号

                <th width="5%">债权人</th>
                <th width="10%">债权人身份证号/营业执照号</th>
                <th width="8%">债权人印章</th>
                <th width="10%">担保法人</th>
                <th width="5%">担保人</th>

                <th width="25%">操作</th>               
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>                
                <td><?php echo $value['cname'] ;?></td>
                <td><img src='<?php echo $value['stamp'] ;?>' width='50px' height='50px'></td>
                <td><?php echo $value['ccname'];?></td>
                <td><?php echo $value['ccard'] ;?></td> 
                <td><?php echo $value['bankname'];?></td>
                <td><?php echo $value['province'];?></td>
                <td><?php echo $value['city'];?></td>
                <td><?php echo $value['subbank'];?></td>
                <td><?php echo $value['banknum'];?></td>

                    <td><?php echo $value['creditor'];?></td>
                    <td><?php echo $value['identity'];?></td>
                    <td>
                        <?php foreach( $value['seal'] as $k => $v){?>
                     <img src="<?php echo $v;?>" width="50" height="50">
                       <?php }?>
                    </td>

                    <td><?php echo $value['guar_corp'];?></td>
                    <td><?php echo $value['guarantee'];?></td>

                <td>
                <?php if($editable==1){?>
                    <a href="<?php echo OP_DOMAIN;?>corporation/editCorporation/<?php echo $value['corid'];?>" target="dialog" title="编辑">编辑</a>
					|&nbsp;&nbsp;
					<a href="<?php echo OP_DOMAIN;?>corporation/delCorporation/<?php echo $value['corid'];?>" target="ajaxTodo" title="删除">删除</a>
               	<?php }?>
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


