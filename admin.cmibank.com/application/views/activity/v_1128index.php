<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/activity_management">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <input type="hidden" name="act_value" value="<?php echo $op_value; ?>" />
</form>
<div class="pageHeader">
    <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>activity_management" method="post">
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    <td>
                        <select name="act_value" id="act_select">
                            <option value="0" <?php if ($op_value == 0){echo 'selected="selected"';}?>>-请选择-</option>
                            <option value="1" <?php if ($op_value == 1){echo 'selected="selected"';}?>>月庆活动排行</option>
                            <option value="2" <?php if ($op_value == 2){echo 'selected="selected"';}?>>单笔投资奖励</option>
                            <option value="3" <?php if ($op_value == 3){echo 'selected="selected"';}?>>累计投资奖励</option>
                            <option value="4" <?php if ($op_value == 4){echo 'selected="selected"';}?>>夺标之王奖励</option>
                        </select>
                    </td>
                    <td id="datetime_input" style="display: none">
                        日期：<input name="start_time" readonly="true"  class="date" dateFmt="yyyy-MM-dd HH:mm" value=""  />&nbsp;&nbsp;至
                        <input name="end_time" readonly="true" class="date" dateFmt="yyyy-MM-dd HH:mm" value=""  />
                    </td>
                    <td id="insert" style="display:none;"><input type="checkbox" name="search_into" value="1"/>获取数据</td>
                    <td><input type="hidden" value="search" name="op"><button type="submit" >检索</button></td>
                </tr>
            </table>
        </div>
    </form>
</div>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <li><a title="批量审核" target="selectedTodo" rel="check_all" postType="string"  href="<?php echo OP_DOMAIN; ?>activity_management/reviewed" class="icon"><span>批量审核</span></a></li>
            <li><a title="确实要撤销这些记录吗?" target="selectedTodo" rel="check_all" postType="string" href="<?php echo OP_DOMAIN; ?>activity_management/del" class="delete"><span>批量删除</span></a></li>
            <li class="line">line</li>
        </ul>
    </div>
    <table class="list" width="100%" layoutH="95">
        <thead>
        <tr>
            <th width="3%"><input type="checkbox" group="check_all" name="selectall" class="checkboxCtrl"></th>
            <th width="4%">序号</th>
            <th width="4%">活动名称</th>
            <th width="4%">项目名称</th>
            <th width="4%">UID</th>
            <th width="7%">中奖人手机号码</th>
            <th width="5%">排名</th>
            <th width="5%">投资金额</th>
            <th width="7%">奖品</th>
            <th width="7%">中奖时间</th>
            <th width="7%">是否发奖</th>
            <th width="7%">数据类型</th>
            <th width="7%">审核状态</th>
            <th width="7%">备注信息</th>
            <th width="5%">操作</th>
        </tr>
        </thead>
        <tbody>

        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><input name="check_all" value="<?php echo $value['id']?>" type="checkbox"></td>
                    <td><?php echo $key+1 ;?></td>
                    <td><?php echo isset($value['act_name']) ? $value['act_name'] : '';?></td>
                    <td><?php echo isset($value['name']) ? $value['name'] : '';?></td>
                    <td><?php echo isset($value['uid']) ? $value['uid'] : '';?></td>
                    <td><?php echo isset($value['account']) ? $value['account'] : '';?></td>
                    <td><?php echo isset($value['rank']) ? $value['rank'] : '';?></td>
                    <td><?php echo isset($value['money']) ? $value['money'] : '';?></td>
                    <td><?php echo isset($value['prize']) ? $value['prize'] : '';?></td>
                    <td><?php echo isset($value['luck_time']) ? date('Y-m-d H:i:s', $value['luck_time'])  : '';?></td>
                    <td><?php echo isset($value['is_prize']) ? $value['is_prize'] ? '已发送' : '未发送' : '';?></td>
                    <td><?php echo isset($value['is_true']) ? $value['is_true'] ? '是' : '否' : '';?></td>
                    <td><?php echo isset($value['status']) ? $value['status'] ? '已审核' : '未审核' : '';?></td>
                    <td><?php echo isset($value['description']) ? $value['description'] : '';?></td>
                    <td>
                        <a href="<?php echo OP_DOMAIN;?>activity_management/reviewed?&id=<?php echo $value['id']?>&status=<?php echo $value['status'];?>" target="ajaxTodo">审核</a>&nbsp&nbsp|&nbsp&nbsp
                        <a href="<?php echo OP_DOMAIN;?>activity_management/edit?&id=<?php echo $value['id']?>" target="dialog">编辑</a>&nbsp&nbsp|&nbsp&nbsp
                        <a href="<?php echo OP_DOMAIN;?>activity_management/del?&id=<?php echo $value['id']?>" target="ajaxTodo" title="您真的要删除吗?">删除</a>
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
                <option value="20" <?php echo $numPerPage == 20 ? 'selected' : ''; ?>>20</option>
                <option value="40" <?php echo $numPerPage == 40 ? 'selected' : ''; ?>>40</option>
            </select>
            <span>共<?php echo $count; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#act_select").change(function () {
            var checkValue = $("#act_select option:selected").val();
            if (checkValue == '1'){
                $("#datetime_input").show();
                $("#insert").show();
            }else {
                $("#datetime_input").hide();
                $("#insert").hide();
            }
        });
    })
</script>