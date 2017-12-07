<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $site_info['site_name']; ?></title>
    <style type="text/css">
        .left_postation{
            float: left;
        }
    </style>
    <link type="image/vnd.microsoft.icon" href="http://static1.cmibank.com/common/images/favicon.png" rel="shortcut icon" />
    <link href="<?php echo $static_path;?>themes/default/style.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="<?php echo $static_path;?>themes/css/core.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="<?php echo $static_path;?>themes/css/print.css" rel="stylesheet" type="text/css" media="print"/>
    <link href="<?php echo $static_path;?>uploadify/css/uploadify.css" rel="stylesheet" type="text/css" media="screen"/>
    <!--[if IE]>
    <link href="<?php echo $static_path;?>themes/css/ieHack.css" rel="stylesheet" type="text/css" media="screen"/>
    
    <![endif]-->

    <!--[if lte IE 9]>
    <script src="<?php echo $static_path;?>js/speedup.js" type="text/javascript"></script>
    <![endif]-->

    <script src="<?php echo $static_path;?>js/jquery-1.7.2.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/jquery.cookie.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/jquery.validate.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/jquery.bgiframe.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>uploadify/scripts/jquery.uploadify.js" type="text/javascript"></script>
<script src="http://cdn.hcharts.cn/highcharts/highcharts.js"></script>
    <!-- svg图表  supports Firefox 3.0+, Safari 3.0+, Chrome 5.0+, Opera 9.5+ and Internet Explorer 6.0+ -->
    <script type="text/javascript" src="<?php echo $static_path;?>chart/raphael.js"></script>
    <script type="text/javascript" src="<?php echo $static_path;?>chart/g.raphael.js"></script>
    <script type="text/javascript" src="<?php echo $static_path;?>chart/g.bar.js"></script>
    <script type="text/javascript" src="<?php echo $static_path;?>chart/g.line.js"></script>
    <script type="text/javascript" src="<?php echo $static_path;?>chart/g.pie.js"></script>
    <script type="text/javascript" src="<?php echo $static_path;?>chart/g.dot.js"></script>

    <script src="<?php echo $static_path;?>js/dwz.core.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.util.date.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.validate.method.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.barDrag.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.drag.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.tree.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.accordion.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.ui.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.theme.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.switchEnv.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.alertMsg.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.contextmenu.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.navTab.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.tab.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.resize.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.dialog.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.dialogDrag.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.sortDrag.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.cssTable.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.stable.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.taskBar.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.ajax.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.pagination.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.database.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.datepicker.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.effects.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.panel.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.checkbox.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.history.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.combox.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.print.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>js/dwz.regional.zh.js" type="text/javascript"></script>
    <script src="<?php echo $static_path;?>xheditor/xheditor-1.2.1.min.js" type="text/javascript"></script>
    
    

    <script type="text/javascript">
        $(function(){
            DWZ.init("<?php echo OP_DOMAIN; ?>/dwz.frag.xml?t=2", {
                loginUrl:"http://<?php echo OP_DOMAIN; ?>/login", loginTitle:"登录",	// 弹出登录对话框
                statusCode:{ok:200, error:300, timeout:301}, //【可选】
                pageInfo:{pageNum:"pageNum", numPerPage:"numPerPage", orderField:"orderField", orderDirection:"orderDirection"}, //【可选】
                keys: {statusCode:"statusCode", message:"message"}, //【可选】
                ui:{hideMode:'offsets'}, //【可选】hideMode:navTab组件切换的隐藏方式，支持的值有’display’，’offsets’负数偏移位置的值，默认值为’display’
                debug:false,	// 调试模式 【true|false】
                callback:function(){
                    initEnv();
                    $("#themeList").theme({themeBase:"themes"}); // themeBase 相对于index页面的主题base路径
                }
            });

            //输入框修改操作
            $('._edit_td').die().live('click',function(){
                var _self = $(this);
                var _input_val =  _self.find('input').val();
                _self.find('font').text('');
                _self.find('input').show();
                _self.find('input').val('').focus().val(_input_val);

                _self.find('input').die().live('blur',function(){
                    var _self1 = $(this);
                    //当前数值
                    var _old_val = _self1.attr('data-value');
                    var _val = _self1.val();

                    if(_old_val == _val){
                        _self1.siblings('font').text(_val);
                        _self1.hide();
                        return false;
                    }
                    var _action = _self1.attr('data-action') ? _self1.attr('data-action') : null;

                    if(_action){
                        //获取表单地址 便于统一提交操作
                        var ajax_url = _action + '&value=' + _val;
                        //插入指定input输入框 data-sort=xx 当前排序值（提交时比对）
                        $.getJSON(ajax_url, { _t: (new Date()).valueOf() }, function(json){
                            if(json.statusCode == 200){
                                _self1.siblings('font').text(_val);
                                _self1.hide();
                                _self1.attr('data-value', _val);
                                alertMsg.correct('修改成功！');
                            }else{
                                _self1.siblings('font').text(_old_val);
                                _self1.hide();
                                alertMsg.error('修改失败！');
                            }
                        });
                    }
                });
            });

            //点击链接修改
            $('._edit_td_change').die().live('click',function(){
                var _self = $(this);
                var _value = _self.attr('data-value');

                var _action = _self.attr('data-action') ? _self.attr('data-action') : null;

                if(_action){
                    //获取表单地址 便于统一提交操作
                    var ajax_url = _action + '&value=' + _value;
                    $.getJSON(ajax_url, {_t: (new Date()).valueOf() }, function(json){
                        if(json.statusCode == 200){
                            var _values = _self.attr('data-value-arr');
                            var _value_arr = _values.split('|');
                            $.each( _value_arr, function(i, n){
                                var _n_arr = n.split('#');
                                if(_n_arr[0] == _value){
                                    _self.attr('data-value',_n_arr[1]);
                                    _self.text(_n_arr[2]);
                                    _self.css("color",_n_arr[3]);
                                }
                            });
                            alertMsg.correct('修改成功！');
                        }else{
                            alertMsg.error('修改失败！');
                        }
                    });
                }
            });
        });

    </script>
    <style type="text/css">

        #header .xy_logo {
            float: left;
            height: 50px;
            text-indent: -1000px;
            width: 250px;
        }
        #header .xy_logo {
            background: url("<?php echo $static_path;?>themes/default/images/logo.png") no-repeat scroll 0 0 rgba(0, 0, 0, 0);
        }
    </style>
