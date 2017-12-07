<script type="text/javascript">
var odate = new Array();
var pay = new Array();
var withdraw = new Array();
var p_userbuy = new Array();
var lp_buy = new Array();
var seven = new Array();
var fourteen = new Array();
<?php foreach($list AS $key=>$value){?>
	odate[<?php echo $key?>] = '<?php echo $value['odate']?>';
	pay[<?php echo $key?>] = <?php echo $value['pay']?>;
	withdraw[<?php echo $key?>] = <?php echo $value['withdraw']?>;
	p_userbuy[<?php echo $key?>] = <?php echo $value['p_userbuy']?>;
	lp_buy[<?php echo $key?>] = <?php echo $value['lp_buy']?>;
<?php }?>
<?php foreach($sevenList AS $key=>$value){?>
	seven[<?php echo $key?>] = <?php echo $value?>;
<?php }?>
<?php foreach($forteenList AS $key=>$value){?>
	fourteen[<?php echo $key?>] = <?php echo $value?>;
<?php }?>
var show_width = 900;
if(odate.length*50>900){
	show_width = odate.length*50;
}
$(function () {
    $('#in_container').highcharts({
    	chart: {
			renderTo: 'container',
			width: show_width,
			borderWidth: 1
		},
        title: {
            text: '易米融理财',
            x: -20 //center
        },
        subtitle: {
            text: '每天充值金额  定期购买金额   活期购买金额',
            x: -20
        },
        xAxis: {
            categories: odate
        },
        yAxis: {
            title: {
                text: '元'
            }
        },
        tooltip: {
            valueSuffix: '元'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: '充值',
            data: pay
        },{
            name: '取现',
            data: withdraw
        }, {
            name: '定期购买',
            data: p_userbuy
        }, {
            name: '活期购买',
            data: lp_buy
        }]
    });
    $('#out_container').highcharts({
    	chart: {
			renderTo: 'container',
			width: show_width,
			borderWidth: 1
		},
        title: {
            text: '易米融理财',
            x: -20 //center
        },
        subtitle: {
            text: '取现金额、平均7天取现金额、平均15天取现金额',
            x: -20
        },
        xAxis: {
            categories: odate
        },
        yAxis: {
            title: {
                text: '元'
            }
        },
        tooltip: {
            valueSuffix: '元'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: '每日取现',
            data: withdraw
        }, {
            name: '7天平均取现',
            data: seven
        }, {
            name: '15天平均取现',
            data: fourteen
        }]
    });
});
</script>
    
    <<style>
.page {
    overflow: scroll;
}
</style>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo OP_DOMAIN?>trend" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					日期：<input name="stime" readonly="true"  class="date"  value="<?php echo isset($stime) ? $stime : "";?>"  />&nbsp;&nbsp;至
            			 <input name="etime" readonly="true" class="date"  value="<?php echo isset($etime) ? $etime : "";?>"  />
				</td>
				<td><input type="hidden" value="search" name="op"><button type="submit">检索</button></td>
			</tr>
		</table>
	</div>
	</form>
</div>
<div class="pageContent" layoutH="55">
	<div id="in_container" style="min-width: 1500px; height: 400px; margin: 0 auto"></div>
	<div id="out_container" style="min-width: 1500px; height: 400px; margin: 0 auto"></div>
</div>

