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
if (!empty($list) && is_array($list)){
    foreach ($list as $value){
        $result[] = count($value);
    }
    $max = max($result);
}else{
    $max = 0;
}
if ($max > 10){
    $num = 10;
}else{
    $num = $max;
}

?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"content="target-densitydpi=device-dpi,width=320,maximum-scale=1.3,user-scalable=no">
<!--    <meta name="viewport" content="target-densitydpi=device-dpi, width=320px, user-scalable=no"  >-->
    <meta name="format-detection" telephone="no">
    <title>易米融月庆</title>
    <link rel="stylesheet" href="<?php echo $static_domain; ?>/css/bind_invite_c9adbf9.css?232">
    <link rel="stylesheet" href="<?php echo $static_domain; ?>/css/dialog.css">
    <style>
        /*html{*/
            /*min-width: 620px;*/
        /*}*/
        body{
            font-size: 0.5rem;
        }
        .table{
            width: 80%;
            border: 1px solid #FF7C50;
            color: #ed6f1a;
            text-align: center;
            border-collapse: collapse;
            margin: auto;
            padding: 0;
            line-height: 50px;
        }
        .back {
            background: url('<?php echo $static_domain;?>/images/20171106/border.png');
            background-size:100% auto;
        }
        .back .describe {
            width: 80%;
            margin:0px auto 15px;
            font-size: 0.8rem;
            color: #E3701F;
        }

        th {
            font-size: 0.8rem;
            font-weight: 600;
            /*font-family: Arial,Helvetica,sans-serif,"方正黑体";*/
            border:1px solid #FF7C50;
            margin: 0;
            padding: 0;
        }
        td{
            line-height: 30px;
            border:1px solid #FF7C50;
            text-align: center;
            font-size: 0.7rem;
            font-weight: 500;
        }
        .image{
            margin-top: 20px;
            text-align: center;
        }
    </style>
    <script type="text/javascript" src="<?php echo $static_domain; ?>/common/js/jquery-1.7.1.min.js"></script>
</head>

<body style="background: #FCF5D8;">
<img style="width: 100%;" src="<?php echo $static_domain;?>/images/20171106/header.png"/>
<img style="width: 100%;" src="<?php echo $static_domain;?>/images/20171106/body_one.png"/>

<div class="back">
    <div class="describe">
        <p>中奖结果将于清零后的下周公布，以下结果仅供参考。
            <?php if(NOW >= strtotime('2017-12-05 00:00:00')){?>
            <a href="<?php echo $domain;?>/activity_rank/active_first?type=2" style="float: right;color: #E3701F;font-size: 0.8rem">上周排名></上周排名></a>
            <?php };?>
        </p>

    </div>
<table class="table">
    <tr>
        <th>排名</th>
        <th>三月盈</th>
        <th>六月盈</th>
        <th>易年盈</th>
    </tr>
    <?php for ($i = 0; $i < $num; $i++){ ?>
    <tr>
<!--        --><?php //if($list['three_product_tpid'][$i]['money'] >= 100000 || $list['six_product_tpid'][$i]['money'] >= 60000 || $list['year_product_tpid'][$i]['money'] >= 30000) {?>
        <td>
            <?php echo $i+1; ?>
        </td>
        <td>
            <?php echo isset($list['three_product_tpid'][$i]['account']) ? substr_replace($list['three_product_tpid'][$i]['account'],'*****',3,5) : '';?><br/>
            <?php echo isset($list['three_product_tpid'][$i]['money']) ? $list['three_product_tpid'][$i]['money'].'元': '';?>
        </td>

        <td>
            <?php echo isset($list['six_product_tpid'][$i]['account']) ? substr_replace($list['six_product_tpid'][$i]['account'],'*****',3,5) : '';?><br/>
            <?php echo isset($list['six_product_tpid'][$i]['money']) ? $list['six_product_tpid'][$i]['money'].'元' : '';?>
            <!--<?php // echo $list['six_product_tpid'][$i]['account'];?>-->
        </td>
        <td>
            <?php echo isset($list['year_product_tpid'][$i]['account']) ? substr_replace($list['year_product_tpid'][$i]['account'], '*****',3,5) : ''?><br/>
            <?php echo isset($list['year_product_tpid'][$i]['money']) ? $list['year_product_tpid'][$i]['money'].'元' : '';?>
        </td>
<!--        --><?php //}?>
    </tr>
    <?php }?>

</table>
<?php if ($max > 10){?>
<div class="image">
    <a href="<?php echo $domain;?>/activity_rank/active_first?type=1"><img style="margin-bottom: 40px;" src="<?php echo $static_domain;?>/images/20171106/see_more.png"/></a>
</div>
<?php };?>
</div>
<img style="width: 100%;" src="<?php echo $static_domain;?>/images/20171106/body_two.png"/>
<img style="width: 100%;" src="<?php echo $static_domain;?>/images/20171106/body_three.png"/>
</body>
</html>