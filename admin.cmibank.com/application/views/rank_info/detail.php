<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>rank_info/detail?&uid=<?php echo $uid;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>&phone=<?php echo $phone;?>&plat=<?php echo $plat;?>">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
</form>
<div class="pageContent">

    <table class="list" width="100%" layoutH="95">
        <thead>
        <tr>
            <th width="4%">序号</th>
            <th width="4%">用户UID</th>
            <th width="4%">产品ID</th>
            <th width="4%">项目ID</th>
            <th width="7%">产品名称</th>
            <th width="5%">投资金额</th>
            <th width="5%">购买时间</th>
            <th width="5%">状态</th>
            <th width="5%">银行类型</th>
            <th width="7%">手机号</th>
            <th width="7%">姓名</th>
            <th width="7%">身份证号</th>
            <th width="3%">银行卡号</th>
            <th width="3%">渠道</th>
        </tr>
        </thead>
        <tbody>

        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo $key+1 ;?></td>
                    <td><?php echo isset($value['uid']) ? $value['uid'] : '';?></td>
                    <td><?php echo isset($value['pid']) ? $value['pid'] : '';?></td>
                    <td><?php echo isset($value['ptid']) ? $value['ptid'] : '' ;?></td>
                    <td><?php echo isset($value['pname']) ? $value['pname'] : '';?></td>
                    <td><?php echo isset($value['money']) ? $value['money'] : '';?></td>
                    <td><?php echo isset($value['buytime']) ? date('Y-m-d H:i:s',$value['buytime']) : '';?></td>
                    <td><?php echo isset($value['status']) ? $value['status'] == '0' ? '未结算' : '已结算' : '';?></td>
                    <td><?php echo isset($value['bankcode']) ? $value['bankcode'] : '';?></td>
                    <td><?php echo isset($value['phone']) ? $value['phone'] : '';?></td>
                    <td><?php echo isset($value['realname']) ? $value['realname'] : '';?></td>
                    <td><?php echo isset($value['idCard']) ? $value['idCard'] : '';?></td>
                    <td><?php echo isset($value['cardno']) ? $value['cardno'] : '';?></td>
                    <td><?php echo isset($value['plat']) ? $value['plat'] : '';?></td>
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