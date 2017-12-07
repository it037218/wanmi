<form id="pagerForm" method="post" action="<?php echo OP_DOMAIN; ?>/rank_info">
    <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo $numPerPage; ?>" />
    <input type="hidden" name="name_post" value="<?php echo $name_post; ?>" />
    <input type="hidden" name="sort" value="<?php echo $sort; ?>" />
    <input type="hidden" name="start_time" value="<?php echo $start_time; ?>" />
    <input type="hidden" name="end_time" value="<?php echo $end_time; ?>" />
    <input type="hidden" name="phone" value="<?php echo $phone; ?>" />
    <input type="hidden" name="re_money" value="<?php echo $re_money; ?>" />
    <input type="hidden" name="re_number" value="<?php echo $re_number; ?>" />
    <input type="hidden" name="plat" value="<?php echo $back_plat; ?>" />
</form>
<div class="pageHeader">
    <form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>rank_info" method="post" id="form_cc">
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    <td>渠道：
                        <select name="plat" id="plat">
                            <option value="" <?php if($back_plat == ''){ echo 'selected';}?>>-请选择-</option>
                            <?php
                            foreach ($plat as $p){ ?>
                                <option value="<?php echo $p['plat'];?>" <?php if($p['plat'] == $back_plat){ echo 'selected';}?>>
                                    <?php echo $p['plat'];?>
                                </option>
                            <?php }; ?>
                        </select>
                    </td>
                    <td>
                        日期：<input name="start_time"   class="date" dateFmt="yyyy-MM-dd HH:mm" value="<?php echo isset($start_time)?$start_time:''?>" placeholder="如:2017-01-01 00:00:00"/>&nbsp;&nbsp;至
                        <input name="end_time"  class="date" dateFmt="yyyy-MM-dd HH:mm" value="<?php echo isset($end_time)?$end_time:''?>"  placeholder="如:2017-12-01 20:00:00"/>
                    </td>
                    <td>注册手机号
                        <input type="text"  value="<?php echo isset($phone)?$phone:''?>"  id="phone" name="phone" placeholder="请输入注册手机号">
                    </td>
                    <td>复投次数
                        <input type="text"  value="<?php echo isset($re_number)?$re_number:''?>"  id="re_number" name="re_number" placeholder="复投次数如：1或者1-2">
                    </td>
                    <td>复投金额
                        <input type="text"  value="<?php echo isset($re_money)?$re_money:''?>"  id="re_money" name="re_money" placeholder="复投金额如10或者10-50">
                    </td>
                    <td>排序：
                        <select name="sort" id="sort">
                            <option value="asc" <?php if($sort == 'asc'){ echo 'selected';}?>>升序</option>
                            <option value="desc" <?php if($sort == 'desc'){ echo 'selected';}?>>降序</option>
                        </select>
                    </td>
                    <td>
                        <select name="name_post">
                            <option value="all_money" <?php if($name_post == 'all_money'){ echo 'selected';}?>>购买总金额</option>
                            <option value="all_asset" <?php if($name_post == 'all_asset'){ echo 'selected';}?>>总资产</option>
                            <option value="product_money" <?php if($name_post == 'product_money'){ echo 'selected';}?>>定期购买</option>
                            <option value="product_long_money" <?php if($name_post == 'product_long_money'){ echo 'selected';}?>>活期购买</option>
                            <option value="re_money" <?php if($name_post == 're_money'){ echo 'selected';}?>>复投金额</option>
                            <option value="num" <?php if($name_post == 'num'){ echo 'selected';}?>>复投次数</option>
                        </select>
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
            <th width="4%">序号</th>
            <th width="4%">用户ID</th>
            <th width="4%">用户名</th>
<!--            <th width="6%">注册手机号码</th>-->
            <th width="7%">银行预留手机号码</th>
            <th width="5%">身份证号码</th>
            <th width="5%">复投总金额</th>
            <th width="5%">复投次数</th>
            <th width="5%">渠道</th>
            <th width="5%">购买总金额</th>
            <th width="7%">总资产</th>
            <th width="7%">定期购买金额</th>
            <th width="7%">活期购买金额</th>
            <th width="3%">详情</th>
        </tr>
        </thead>
        <tbody>

        <?php if(!empty($list)):?>
            <?php foreach($list AS $key=>$value):?>
                <tr>
                    <td><?php echo $key+1 ;?></td>
                    <td><?php echo isset($value['uid']) ? $value['uid'] : '';?></td>
                    <td><?php echo isset($value['realname']) ? $value['realname'] : '' ;?></td>
                    <td><?php echo isset($value['phone']) ? $value['phone'] : '';?></td>
                    <td><?php echo isset($value['idCard']) ? $value['idCard'] : '';?></td>
                    <td><?php echo isset($value['re_money']) ? $value['re_money'] : '';?></td>
                    <td><?php echo isset($value['num']) ? $value['num'] : '';?></td>
                    <td><?php echo isset($value['plat']) ? $value['plat'] : '';?></td>
                    <td><?php echo isset($value['all_money']) ? $value['all_money'] : '';?></td>
                    <td><?php echo isset($value['all_asset']) ? $value['all_asset'] : '';?></td>
                    <td><?php echo isset($value['product_money']) ? $value['product_money'] : '';?></td>
                    <td><?php echo isset($value['product_long_money']) ? $value['product_long_money'] : '';?></td>
                    <td>
                        <a href="<?php echo OP_DOMAIN;?>rank_info/detail?&uid=<?php echo $value['uid'];?>&start_time=<?php echo isset($start_time)?$start_time:'';?>&end_time=<?php echo isset($end_time)?$end_time:'';?>&phone=<?php echo isset($phone)?$phone:'';?>&plat=<?php echo isset($back_plat)?$back_plat:'';?>" target="navtab" title="查看">查看</a>
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