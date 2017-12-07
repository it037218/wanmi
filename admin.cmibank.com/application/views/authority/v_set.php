<style type="text/css">
    h2.contentTitle {text-align: left;}    
</style>
<h2 class="contentTitle">权限编辑 &nbsp;&nbsp;| <?php echo $groupInfo['inner_group'].'-'.$groupInfo['name'];?></h2>
<div class="pageContent">
    <dl>
    	<form method="post" action="<?php echo OP_DOMAIN; ?>authority/set/<?php echo $group_id;?>" class="pageForm required-validate" onsubmit="return validateCallback(this,navTabAjaxDone)">
    	<table class='list' width="100%" layoutH="115">
    	<thead>
            <tr>
                <th width="25%">系统</th>
                <th width="25%">功能</th>                
                <th width="50%">权限</th>              
            </tr>
        </thead>
        <?php $old_group_name = '' ;?>
    	<?php foreach($list AS $key=>$value):?>        		
			<?php foreach($value['list'] AS $k=>$v):?>
			    <tr>
			    <td>
			    <?php if($value['group_name'] == $old_group_name){ ?>
			    -------
			    <?php }else{ ?>
			    <h3 class="dashed"><?php echo $value['group_name'];?></h3>
			    <?php $old_group_name = $value['group_name'] ;?>
			    </td>
			    <?php }?>
				<td>
				<?php echo $v['name'];?>
				</td>
				<td>
    				<span><input type="checkbox" class="chk" name="visible_<?php echo $v['id'];?>" id="visible_<?php echo $v['id'];?>" value="1" <?php if(in_array($v['id'],$visible)):?>checked="checked"<?php endif;?>><em>可见</em></span>
    				<span><input type="checkbox" class="chk" name="editable_<?php echo $v['id'];?>" id="editable_<?php echo $v['id'];?>" value="1" <?php if(in_array($v['id'],$editable)):?>checked="checked"<?php endif;?>><em>可编辑</em></span>
			    </td>
			    </tr>
			<?php endforeach;?>
			
    	<?php endforeach;?>
    	</table>
    	<input type="hidden" name="op" value="set" />
    	
    	<!-- <p class="submit2" style=""><button type="submit" class="blue-btn" onmouseover="this.className = 'blue-btn-hover'" onmouseout="this.className = 'blue-btn'">提 交</button></p> -->
    	<div class="formBar">
            <ul>                       
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    	</form>
	</dl>
</div>
