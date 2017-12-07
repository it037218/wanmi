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

if($max > 200){
    $num = 200;
}else{
    $num = $max;
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="target-densitydpi=device-dpi, width=640px, user-scalable=no"  >
    <meta name="format-detection" telephone="no">
    <title>易米融月庆</title>
    <link rel="stylesheet" href="<?php echo $static_domain; ?>/css/bind_invite_c9adbf9.css?232">
    <link rel="stylesheet" href="<?php echo $static_domain; ?>/css/dialog.css">
    <style>
        /*html{*/
            /*min-width: 620px;*/
        /*}*/
        body{
            font-size: 22px;
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
        th {
            font-size: 1.2rem;
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
            font-size: 1rem;
            font-weight: 500;
        }
    </style>
    <script type="text/javascript" src="<?php echo $static_domain; ?>/common/js/jquery-1.7.1.min.js"></script>
</head>

<body style="background: #FCF5D8;">
<table class="table">
    <tr>
        <th>排名</th>
        <th>三月盈</th>
        <th>六月盈</th>
        <th>易年盈</th>
    </tr>
    <?php for ($i = 0; $i < $num ; $i++){ ?>
    <tr>
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
        </td>
        <td>
            <?php echo isset($list['year_product_tpid'][$i]['account']) ? substr_replace($list['year_product_tpid'][$i]['account'], '*****',3,5) : ''?><br/>
            <?php echo isset($list['year_product_tpid'][$i]['money']) ? $list['year_product_tpid'][$i]['money'].'元' : '';?>
        </td>
    </tr>
    <?php }?>
</table>
</body>
</html>