</head>

<body scroll="no">
<div id="layout">
<div id="header">
    <div class="headerNav">
        <a class="xy_logo" href="<?php echo OP_DOMAIN;?>homepage" title="<?php echo $site_info['site_name']; ?>"><?php echo $site_info['site_name']; ?></a>
        <ul class="nav">
            <li id="switchEnvBox"><a href="javascript:;">您好：<?php echo $managerInfo['realname'];?>&nbsp;[<span style="color: #ff0000"><?php echo $managerInfo['loginTimes'];?></span>]次！</a></li>
            <li><a href="javascript:;">职位：<?php echo $managerInfo['post'].'-'.$managerInfo['inner_group'];?></a></li>
            <li><a href="javascript:;">上次登录时间：<?php echo date("Y-m-d h:i:s", $managerInfo['lastLoginTime']);?></a></li>
            <li><a href="<?php echo OP_DOMAIN;?>system/editpass" target="dialog">修改密码</a></li>
            <li><a href="<?php echo OP_DOMAIN;?>login/logout">退出</a></li>
        </ul>
    </div>

    <!-- navMenu -->

</div>

<div id="leftside">
    <div id="sidebar_s">
        <div class="collapse">
            <div class="toggleCollapse"><div></div></div>
        </div>
    </div>
    <div id="sidebar">
        <div class="toggleCollapse"><h2>主菜单</h2><div>收缩</div></div>

        <div class="accordion" fillSpace="sidebar">
            <?php
                foreach((array)$menu as $k => $v){
            ?>
            <div class="accordionHeader">
                <h2><span>Folder</span><?php echo $v['name'] ?><?php if(in_array($v['name'], $new_admin_arr)){ ?>(<font style="color:red">new!</font>)<?php } ?></h2>
            </div>
            <div class="accordionContent">
                <ul class="tree treeFolder">
                    <?php foreach((array)$v['submenu'] as $sk => $sv){ ?>
                    <li>
                        <a rel="<?php echo md5($sv['name']); ?>" href="<?php echo $sv['url']; ?>" target="navTab"><?php echo $sv['name']; ?></a>                            
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <?php } ?>
             
        </div>
    </div>
</div>
<div id="container">
    <div id="navTab" class="tabsPage">
        <div class="tabsPageHeader">
            <div class="tabsPageHeaderContent"><!-- 显示左右控制时添加 class="tabsPageHeaderMargin" -->
                <ul class="navTab-tab">
                    <li tabid="main" class="main"><a href="javascript:;"><span><span class="home_icon">我的主页</span></span></a></li>
                </ul>
            </div>
            <div class="tabsLeft">left</div><!-- 禁用只需要添加一个样式 class="tabsLeft tabsLeftDisabled" -->
            <div class="tabsRight">right</div><!-- 禁用只需要添加一个样式 class="tabsRight tabsRightDisabled" -->
            <div class="tabsMore">more</div>
        </div>
        <ul class="tabsMoreList">
            <li><a href="javascript:;">我的主页</a></li>
        </ul>
        <div class="navTab-panel tabsPageContent layoutBox">
            <div class="page unitBox">
                <div class="accountInfo">
                    <div class="alertInfo">
                        <p><a href="https://code.csdn.net/dwzteam/dwz_jui/tree/master" target="_blank" style="line-height:19px"><span style="float: left">DWZ框架开源地址</span></a></p>
                        <br><br>
                        <p><a href="https://code.csdn.net/dwzteam/dwz_jui/tree/master/doc" target="_blank" style="line-height:19px"><span style="float: left;font-weight: normal;font-size: 12px">DWZ框架文档地址</span></a></p>
                    </div>
                    <div class="right">
                        <p style="color:red">XYZS官方微博 <a href="http://weibo.com/xyzhushou?topnav=1&wvr=5&topsug=1" target="_blank">http://weibo.com/xyzhushou?topnav=1&wvr=5&topsug=1</a></p>
                    </div>
                    <p><span style="float: left"><?php echo $site_info['site_name']; ?></span></p>
                    <br><br>
                    <p><span style="float: left;font-weight: normal;font-size: 12px">XYZS开发注意事项：<a href="javascript:alert('开发中……');" style="color: #ff0000">[我要去了解开发相关事项]</a></span></p>
                </div>
                <div class="pageFormContent" layoutH="80" style="margin-right:230px;float: left">

                    <h2 class="left_postation">注意事项:</h2>
                    <div class="unit left_postation"><a href="javascript:alert('开发中……');" style="color: #ff0000">[我要去了解开发相关事项]</a></div>

                    <div class="divider" style="display: none"></div>

                </div>

                <div style="width:319px;position: absolute;top:60px;right:0;border: 1px solid #c0c0c0" layoutH="80">
                <!--右侧提示框-->
                </div>
            </div>

        </div>
    </div>
</div>

</div>

<div id="footer"><?php echo $site_info['copyright']; ?></div>

</body>
<script>
$.ajaxSettings.global=false;
</script>
</html>