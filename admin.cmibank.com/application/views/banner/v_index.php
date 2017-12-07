<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/banner">
    <input type="hidden" name="pageNum" value="<?php echo isset($pageNum) ? $pageNum : 0; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo isset($numPerPage) ? $numPerPage : 0; ?>" />
     <?php if(isset($bannertitle)){?>
     <input type="hidden" name="bannertitle" value="<?php echo $bannertitle; ?>" />
     <input type="hidden" value="search_bannertitle" name="op">
     <?php }?>
      <?php if(isset($stime)){?>
     <input type="hidden" name="stime" value="<?php echo date('Y-m-d',$stime); ?>" />
     <?php }?>
      <?php if(isset($etime)){?>
     <input type="hidden" name="etime" value="<?php echo date('Y-m-d',$etime); ?>" />
     <?php }?>
     <?php if(isset($stime) or isset($etime)){?>
        <input type="hidden" value="search_bannertime" name="op">
     <?php }?>
</form>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/banner" method="post">
            <li><span>广告主题</</span></li>
            <li><input type="text" onfocus="if(this.value=='请输入搜索内容') this.value='';" onblur="if(this.value=='') this.value='请输入搜索内容';" value="<?php echo isset($bannertitle)?$bannertitle:'请输入搜索内容'?>"  id="bannertitle" name="bannertitle"></li>
            <li><input type="hidden" value="search_bannertitle" name="op"><button type="submit" >检索</button></li>
            </form>
            <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>/banner" method="post">
            <li><span>日期</</span></li>
            <li><input name="stime" readonly="true"  class="date" onfocus="if(this.value=='请选择开始时间') this.value='';" onblur="if(this.value=='') this.value='请选择开始时间';" value="<?php echo isset($stime)?date('Y-m-d',$stime):'请选择开始时间'?>"/>&nbsp;&nbsp;至</li>
            <li><input name="etime" readonly="true" class="date" onfocus="if(this.value=='请选择结束时间') this.value='';" onblur="if(this.value=='') this.value='请选择结束时间';" value="<?php echo isset($etime)?date('Y-m-d',$etime):'请选择结束时间'?>"/></li>
            <li><input type="hidden" value="search_bannertime" name="op"><button type="submit">检索</button></li>
            </form>
            <?php if($editable==1){?>
            <li class="line">line</li>
            <li><a title="添加banner"   href="<?php echo OP_DOMAIN; ?>banner/addbanner" target="navtab"  class="icon"><span>添加banner</span></a></li>
            <li class="line">line</li>
            <?php }?>
        </ul>
    </div>
	<table class="list" width="100%" layoutH="115">
        <thead>
            <tr>
                <th width="3%">id</th>
                <th width="5%">广告主题</th>
                 <th width="8%">广告图片</th>
                <th width="8%">活动时间</th>
                <th width="8%">链接地址</th>
                <th width="3%">显示位置</th>
                <th width="8%">发布时间</th>
                <th width="10%">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $oldname = ''; ?>
        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo $value['bid'] ;?></td>
                    <td><?php echo $value['title'] ;?></td>
                    <td><img src="<?php echo $value['img'] ;?>" witdh=200 height=100 /></td>
                    <td><?php echo $value['startime'] ;?>--<?php echo $value['endtime']?></td>
                    <!-- <td><?php echo $value['type'] == 0 ? '活动banner' : '产品banner';?></td> -->
                    <td><?php echo $value['uri'] ;?></td>
                    <td>No.<?php echo $value['location']?></td>
                    <td><?php echo date('Y-m-d H:i:s',$value['ctime']);?></td>
                    <td>
                   	 <?php if($editable==1){?>
                            <a href="<?php echo OP_DOMAIN;?>banner/editBanner/<?php echo $value['bid']?>" target="dialog">编辑</a>&nbsp&nbsp|&nbsp&nbsp
                        <?php if(!in_array($value['bid'], $banner_ids)){?>
                            <a href="<?php echo OP_DOMAIN;?>banner/uptoline/<?php echo $value['bid']?>" target="ajaxTodo" title="你真的要发布么？">发布</a>&nbsp&nbsp|&nbsp&nbsp
                        <?php }else{?>
                            <a href="<?php echo OP_DOMAIN;?>banner/downtoline/<?php echo $value['bid']?>" target="ajaxTodo" title="你真的要取消发布么？"><font color='red'>取消发布</font></a>&nbsp&nbsp|&nbsp&nbsp
                        <?php }?>
                        <a href="<?php echo OP_DOMAIN;?>banner/delBanner/<?php echo $value['bid']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
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
                <option value="30" <?php echo $numPerPage == 2 ? 'selected' : ''; ?>>30</option>
                <option value="50" <?php echo $numPerPage == 50 ? 'selected' : ''; ?>>50</option>
            </select>
            <span>条，共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>


