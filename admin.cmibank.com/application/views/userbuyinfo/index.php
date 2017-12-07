<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/userbuyinfo">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <?php if(isset($account)){?>
        <input type="hidden" name="account" value="<?php echo $account; ?>" />
    <?php }?>
    <?php if(isset($type)){?>
        <input type="hidden" name="type" value="<?php echo $type; ?>" />
    <?php }?>
    <?php if(isset($timestart)){?>
        <input type="hidden" value="<?php echo $timestart; ?>" name="timestart">
    <?php }?>
    <?php if(isset($timeend)){?>
        <input type="hidden" value="<?php echo $timeend; ?>" name="timeend">
     <?php }?>
    <?php if(isset($amountmin)){?>
        <input type="hidden" value="<?php echo $amountmin; ?>" name="amountmin">
     <?php }?>
    <?php if(isset($amountmax)){?>
        <input type="hidden" value="<?php echo $amountmax; ?>" name="amountmax">
     <?php }?>
    <input type="hidden" value="search" name="op">
</form>
<div class="pageHeader">
    <form onsubmit="return myValidateCallback(this);"  class="pageForm required-validate" action="<?php echo OP_DOMAIN ?>/userbuyinfo" method="post" id="form_cc"> 
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    <td>
                        账户：
                        <input type="text"  value="<?php echo isset($account) ? $account : '' ?>"  id="account" name="account" class='filed-text'>
                    </td>
                    <td>
                        类型：
                        <select name="type">
                            <option value="">请选择</option>
                            <?php foreach($types as $k => $val):?>
                                <option value="<?php echo $val['ptid']?>" <?php if ($type == $val['ptid']) { echo 'selected';} ?>><?php echo $val['name']?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                    <td>
                        起购时间：
                        <input type="text"  value="<?php echo isset($timestart) ? $timestart : '' ?>"  id="timestart" name="timestart" class='date' dateFmt="yyyy-MM-dd HH:mm" >
                        截止时间：
                        <input type="text"  value="<?php echo isset($timeend) ? $timeend : '' ?>"  id="timeend" name="timeend" class='date' dateFmt="yyyy-MM-dd HH:mm" >
                    </td>
                    <td>
                        最低金额：
                        <input type="text"  value="<?php echo isset($amountmin) ? $amountmin : '' ?>"  id="amountmin" name="amountmin" class='number'>
                        最高金额：
                        <input type="text"  value="<?php echo isset($amountmax) ? $amountmax : '' ?>"  id="amountmax" name="amountmax" class='number'>
                    </td>
                    <td><input type="hidden" value="search" name="op"><button type="submit" >检索</button></td>
                </tr>
            </table>
        </div>
    </form>
</div>
<div class="pageContent">
    <table class="list" width="100%" layoutH="95">
        <thead>
            <tr>
                <th width="5%">用户名称</th>
                <th width="7%">注册手机号码</th>
                <th width="7%">存管账号</th>
                <th width="7%">产品名称</th>
                <th width="9%">购买时间</th>
                <th width="8%">购买金额</th>
                <th width="9%">最后登录时间</th>
                <th width="4%">是否绑定</th>
                <th width="4%">是否新用户</th>
                <th width="4%">用户来源</th>
                <th width="10%">操作</th>
            </tr>
        </thead>
        <tbody>
<?php $oldname = ''; ?>
<?php if (!empty($list)): ?>
    <?php foreach ($list AS $key => $value): ?>
                    <tr>
                        <td><?php echo $value['realname']; ?></td>
                        <td><?php echo $value['account']; ?></td>
                        <td><?php echo $value['phone']; ?></td>
                        <td><?php echo $value['productname']; ?></td>
                        <td><?php echo date('Y-m-d H:i:s', $value['buytime']); ?></td>
                        <td><?php echo $value['buyamount']; ?></td>
                        <td><?php echo date('Y-m-d H:i:s', $value['ltime']); ?></td>
                        <td><?php echo $value['ischeck'] == 1 ? '<font color="green" >绑定</font>' : '<font color="red" >末绑定</font>'; ?></td>
                        <td><?php echo empty($value['isnew']) ? '新用户' : ($value['isnew'] == 1 ? '新用户' : '老用户'); ?></td>
                        <td><?php echo $value['plat'] ?></td>
                        <td>
                            <!-- <a href="<?php echo OP_DOMAIN ?>/useridentity/editUseridentity/<?php echo $value['uid'] ?>" target="dialog">修改用户信息</a>
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                             <a href="<?php echo OP_DOMAIN ?>/useridentity/Tiebankcard/<?php echo $value['pnr_usrid'] ?>/<?php echo $value['cardno'] ?>/<?php echo $value['uid'] ?>" target="ajaxTodo" title="您真的要删除吗?">解绑银行卡</a>
                            &nbsp;&nbsp;|&nbsp;&nbsp;-->
                            <a href="<?php echo OP_DOMAIN ?>/userinfomanage/onePersonaldetail/<?php echo $value['uid'] ?>" target="navtab">查看账户</a>
                            &nbsp;&nbsp;|&nbsp;&nbsp;
        <?php if (empty($value['fengkong'])) { ?>
                                <a href="<?php echo OP_DOMAIN ?>/useridentity/addFengKong/<?php echo $value['uid'] ?>/<?php echo $value['account'] ?>" target="ajaxTodo" title="您真的要列为风控吗?">列为风控</a>
        <?php } else { ?>
                                <a href="<?php echo OP_DOMAIN ?>/useridentity/removeFengKong/<?php echo $value['uid'] ?>" target="ajaxTodo" title="您真的要重新移除风控吗?" style="color:red">移除风控</a>
        <?php } ?>
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                            <a href="<?php echo OP_DOMAIN ?>/useridentity/freeze/<?php echo $value['uid'] ?>" target="dialog"  style="color:red">冻结资金</a>       &nbsp;&nbsp;|&nbsp;&nbsp;
                            <a href="<?php echo OP_DOMAIN ?>/useridentity/unfreeze/<?php echo $value['uid'] ?>" target="dialog"  style="color:red">解冻资金</a>
                        </td>
                    </tr>
    <?php endforeach; ?>
<?php endif; ?>
        </tbody>
    </table>
    <div class="panelBar">
        <div class="pages">
            <span>显示</span>
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage: this.value})">
                <option value="20" <?php echo $numPerPage == 20 ? 'selected' : ''; ?>>20</option>
                <option value="40" <?php echo $numPerPage == 40 ? 'selected' : ''; ?>>40</option>
            </select>
            <span>共<?php echo $count; ?>条</span> <span>实际共<?php echo $total; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $count; ?>" numPerPage="<?php echo $numPerPage; ?>" pageNumShown="10" currentPage="<?php echo $pageNum; ?>"></div>
    </div>
</div>
<script type="text/javascript">
    function myValidateCallback(form) {
        var $form = $(form);

        if (!$form.valid()) {
            return false;
        }
        return navTabSearch(form);
    }
</script>
