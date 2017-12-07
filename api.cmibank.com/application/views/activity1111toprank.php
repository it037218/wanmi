<?php
if (@$_SERVER['ENVIRONMENT'] == 'production') {
    $domain = 'http://api.cmibank.com'; 
    $static_domain = 'http://static1.cmibank.com';
} elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
    $domain = 'http://api.cmibank.vip'; 
    $static_domain = 'http://static.cmibank.vip'; 
} else {
    $domain = 'http://api.cmibank.dev'; 
    $static_domain = 'http://static.cmibank.dev'; 
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="target-densitydpi=device-dpi, width=640px, user-scalable=no"  >
	<meta name="format-detection" telephone="no">
	<title>activity1111小伙伴喊你来赚钱啦！</title>
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/css/bind_invite_c9adbf9.css?232">
	<link rel="stylesheet" href="<?php echo $static_domain; ?>/css/dialog.css">
	<script type="text/javascript" src="<?php echo $static_domain; ?>/common/js/jquery-1.7.1.min.js"></script>
</head>
<script type="text/javascript">

</script>
<body>
    <div class="head" style="height:40px;"></div>
<style>
html{min-width: 370px}
body{
    background: #ffd5b3;
}
.rank{
    border: 1px solid #ffdfc8;
    width:83%;
    margin: 20px auto;
    padding-top: 15px;
    background-color: #ffdcc5;
    border-radius: 20px;
    clear: both;
}

.itemicon{
    margin: -44px auto 0;
    display: block;
}

.ranktips{
    background-color: #ffceb4;
    color: #a8432d;
    padding: 17px;
    font-size: 1em;
    margin-top: 8px;
    border: 1px solid #ffebdc;
    display: block;
}

.rank table{
    text-align:center;
}

.rank table tr td{
    padding:12px 0px;
    font-size: 0.8em;
    
}
.rank table tr.single{
    background-color: #ffceb4;
}
.rank table tr.single,.rank table tr.double{
    border-top: 3px solid #ffebdc;
    border-bottom: 3px solid #ffebdc;
}
.rank table .head td{
    padding: 20px 0px;
    
}

</style>
<div style="background: #ffd5b3;">
    <div class="rank">
        <img class="itemicon" width="70%" src="<?php echo $static_domain;?>/images/20171106/paihang.png"/>
        <strong class="ranktips">中奖结果将在活动结束后公布，以下仅供参考！</strong>  
        <table width="100%">
            <tr class="head">
                <td>
                    当前排名
                </td>
                 <td>
                    邀请人账户
                </td>
                 <td>
                    邀请好友数量
                </td>
                <td>
                    好友首投总额
                </td>
            </tr>
            <?php foreach ($toprank as $key => $value) {
                $ranknum = $key+1;
                ?>
            <tr class="<?php if($ranknum%2==0){echo 'double';}else{echo 'single';}?>">
                <td>
                    <?php echo $ranknum;?>
                </td>
                 <td>
                    <?php echo $value['_invite_account'];?>
                </td>
                 <td>
                    <?php echo $value['count'];?>
                </td>
                <td>
                    <?php echo $value['subbuyamout']; ?>
                </td>
            </tr>
            <?php  };?>

        </table>
    </div>
<div class="shuoming">
<div   style="margin-top:50px; font-size:14px"  >
    <hr>
    <p> <center >Copyright<span style="font-size:1.5em; bottom:0">&copy;</span>万米财富有限公司ALL Rights Reserved</center> </p>
    <p><center>ICP备案号：沪ICP备15008416号</center></p>
</div></div>
</div>


</body>
</html>