<?php
/**
 * 用户账户信息管理
 * * */
class userinfomanage extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '用户管理'){
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_useridentity_model', 'useridentity');
        $this->load->model('admin_userproduct_model','userproduct');
        $this->load->model('admin_product_model','product');
        $this->load->model('admin_buy_log_model','buy_log');
        $this->load->model('admin_user_log_model','user_log');
        $this->load->model('admin_product_buy_model','product_buy');
        $this->load->model('admin_up_profit_log_model','up_profit_log');
        $this->load->model('admin_balance_model','balance');
        $this->load->model('admin_longmoney_model','longmoney');
        $this->load->model('admin_ulpprofitlog_model','ulpprofitlog');
        $this->load->model('admin_expmoney_log_model','expmoney_log');
        $this->load->model('admin_user_expmoney_model','user_expmoney');
        $this->load->model('admin_exp_profit_log_model','exp_profit_log');
        $this->load->model('admin_user_expproduct_model','user_expproduct');
        $this->load->model('admin_redbag_model','redbag');
        $this->load->model('admin_user_coupon_model','user_coupon');
        
    }
    public function getUserlogDetails(){
        $flag = $this->op->checkUserAuthority('用户收支明细',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户收支明细');
        }else{
            $data = array();
            $rtn = array();
            $uid=0;
           	$userLogList= array();
            $count=0;
            $page = max(1, intval($this->input->post('pageNum')));
            $psize = max(20, intval($this->input->post('numPerPage')));
            $offset = ($page - 1) * $psize;
            $data['pageNum'] = $page;
            $data['numPerPage'] = $psize;
            if($this->input->post('op') == "search"){
                
                $phone = trim($this->input->post('phone'));
                $type = trim($this->input->post('type'));
                $stime = trim($this->input->post('stime'));
                $etime = trim($this->input->post('etime'));
                if(!empty($phone)){
                    $this->load->model('admin_account_model', 'account');
                    $uid = $this->account->getUidByAccount($phone);
                    if(!empty($uid)){
                        $uid = $uid[0]['uid'];
                    }else{
                    	exit($this->ajaxDataReturn(self::AJ_RET_FAIL,'手机号码未找到',array(),'定期用户购买记录'));
                    }
                    $data['phone'] = $phone;
                }
                $searchparam = array();
               
                if(!empty($uid)){
                    $searchparam['uid'] = $uid;
                
                }
           
                if(!empty($stime)){
                    $searchparam['stime'] = strtotime($stime);
                    $data['stime'] = $stime;
                }else {
                    $searchparam['stime'] = 1462781437;
                }
                if(!empty($etime)){
                    $searchparam['etime'] = strtotime($etime)+86400;
                    $data['etime'] = $etime;
                     
                }else{
                    $searchparam['etime'] = NOW;
                }
                if(!empty($type)){
                    $searchparam['type'] = $type;
                    $data['type'] = $type;
                }else{
                    $data['type'] = 0;
                }
                
                $userLogList= $this->user_log->getUserLogListByCondition($searchparam,$offset,$psize);
                if(!empty($userLogList)){
                    $count =$this->user_log->countUserLogListByCondition($searchparam);
                    $data['count'] = $count;
                }
                
            }else{
                $uid = $this->uri->segment(3);
                $logid = trim($this->uri->segment(4));
                if(!empty($logid)){
                	$data['logid'] = $logid;
                }
                if(!empty($uid)){
                    $searchparam['uid'] = $uid;
                    $this->load->model('admin_account_model', 'account');
                    $phone = $this->account->getAccountByUid($uid);
                    $data['phone'] = $phone['account'];
                    $searchparam['stime'] = 1462781437;
                    $searchparam['etime'] = NOW;
                    $userLogList= $this->user_log->getUserLogListByCondition($searchparam,$offset,$psize);
                    if(!empty($userLogList)){
                        $count =$this->user_log->countUserLogListByCondition($searchparam);
                        $data['count'] = $count;
                        
                    }
                    $data['type'] = 0;
                }else{
                    $data['type'] = 0;
                    $data['count'] = 0;
                    $data['list'] = array();
                    $data['product_money'] = 0;
                    $data['longmoney'] = 0;
                    $data['balance'] = 0;
                    $data['pay_money'] = 0;
                    $data['withdraw_money'] = 0;
                    $data['lprofit'] = 0;
                    $data['sum_product_profit'] = 0;
                    $data['activity_money'] = 0;
                    $data['invite_reward_money'] = 0;
                    $data['tiyangjing_money'] = 0;
                    $data['withdraw_failed'] = 0;
                    $data['withdraw_back'] = 0;
                    $data['diff'] = 0;
                }
            }
            
            if(!empty($uid)){
	            $userproduct_list = $this->userproduct->getUserProductlistByUid($uid,array('status'=>'0','uid'=>$uid));
	            $product_money = 0;
	            foreach ($userproduct_list as $key=>$__val){
	            	$product_money +=$__val['money'];
	            }
	            //定期
	            $data['product_money'] = $product_money;
	            
	            $longmoney = 0;
	            $longmoneyList = $this->longmoney->getLongMoneyByUid($uid);
	            if(!empty($longmoneyList)){
	            	$longmoney=$longmoneyList[0]['money'];
	            }
	            //活期
	            $data['longmoney'] = $longmoney;
	            //余额
	            $balance = $this->balance->get_user_balance($uid);
	            $data['balance'] = $balance;
	            //充值
	            $pay_money=$this->user_log->sum_money_by_action($uid, 0);
	            $data['pay_money'] = $pay_money;
	            //取现
	            $withdraw_money=$this->user_log->sum_money_by_action($uid, 2);
	            $data['withdraw_money'] = $withdraw_money;
	            
	            //活期利息：
	            $lprofit = $this->ulpprofitlog->sum_user_longproduct_profit($uid);
	            $data['lprofit'] = $lprofit;
	            
	            //定期利息
	            $sum_product_profit = $this->userproduct->get_finished_product_profit($uid);
	            $data['sum_product_profit'] = $sum_product_profit;
	            //活动奖励
	            $activity_money=$this->user_log->sum_money_by_action($uid, 5);
	            $data['activity_money'] = $activity_money;
	            //邀请奖励
	            $invite_reward_money=$this->user_log->sum_money_by_action($uid, 6);
	            $data['invite_reward_money'] = $invite_reward_money;
	            //体验金奖励发放
	            $tiyangjing_money=$this->user_log->sum_money_by_action($uid, 7);
	            $data['tiyangjing_money'] = $tiyangjing_money;
	            //取现失败
	            $withdraw_failed=$this->user_log->sum_money_by_action($uid, 20);
	            $data['withdraw_failed'] = $withdraw_failed;
	            //取现退回
	            $withdraw_back=$this->user_log->sum_money_by_action($uid, 21);
	            $data['withdraw_back'] = $withdraw_back;
	            $diff = $balance + $product_money + $longmoney+$withdraw_money-$pay_money - $activity_money - $invite_reward_money - $tiyangjing_money - $sum_product_profit - $lprofit;
	            $data['diff'] = round($diff,2);
            }
            if(!empty($userLogList)){
                $in_array = array(0, 4, 14, 13, 5, 6, 7,10,21);
                $out_array = array(1, 11, 2,20);
                
                foreach ($userLogList as $key=>$val){
                    if(in_array($val['action'], $in_array)){
                        //收入
                        $userLogList[$key]['in'] = $val['money'];
                        $userLogList[$key]['out'] = "--";
                    }else if(in_array($val['action'], $out_array)){
                        //支出
                        $userLogList[$key]['out'] = $val['money'];
                        $userLogList[$key]['in'] = "--";
                    }else{
                        //支出
                        $userLogList[$key]['out'] = $val['money'];
                        $userLogList[$key]['in'] = "--";
                    }
                }
                $data['list'] = $userLogList;
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize; 
                $data['uid'] =$uid;
                $count = count($this->user_log->getUserLoglist(array('uid'=>$uid,'action'=>array(11,13)),'paytime desc',''));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '用户管理', '', '用户收支明细', $this->getIP(), $this->getSession('uid'));
        $this->load->view('/userinfomanage/user_log',$data);
    }
    public function getlongproductDetails($uid){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'定期用户购买记录');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $data = array();
            $userloglist =array();
            $profit = array();
            $buymoney = 0;
            $user_log = $this->user_log->getUserLoglist(array('uid'=>$uid,'action'=>array(11,13)),'ctime desc',array($psize, $offset));
            $longulpprofitlog_info = $this->ulpprofitlog->getUlpProfitLogUid($uid);
            foreach ($longulpprofitlog_info as $_lval){
                if(!isset($profit['countprofit'])){
                    $profit['countprofit'] = 0;
                }
                //活期收益总额
                $profit[$_lval['time']]['yestprofit'] = $_lval['profit'];      //活期  1天收益
                $profit['countprofit'] += $_lval['profit'];
            }
            $in_array = array(0, 4, 14, 13, 5, 6);
            $out_array = array(1, 11, 2);
            $count_in = '';
            $count_out = '';
            foreach ($user_log as $key=>$val){
                if(in_array($val['action'], $in_array)){
                    //收入
                    $userloglist[$key]['in'] = $val['money'];
                    $count_in += $val['money'];
                    $userloglist[$key]['out'] = "--";
                    $userloglist[$key]['orderid'] = $val['orderid'];
                    $userloglist[$key]['ctime'] = $val['ctime'];
                    $userloglist[$key]['pname'] = $val['pname'];
                    $userloglist[$key]['balance'] = $val['balance'];
                }else if(in_array($val['action'], $out_array)){
                    //支出
                    $userloglist[$key]['out'] = $val['money'];
                    $count_out += $val['money'];
                    $userloglist[$key]['in'] = "--";
                    $userloglist[$key]['orderid'] = $val['orderid'];
                    $userloglist[$key]['ctime'] = $val['ctime'];
                    $userloglist[$key]['pname'] = $val['pname'];
                    $userloglist[$key]['balance'] = $val['balance'];
                }
            }
            
            $user_log_in = $this->user_log->inmoney($uid);
            $user_log_out = $this->user_log->outmoney($uid);
            $longmoney = $this->longmoney->getLongMoneyByUid($uid);
            if($longmoney[0]['money'] != 0){
                if($user_log_in[0]['SUM(money)']>$user_log_out[0]['SUM(money)']){
                    $buymoney = $user_log_in[0]['SUM(money)'] - $user_log_out[0]['SUM(money)'];
                }else{
                    $buymoney = $user_log_out[0]['SUM(money)'] - $user_log_in[0]['SUM(money)'];
                }
            }
            
            $data['count_in'] = $count_in;
            $data['count_out'] = $count_out;
            $data['list'] = $userloglist;
            $data['uid'] =$uid;
            $data['buymoney'] =$buymoney;
            $data['profit'] = $profit;
            $count = count($this->user_log->getUserLoglist(array('uid'=>$uid,'action'=>array(11,13)),'paytime desc',''));
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'aboutus/index?page=' . $page;
                if(!empty($aboutustitle)){
                    $data['rel'] .= '&title=' . $aboutustitle;
                }
            }else{
                $data['list'] = $data['page'] = '';
            }
            $this->load->view('/userinfomanage/longProductDetails',$data);
        }
    }


    public function getExpProfitDetails($uid){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'定期用户购买记录');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $data = array();
            $searchstart = trim($this->input->post('searchstart'));
            $searchend = trim($this->input->post('searchend'));
            if($searchstart && $searchstart != '请输入搜索内容' && $this->input->request('op') == "search"){
                
            }else{
               $num = 1;
               $exp_profit_log = $this->exp_profit_log->getExpProfitList(array('uid'=>$uid),'odate desc',array($psize, $offset));
               $count = count($exp_profit_log);
               foreach ($exp_profit_log as $key=>$val){
                   if(!isset($rtnExpProfit[$val['odate']]['money'])){
                       $rtnExpProfit[$val['odate']]['money'] = 0;
                   }
                   if(!isset($rtnExpProfit[$val['odate']]['count_profit'])){
                       $rtnExpProfit[$val['odate']]['count_profit'] = 0;
                   }
                   if(!isset($rtnExpProfit[$val['odate']]['num'])){
                       $rtnExpProfit[$val['odate']]['num'] = 0;
                   }
                   $useridentity = $this->useridentity->getUseridentityByUid($val['uid']);
                   $user_expproduct = $this->user_expproduct->getExpProductByPid($val['uid'],$val['ue_id']);
                   $rtnExpProfit[$val['odate']]['uid'] = $useridentity['uid'];
                   $rtnExpProfit[$val['odate']]['realname'] = $useridentity['realname'];
                   $rtnExpProfit[$val['odate']]['num'] += $num;
                   $rtnExpProfit[$val['odate']]['money'] += $val['money'];
                   $rtnExpProfit[$val['odate']]['count_profit'] += $val['profit'];
                   $rtnExpProfit[$val['odate']]['product_list'][$key] = $val;
                   
                   $rtnExpProfit[$val['odate']]['product_list'][$key]['uietime'] = $user_expproduct['uietime'];
                   $rtnExpProfit[$val['odate']]['product_list'][$key]['uistime'] = date('Y-m-d',strtotime($user_expproduct['uietime'])-(86400*6));
                   $rtnExpProfit[$val['odate']]['product_list'][$key]['income'] = $user_expproduct['income'];
               }
               $exp_profit_log = $this->exp_profit_log->getCountExpProfit($uid);
               $yestProfit = $this->exp_profit_log->getYestExpProfit($uid);
               
               $data['countexprofit'] = $exp_profit_log[0]['sum(profit)'];
               $data['yestexprofit'] = $yestProfit[0]['sum(profit)'];
               
               $nowids = $this->user_expproduct->getUserExpProductUid($uid);
               if(empty($nowids)){
                   $data['nowProfit'] = 0;
               }else{
                   $nowProfit = $this->exp_profit_log->getNowtExpProfit($uid,$nowids);
                   $data['nowProfit'] = $nowProfit[0]['sum(profit)'];
               }

               $data['list'] = $rtnExpProfit;
               $data['uid'] = $uid;
               
               
            }
            if($count>0){
            
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'aboutus/index?page=' . $page;
            }else{
                $data['list'] = $data['page'] = '';
            }

            $this->load->view('/userinfomanage/expprofitDetails',$data);
        }
    }

    public function getproductprofitDetails($uid = ''){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'定期用户购买记录');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $data = array();
            $searchstart = trim($this->input->post('searchstart'));
            $searchend = trim($this->input->post('searchend'));
            if($searchstart && $searchstart != '请输入搜索内容' && $this->input->request('op') == "search"){
                $up_profit_log = $this->up_profit_log->getupprofit($uid,$searchstart,$searchend,array($psize, $offset));
                $count = count($this->up_profit_log->getupprofit($uid,$searchstart,$searchend,''));
            }else{
                $uid = $this->uri->segment(3);
                $rtn_odate = array();
                $up_profit_log = $this->up_profit_log->getUpProfitLogUidList($uid,array('uid'=>$uid),'odate desc',array($psize, $offset));
                $count = count($this->up_profit_log->getUpProfitLogUidList($uid,array('uid'=>$uid),'odate',''));
            }
            $num = 1;
            foreach ($up_profit_log as $key=>$val){
                if(!isset($rtn_odate[$val['odate']]['buymoney'])){
                    $rtn_odate[$val['odate']]['buymoney'] = 0;
                }
                if(!isset($rtn_odate[$val['odate']]['num'])){
                    $rtn_odate[$val['odate']]['num'] = 0;
                }
                if(!isset($rtn_odate[$val['odate']]['count_profit'])){
                    $rtn_odate[$val['odate']]['count_profit'] = 0;
                }
                $useridentity = $this->useridentity->getUseridentityByUid($val['uid']);
                $product= $this->product->getProductByPid($val['pid']);
                $rtn_odate[$val['odate']]['product_list'][$key] = $val;
                $rtn_odate[$val['odate']]['realname'] = $useridentity['realname'];
                $rtn_odate[$val['odate']]['uid'] = $useridentity['uid'];
                $rtn_odate[$val['odate']]['buymoney'] += $val['money'];
                $rtn_odate[$val['odate']]['num'] += $num;
                $rtn_odate[$val['odate']]['count_profit'] += $val['profit'];
                $rtn_odate[$val['odate']]['product_list'][$key]['pname'] = $product['pname'];
                $rtn_odate[$val['odate']]['product_list'][$key]['uistime'] = $product['uistime'];
                $rtn_odate[$val['odate']]['product_list'][$key]['uietime'] = $product['uietime'];
                $rtn_odate[$val['odate']]['product_list'][$key]['income'] = $product['income']; 
            }
            $ontotalprofit = $this->up_profit_log->oneTotal_up_profit($uid);
            $data['ontotalprofit'] = $ontotalprofit;
            $data['list'] = $rtn_odate;
            $data['uid'] = $uid;
            if($count>0){
            
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'aboutus/index?page=' . $page;
                if(!empty($aboutustitle)){
                    $data['rel'] .= '&title=' . $aboutustitle;
                }
            }else{
                $data['list'] = $data['page'] = '';
            }
            $this->load->view('/userinfomanage/productprofitDetails',$data);
        }
        
        
    }
    public function getProductDetails(){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'定期用户购买记录');
        }else{
            $uid = $this->uri->segment(3);
            $status = $this->uri->segment(4);
            $userproduct_list = array();
            $userproduct_count = array();
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $asc = htmlspecialchars($this->input->request('asc'));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $searchtitle = trim($this->input->post('searchtitle'));
            $searchtype = trim($this->input->post('searchtype'));
            $searchpname = trim($this->input->post('searchpname'));
            $searchtrxid = trim($this->input->post('searchtrxid'));
            $searchstatus = trim($this->input->post('searchstatus'));
            if($searchtrxid && $searchtrxid != '请输入搜索的订单号' && $this->input->request('op') == "search"){ 
                $buy_log = $this->buy_log->getBuyLog($searchtrxid);
                if(count($buy_log)==0){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
                $uid=$buy_log[0]['uid'];
                $userproduct_list = $this->userproduct->getUserProductTrxIdByUid($uid,$searchtrxid);
                foreach ($userproduct_list as $key=>$val){
                    $product = $this->product->getRedisProductDetailInfo($val['pid']);
                    $useridentity = $this->useridentity->getUseridentityByUid($uid);
                    $userproduct_list[$key]['uistime'] = $product['uistime'];
                    $userproduct_list[$key]['uietime'] = $product['uietime'];
                    $userproduct_list[$key]['day'] = ((strtotime($product['uietime'])-strtotime($product['uistime']))/3600/24) + 1;
                    $userproduct_list[$key]['realname'] = $useridentity['realname'];
                    $userproduct_list[$key]['phone'] = $useridentity['phone'];
                    $userproduct_list[$key]['profit'] = ($val['income']/365)*$userproduct_list[$key]['day'];
                    $userproduct_list[$key]['principal'] = $userproduct_list[$key]['profit']+$val['money'];
                    $userproduct_list[$key]['yest_profit'] = ($val['income']/365)*$val['money']/100;
                    $userproduct_list[$key]['pro'] = $this->product_profit($product['uistime'],$product['uietime'],$val['income'],$val['money']);
                }
                $count=count($userproduct_list);
                $data['searchtrxid'] = $searchtrxid;
                $data['userproduct_list'] = $userproduct_list;
            }else if($searchstatus && $this->input->request('op') == "search"){
                $uid = $this->uri->segment(3);
                if($searchstatus==1){
                    //全部
                    $status = array(0,1);
                }else if($searchstatus==2){
                    //未还款
                    $status = 0;
                }else if($searchstatus==3){
                    //已还款
                    $status = 1;
                }
                $userproduct_list= $this->userproduct->getUserProductlistByUid($uid,array('uid'=>$uid,'status'=>$status),'buytime desc',array($psize, $offset));
                foreach ($userproduct_list as $key=>$val){
                    $product = $this->product->getRedisProductDetailInfo($val['pid']);
                    $useridentity = $this->useridentity->getUseridentityByUid($uid);
                    $userproduct_list[$key]['uistime'] = $product['uistime'];
                    $userproduct_list[$key]['uietime'] = $product['uietime'];
                    $userproduct_list[$key]['day'] = ((strtotime($product['uietime'])-strtotime($product['uistime']))/3600/24) + 1;
                    $userproduct_list[$key]['realname'] = $useridentity['realname'];
                    $userproduct_list[$key]['phone'] = $useridentity['phone'];
                    $userproduct_list[$key]['profit'] = ($val['income']/365)*$userproduct_list[$key]['day'];
                    $userproduct_list[$key]['principal'] = $userproduct_list[$key]['profit']+$val['money'];
                    $userproduct_list[$key]['yest_profit'] = ($val['income']/365)*$val['money']/100;
                    $userproduct_list[$key]['pro'] = $this->product_profit($product['uistime'],$product['uietime'],$val['income'],$val['money']);
                }
                $data['userproduct_list'] = $userproduct_list;
                $count = count($this->userproduct->getUserProductlistByUid($uid,array('uid'=>$uid,'status'=>$status),''));

            }else if($searchpname && $searchpname != '请输入查询产品名字' && $this->input->request('op') == "search"){
                $uid = $this->uri->segment(3);
                $product = $this->product->getProductList(array('pname'=>$searchpname), '','');
                $pid = $product[0]['pid'];
                $product_buy = $this->product_buy->getProductBuyInfoByPid(array('pid'=>$pid,'uid'=>$uid),'',array($psize, $offset));
                $product = $this->product->getRedisProductDetailInfo($pid);
                $useridentity = $this->useridentity->getUseridentityByUid($uid);
                foreach ($product_buy as $key=>$val){ 
                    $userproduct_list[$key]['status'] = isset($val['b_trxid']) ? '0' : '1';
                    $userproduct_list[$key]['trxId'] = $val['trxId'] ? $val['trxId'] : '未知订单';
                    $userproduct_list[$key]['uid'] = $val['uid'];
                    $userproduct_list[$key]['money'] = $val['money'];
                    $userproduct_list[$key]['uistime'] = $product['uistime'];
                    $userproduct_list[$key]['uietime'] = $product['uietime'];
                    $userproduct_list[$key]['income'] = $product['income'];
                    $userproduct_list[$key]['pname'] = $product['pname'];
                    $userproduct_list[$key]['day'] = ((strtotime($product['uietime'])-strtotime($product['uistime']))/3600/24) + 1;
                    $userproduct_list[$key]['profit'] = ($product['income']/365)*$userproduct_list[$key]['day'];
                    $userproduct_list[$key]['principal'] = $userproduct_list[$key]['profit']+$val['money'];
                    $userproduct_list[$key]['yest_profit'] = ($product['income']/365)*$val['money']/100;
                    $userproduct_list[$key]['realname'] = $useridentity['realname'];
                    $userproduct_list[$key]['phone'] = $useridentity['phone'];
                    $userproduct_list[$key]['buytime'] = $val['ctime'];
                    $userproduct_list[$key]['repaytime'] = $val['b_time'];
                }
                $data['userproduct_list'] = $userproduct_list;
                $count = count($this->product_buy->getProductBuyInfoByPid(array('pid'=>$pid,'uid'=>$uid),''));
                $data['searchpname'] = $searchpname;
                
            }else{
                if (!$orderby) {
                    $orderby = 'ctime';
                }
                if (!$asc) {
                    $asc = 'ASC';
                }
                $userproduct_list= $this->userproduct->getUserProductlistByUid($uid,array('uid'=>$uid,'status'=>$status),'buytime desc',array($psize, $offset));
                foreach ($userproduct_list as $key=>$val){
                    $product = $this->product->getRedisProductDetailInfo($val['pid']);
                    $useridentity = $this->useridentity->getUseridentityByUid($uid);
                    $userproduct_list[$key]['uistime'] = $product['uistime'];
                    $userproduct_list[$key]['uietime'] = $product['uietime'];
                    $userproduct_list[$key]['day'] = ((strtotime($product['uietime'])-strtotime($product['uistime']))/3600/24) + 1;
                    $userproduct_list[$key]['realname'] = $useridentity['realname'];
                    $userproduct_list[$key]['phone'] = $useridentity['phone'];
                    $userproduct_list[$key]['profit'] = ($val['income']*$val['money']/365/100)*$userproduct_list[$key]['day'];
                    $userproduct_list[$key]['principal'] = $userproduct_list[$key]['profit']+$val['money'];
                    $userproduct_list[$key]['yest_profit'] = ($val['income']/365)*$val['money']/100;
                    $userproduct_list[$key]['pro'] = $this->product_profit($product['uistime'],$product['uietime'],$val['income'],$val['money']);
                }
                $data['userproduct_list'] = $userproduct_list;
                
                $count = $this->userproduct->getUserProductCountByUid($uid,array('uid'=>$uid,'status'=>$status));
            }
            $money = $this->userproduct->money($uid);
            $repaymoney = $this->userproduct->repaymoney($uid);
            $data['money'] = $money[0]['SUM(money)'];
            $data['repaymoney'] = $repaymoney[0]['SUM(money)'];
            if($count>0){
                $data['uid'] =$uid; 
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . '/userinfomanage/getProductDetails/'.$uid.'/?page='.$page;
                if(!empty($searchtitle)){
                    $data['rel'] .= '&title=' . $searchtitle;
                }
            }else{
                    $data['pageNum']    = 0;
                    $data['numPerPage'] = 0;
                    $data['count'] = 0;
                    $data['userproduct_count'] = $data['page'] = '';
            }
            $log = $this->op->actionData($this->getSession('name'), '用户管理', '', '定期用户购买记录', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/userinfomanage/productDetails',$data);
        }

    }

       public function index(){
           $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
           if($flag == 0){
              echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'关于公司信息管理');
           }else{
               $data = array();
               $orderby = htmlspecialchars($this->input->request("orderby"));
               $searchtitle = trim($this->input->post('searchtitle'));
               $searchtype = trim($this->input->post('searchtype'));
               if($searchtitle && $searchtitle != '请输入搜索内容' && $this->input->request('op') == "search"){
                   if($searchtype == 1){
                  $where = array('realname'=>$searchtitle);
                }else if($searchtype == 3){
                  $where = array('idCard'=>$searchtitle);
                }
                $useridentity = $this->useridentity->getUseridentityList($where,'uid desc','');
                $this->onePersonaldetail($useridentity[0]['uid']);
               }else{
                   //总资产
                         
                   // 账户余额
                   $balance = $this->balance->getSumBalanceMoney();
                   $Tota_balance = $balance[0]['sum(balance)'];
                   $data['Tota_balance'] = $Tota_balance;
                   
                   //定期投资总额
                   $Tota_productmoney = 0;
                   for($_table_index=0;$_table_index<16;$_table_index++){
                       $productmoney = $this->userproduct->getSumProductMoney($_table_index);
                       $Tota_productmoney += $productmoney[0]['SUM(money)'];
                        
                   }
                   $data['Tota_productmoney'] = $Tota_productmoney;
                   
                   //活期总资产
                   $longmoney = $this->longmoney->getSumLongMoney();
                   $Tota_longmoney = $longmoney[0]['sum(money)'];
                   $data['Tota_longmoney'] = $Tota_longmoney;
                   
                   
                   //昨日收益  = 昨日定期收益 + 昨日活期收益
                   
                   //昨日定期收益
                   $TotalYest_up_proift = $this->up_profit_log->TotalYest_up_proift();
                   $data['TotalYest_up_proift'] = $TotalYest_up_proift;
                   
                   //昨日活期收益
                   $total_yesy_ulp_profit = $this->ulpprofitlog->total_yesy_ulp_profit();
                   $data['total_yesy_ulp_profit'] = $total_yesy_ulp_profit;
                   //累计收益 = 累计定期收益 + 累计活期收益
                  
                   //累计定期收益
                   $total_up_profit = $this->up_profit_log->TotalCount_up_profit();
                   $data['total_up_profit'] = $total_up_profit;
				   
                   //累计活期收益
                   $Total_ulp_profit = $this->ulpprofitlog->Total_Count_ulp_profit();
                   $data['Total_ulp_profit'] = $Total_ulp_profit;
				   
                   //当前定期收益
                   $totalnow_profit = 0;
                   for($_table_index=0;$_table_index<16;$_table_index++){
                       $pid[] = $this->userproduct->getProductPid($_table_index);
                   
                   }
				  
                   $pids = implode(",", array_filter($pid));
                   for($index = 0;$index<32;$index++){
                       $up_profit_log = $this->up_profit_log->getUpProfitLog($index,$pids);

					   
                       $totalnow_profit += $up_profit_log[0]['sum(profit)'];
                   }
                   $data['totalnow_profit'] = $totalnow_profit;
    
                   //昨日体验金收益
                   $TotalYestExpproift = 0;
                   for($index = 0;$index<16;$index++){
                       $exp_profit_log = $this->exp_profit_log->getTotalYestExpProfit($index);
                       $TotalYestExpproift += $exp_profit_log[0]['sum(profit)'];
                   }
                   $data['TotalYestExpproift'] = $TotalYestExpproift;
                   
                   //累计体验金收益
                   $totalexpprofit = 0;
                   for($index = 0;$index<16;$index++){
                       $exp_profit_log = $this->exp_profit_log->geTotalExpProfit($index,'');
                       $totalexpprofit += $exp_profit_log[0]['sum(profit)'];
                   }
                   $data['totalexpprofit'] = $totalexpprofit;
                   
                   //当前体验金收益
                   $totalnowexp = 0;
                   for($index = 0;$index<16;$index++){
                       $ids[] = $this->user_expproduct->getUserExpProduct($index);
                   }
                   $ids = implode(",", $ids);
                   for($index = 0;$index<16;$index++){
                       $countProfit[] = $this->exp_profit_log->geTotalExpProfit($index,$ids);
                   }
                   foreach($countProfit as $val){
                       $totalnowexp += $val[0]['sum(profit)'];
                   }
                   $data['totalnowexp'] = $totalnowexp;
                   //可投体验金
                    $expmoney = $this->user_expmoney->sumUnusedExpmoney();
       				$data['totalexpmoney'] = $expmoney;
				  
                   $this->load->view('/userinfomanage/v_index2',$data);
               }
           }
       }  
       
       
    public function index11(){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'关于公司信息管理');
        }else{
            $balance_list = array();                       //账户余额
            $longmoney_list =array();                      //活期投资总额
            $userproduct_countmoney = array();             // 定期投资总额
            $longulpprofitlog_list = array();                  //活期昨日收益
            $longmoney = $this->longmoney->getLongMoneyList();
            $balance = $this->balance->getBalanceList();
            $now_up_profit = array();
            
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            
            $data = array();
            $offset = ($page - 1) * $psize;
            $asc = htmlspecialchars($this->input->request('asc'));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $searchtitle = trim($this->input->post('searchtitle'));
            $searchtype = trim($this->input->post('searchtype'));
            if($searchtitle && $searchtitle != '请输入搜索内容' && $this->input->request('op') == "search"){
                if (!$orderby) {
                    $orderby = 'ctime';
                }
                if (!$asc) {
                    $asc = 'ASC';
                }
                if($searchtype == 1){
                  $where = array('realname'=>$searchtitle);
                }else if($searchtype == 3){
                  $where = array('idCard'=>$searchtitle);
                }
                $useridentity = $this->useridentity->getUseridentityList($where,'uid desc',array($psize, $offset));
                if(empty($useridentity)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
                foreach ($useridentity as $key=>$val){
                    $up_profit_log[$val['uid']] = $this->up_profit_log->getUpProfitLogUid($val['uid']);
                    $rtnuserproduct = array();
                    $userproduct_list = $this->userproduct->getUserProductlistByUid($val['uid'],array('status'=>'0','uid'=>$val['uid']));
                    foreach ($userproduct_list as $key=>$__val){
                        $rtnuserproduct[$__val['uid']][$key] = $__val['pid'];
                        if(!isset($userproduct_countmoney[$__val['uid']]['count_money'])){
                            $userproduct_countmoney[$__val['uid']]['count_money'] = 0;
                        }
                        if(!isset($userproduct_countmoney['count_money'])){
                            $userproduct_countmoney['count_money'] = 0;
                        }
                        $userproduct_countmoney[$__val['uid']]['count_money'] +=$__val['money'];
                        //                         $userproduct_countmoney['count_money'] +=$__val['money'];
                    }
                    if(empty($rtnuserproduct)){
                        $rtnuserproduct = null;
                    }
                    $UpProfitLogUidList = $this->up_profit_log->getUpProfitLogUidList($val['uid'],array('pid' =>$rtnuserproduct[$val['uid']],'uid'=>$val['uid']));
                     
                    foreach ($UpProfitLogUidList as $value){
                        if(!isset($up_profit_log[$value['uid']]['now_profit'])){
                            $up_profit_log[$value['uid']]['now_profit'] = 0;
                        }
                        if(!isset($up_profit_log['totalnow_profit'])){
                            $up_profit_log['totalnow_profit'] = 0;
                        }
                        $up_profit_log[$value['uid']]['now_profit'] += $value['profit'];
                        $up_profit_log['totalnow_profit'] += $value['profit'];
                    }
                
                    if(!isset($up_profit_log[$val['uid']]['count_profit'])){
                        $up_profit_log[$val['uid']]['count_profit'] = 0;
                    }
                    if(!isset($up_profit_log[$val['uid']]['yest_profit'])){
                        $up_profit_log[$val['uid']]['yest_profit'] = 0;
                    }
                
                    foreach ($up_profit_log[$val['uid']] as $_val){
                        $up_profit_log[$val['uid']]['count_profit'] += $_val['profit'];
                        if(date('Y-m-d',time()-84600) == $_val['odate']){
                            $up_profit_log[$val['uid']]['yest_profit'] += $_val['profit'];
                        }
                    }
                    $longulpprofitlog_info = $this->ulpprofitlog->getUlpProfitLogUid($val['uid']);
                    foreach ($longulpprofitlog_info as $_lval){
                        if(!isset($longulpprofitlog_list[$_lval['uid']]['count_profit'])){
                            $longulpprofitlog_list[$_lval['uid']]['count_profit'] = 0;
                        }
                        $longulpprofitlog_list[$_lval['uid']]['count_profit'] +=$_lval['profit'];               //活期收益总额
                        $longulpprofitlog_list[$_lval['uid']][$_lval['time']]['profit'] = $_lval['profit'];      //活期  1天收益
                    }
                }
                if(!isset($balance_list['count']) && !isset($longmoney_list['count'])){
                    $balance_list['count'] =0;
                    $longmoney_list['count'] = 0;
                }
                $balance_count = array();
                foreach ($balance as $_bval){
                    $balance_list[$_bval['uid']] = $_bval['balance'];
                    $balance_list['count'] += $_bval['balance'];
                }
                foreach ($longmoney as $__val){
                    $longmoney_list[$__val['uid']] = $__val['money'];
                    $longmoney_list['count'] += $__val['money'];
                }
                
                
                $data['balance_list'] = $balance_list;
                $data['longmoney_list'] = $longmoney_list;
                $data['userproduct_list'] = $userproduct_countmoney;
                $data['longulpprofitlog_list'] = $longulpprofitlog_list;
                $data['list'] = $useridentity;
                $data['up_profit_log'] = $up_profit_log;
                $count = $this->useridentity->getUseridentityCount();
                //统计
                $useridentity = $this->useridentity->getUseridentityList('','','');
                foreach ($useridentity as $key=>$val){
                    $rtnuserproduct = array();
                    $userproduct_list = $this->userproduct->getUserProductlistByUid($val['uid'],array('status'=>'0','uid'=>$val['uid']));
                    if(!isset($total_product_money['count'])){
                        $total_product_money['count'] = 0;
                    }
                    foreach ($userproduct_list as $key=>$__val){
                        $rtnuserproduct[$__val['uid']][$key] = $__val['pid'];
                        $total_product_money['count'] +=$__val['money'];
                         
                    }
                    if(empty($rtnuserproduct)){
                        $rtnuserproduct = null;
                    }
                    $UpProfitLogUidList = $this->up_profit_log->getUpProfitLogUidList($val['uid'],array('pid' =>$rtnuserproduct[$val['uid']],'uid'=>$val['uid']));
                    foreach ($UpProfitLogUidList as $value){
                        if(!isset($totalnow_profit['count'])){
                            $totalnow_profit['count'] = 0;
                        }
                        $totalnow_profit['count'] += $value['profit'];
                    }
                }
                $total_up_profit = $this->up_profit_log->TotalCount_up_profit();
                $TotalYest_up_proift = $this->up_profit_log->TotalYest_up_proift();
                $Total_ulp_profit = $this->ulpprofitlog->Total_Count_ulp_profit();
                $total_yesy_ulp_profit = $this->ulpprofitlog->total_yesy_ulp_profit();
                $data['total_up_profit'] = $total_up_profit;
                $data['TotalYest_up_proift'] = $TotalYest_up_proift;
                $data['Total_ulp_profit'] = $Total_ulp_profit;
                $data['total_yesy_ulp_profit'] = $total_yesy_ulp_profit;
                $data['total_product_money'] = $total_product_money;
                $data['totalnow_profit'] = $totalnow_profit;
                $data['searchtitle'] = $searchtitle;
            }else{
                if (!$orderby) {
                    $orderby = 'ctime';
                }
                if (!$asc) {
                    $asc = 'ASC';
                }
                $uid = $this->uri->segment(3);
                if($uid){
                    $where = array('uid'=>$uid);
                }else{
                    $where ='';
                }
                $useridentity = $this->useridentity->getUseridentityList($where,'uid desc',array($psize, $offset));
                foreach ($useridentity as $key=>$val){
                    $up_profit_log[$val['uid']] = $this->up_profit_log->getUpProfitLogUid($val['uid']);
                    $rtnuserproduct = array();
                    $userproduct_list = $this->userproduct->getUserProductlistByUid($val['uid'],array('status'=>'0','uid'=>$val['uid']));
                    foreach ($userproduct_list as $key=>$__val){
                        $rtnuserproduct[$__val['uid']][$key] = $__val['pid'];
                        if(!isset($userproduct_countmoney[$__val['uid']]['count_money'])){
                            $userproduct_countmoney[$__val['uid']]['count_money'] = 0;
                        }
                        if(!isset($userproduct_countmoney['count_money'])){
                            $userproduct_countmoney['count_money'] = 0;
                        }
                        $userproduct_countmoney[$__val['uid']]['count_money'] +=$__val['money'];
                    }
                    if(empty($rtnuserproduct)){
                        $rtnuserproduct = null;
                    }
                    $UpProfitLogUidList = $this->up_profit_log->getUpProfitLogUidList($val['uid'],array('pid' =>$rtnuserproduct[$val['uid']],'uid'=>$val['uid']));
                   
                    foreach ($UpProfitLogUidList as $value){
                        if(!isset($up_profit_log[$value['uid']]['now_profit'])){
                           $up_profit_log[$value['uid']]['now_profit'] = 0;
                        }
                        if(!isset($up_profit_log['totalnow_profit'])){
                            $up_profit_log['totalnow_profit'] = 0;
                        }
                        $up_profit_log[$value['uid']]['now_profit'] += $value['profit'];
                        $up_profit_log['totalnow_profit'] += $value['profit'];
                    }
                    
                    if(!isset($up_profit_log[$val['uid']]['count_profit'])){
                        $up_profit_log[$val['uid']]['count_profit'] = 0;
                    }
                    if(!isset($up_profit_log[$val['uid']]['yest_profit'])){
                        $up_profit_log[$val['uid']]['yest_profit'] = 0;
                    }
                    
                    foreach ($up_profit_log[$val['uid']] as $_val){
                        $up_profit_log[$val['uid']]['count_profit'] += $_val['profit'];
                        if(date('Y-m-d',time()-84600) == $_val['odate']){
                            $up_profit_log[$val['uid']]['yest_profit'] += $_val['profit'];
                        }
                    }
                    $longulpprofitlog_info = $this->ulpprofitlog->getUlpProfitLogUid($val['uid']);
                    foreach ($longulpprofitlog_info as $_lval){
                        if(!isset($longulpprofitlog_list[$_lval['uid']]['count_profit'])){
                            $longulpprofitlog_list[$_lval['uid']]['count_profit'] = 0;
                        }
                        $longulpprofitlog_list[$_lval['uid']]['count_profit'] +=$_lval['profit'];               //活期收益总额
                        $longulpprofitlog_list[$_lval['uid']][$_lval['time']]['profit'] = $_lval['profit'];      //活期  1天收益
                    }
                }
                if(!isset($balance_list['count']) && !isset($longmoney_list['count'])){
                    $balance_list['count'] =0;
                    $longmoney_list['count'] = 0;
                }
                $balance_count = array();
                foreach ($balance as $_bval){
                    $balance_list[$_bval['uid']] = $_bval['balance'];
                    $balance_list['count'] += $_bval['balance'];
                }
                foreach ($longmoney as $__val){
                    $longmoney_list[$__val['uid']] = $__val['money'];
                    $longmoney_list['count'] += $__val['money'];
                }
                
                
                $data['balance_list'] = $balance_list;
                $data['longmoney_list'] = $longmoney_list;
                $data['userproduct_list'] = $userproduct_countmoney;
                $data['longulpprofitlog_list'] = $longulpprofitlog_list;
                $data['list'] = $useridentity;
                $data['up_profit_log'] = $up_profit_log;
                $count = $this->useridentity->getUseridentityCount();
                 //统计
                $useridentity = $this->useridentity->getUseridentityList('','','');
                foreach ($useridentity as $key=>$val){
                    $rtnuserproduct = array();
                    $userproduct_list = $this->userproduct->getUserProductlistByUid($val['uid'],array('status'=>'0','uid'=>$val['uid']));
                    if(!isset($total_product_money['count'])){
                        $total_product_money['count'] = 0;
                    }
                    foreach ($userproduct_list as $key=>$__val){
                        $rtnuserproduct[$__val['uid']][$key] = $__val['pid'];
                        $total_product_money['count'] +=$__val['money'];
                         
                    }
                    if(empty($rtnuserproduct)){
                        $rtnuserproduct = null;
                    }
                    $UpProfitLogUidList = $this->up_profit_log->getUpProfitLogUidList($val['uid'],array('pid' =>$rtnuserproduct[$val['uid']],'uid'=>$val['uid']));
                    foreach ($UpProfitLogUidList as $value){
                        if(!isset($totalnow_profit['count'])){
                            $totalnow_profit['count'] = 0;
                        }
                        $totalnow_profit['count'] += $value['profit'];
                    }
                } 
                $total_up_profit = $this->up_profit_log->TotalCount_up_profit();
                $TotalYest_up_proift = $this->up_profit_log->TotalYest_up_proift();
                $Total_ulp_profit = $this->ulpprofitlog->Total_Count_ulp_profit();
                $total_yesy_ulp_profit = $this->ulpprofitlog->total_yesy_ulp_profit();
                $data['total_up_profit'] = $total_up_profit;
                $data['TotalYest_up_proift'] = $TotalYest_up_proift;
                $data['Total_ulp_profit'] = $Total_ulp_profit;
                $data['total_yesy_ulp_profit'] = $total_yesy_ulp_profit;
                $data['total_product_money'] = $total_product_money;
                $data['totalnow_profit'] = $totalnow_profit;
                
                
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'userinfomanage/index?page=' . $page;
                if(!empty($searchtitle)){
                    $data['rel'] .= '&title=' . $searchtitle;
                }
            }else{
                $data['list'] = $data['page'] = '';
            }
            $this->load->view('/userinfomanage/v_index',$data);
        }
    }
    
    public function onePersonaldetail($uid=null){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'关于公司信息管理');
        }else{
            $expmoney_list = array();   //可用体验金
            $count_expmoney = array();                   
            $balance_list = array();                       //账户余额
            $longmoney_list =array();                      //活期投资总额
            $userproduct_countmoney =array();
            $longulpprofitlog_list = array();
            $searchtitle = trim($this->input->post('searchtitle'));
            $searchtype = trim($this->input->post('searchtype'));
            if($searchtitle && $searchtitle != '请输入搜索内容' && $this->input->request('op') == "search"){
                if($searchtype == 1){
                    $where = array('realname'=>$searchtitle);
                }else if($searchtype == 2){
                    $where = array('phone'=>$searchtitle);
                }else if($searchtype == 3){
                    $where = array('idCard'=>$searchtitle);
                }
                 
            }else{
                $where = array('uid'=>$uid);  
            }
            $useridentity = $this->useridentity->getUseridentityList($where,'','');
            foreach ($useridentity as $key=>$val){
                $up_profit_log[$val['uid']] = $this->up_profit_log->getUpProfitLogUid($val['uid']);
                $rtnuserproduct = array();
                $userproduct_list = $this->userproduct->getUserProductlistByUid($val['uid'],array('status'=>'0','uid'=>$val['uid']));
                foreach ($userproduct_list as $key=>$__val){
                    $rtnuserproduct[$__val['uid']][$key] = $__val['pid'];
                    if(!isset($userproduct_countmoney[$__val['uid']]['count_money'])){
                        $userproduct_countmoney[$__val['uid']]['count_money'] = 0;
                    }
                    if(!isset($userproduct_countmoney['count_money'])){
                        $userproduct_countmoney['count_money'] = 0;
                    }
                    $userproduct_countmoney[$__val['uid']]['count_money'] +=$__val['money'];
                }
                if(empty($rtnuserproduct)){
                    $rtnuserproduct = null;
                }
                $UpProfitLogUidList = $this->up_profit_log->getUpProfitLogUidList($val['uid'],array('pid' =>$rtnuserproduct[$val['uid']],'uid'=>$val['uid']));
                 
                foreach ($UpProfitLogUidList as $value){
                    if(!isset($up_profit_log[$value['uid']]['now_profit'])){
                        $up_profit_log[$value['uid']]['now_profit'] = 0;
                    }
                    if(!isset($up_profit_log['totalnow_profit'])){
                        $up_profit_log['totalnow_profit'] = 0;
                    }
                    $up_profit_log[$value['uid']]['now_profit'] += $value['profit'];
                    $up_profit_log['totalnow_profit'] += $value['profit'];
                }
            
                if(!isset($up_profit_log[$val['uid']]['count_profit'])){
                    $up_profit_log[$val['uid']]['count_profit'] = 0;
                }
                if(!isset($up_profit_log[$val['uid']]['yest_profit'])){
                    $up_profit_log[$val['uid']]['yest_profit'] = 0;
                }
            
                foreach ($up_profit_log[$val['uid']] as $_val){
                    $up_profit_log[$val['uid']]['count_profit'] += $_val['profit'];
                    if(date('Y-m-d',time()-84600) == $_val['odate']){
                        $up_profit_log[$val['uid']]['yest_profit'] += $_val['profit'];
                    }
                }
                $longulpprofitlog_info = $this->ulpprofitlog->getUlpProfitLogUid($val['uid']);
                foreach ($longulpprofitlog_info as $_lval){
                    if(!isset($longulpprofitlog_list[$_lval['uid']]['count_profit'])){
                        $longulpprofitlog_list[$_lval['uid']]['count_profit'] = 0;
                    }
                    $longulpprofitlog_list[$_lval['uid']]['count_profit'] +=$_lval['profit'];               //活期收益总额
                    $longulpprofitlog_list[$_lval['uid']][$_lval['time']]['profit'] = $_lval['profit'];      //活期  1天收益
                }
                $balance = $this->balance->getBalanceByUid($val['uid']);
                foreach ($balance as $_bval){
                    $balance_list[$_bval['uid']] = $_bval['balance'];
                }
                
                $longmoney = $this->longmoney->getLongMoneyByUid($val['uid']);
                foreach ($longmoney as $__val){
                    $longmoney_list[$__val['uid']] = $__val['money'];
            
                }
            }
            $expmoneyList = $this->user_expmoney->get_user_expmoney_list($uid);
            $using_eids = array();
            $sum_notusedexpmoney=0;
           	$sum_usingexpmoney=0;
            if(!empty($expmoneyList)){
            	foreach ($expmoneyList as $expmoney_val){
            		if($expmoney_val['status']==1){
            			$using_eids[] = $expmoney_val['id'];
            			$sum_usingexpmoney+=$expmoney_val['money'];
            		}else if($expmoney_val['status']==0){
            			$sum_notusedexpmoney+=$expmoney_val['money'];
            		}
            	}
            }
            $exp_profit_log = $this->exp_profit_log->getCountExpProfit($uid);
            $yestProfit = $this->exp_profit_log->getYestExpProfit($uid);
            
            if(!empty($using_eids)){
                $nowProfit = $this->exp_profit_log->getNowtExpProfit($uid,implode(',', $using_eids));
                $data['nowProfit'] = $nowProfit[0]['sum(profit)'];
            }else{
                $data['nowProfit'] = 0;
            }
            
            $this->load->model('admin_user_coupon_model','admin_user_coupon_model');
            $couponcount = $this->admin_user_coupon_model->countUserCouponList($uid);
            $data['couponcount'] = $couponcount;
            $data['yestexprofit'] = $yestProfit[0]['sum(profit)'];
            $data['countexprofit'] = $exp_profit_log[0]['sum(profit)'];
            $data['expmoney'] = $sum_usingexpmoney;
            
            $data['balance_list'] = $balance_list;
            $data['longmoney_list'] = $longmoney_list;
            $data['userproduct_list'] = $userproduct_countmoney;
            $data['longulpprofitlog_list'] = $longulpprofitlog_list;
            $data['list'] = $useridentity;
            $data['up_profit_log'] = $up_profit_log;
            
            $jiangli = $this->getHuodongshouyi($uid);
            $data['jiangli']=$jiangli['total'];
            $this->load->view('/userinfomanage/v_onePersonaldetail',$data);
        }
        
        
    }
    public function now_profit($money,$income){
        $pro = ($money*$income)/360/100;
        return $pro;
    }
    //计算每个定期产品收益
    public function product_profit($starttime, $endtime, $income, $money){
        //$starttime='2015-06-10'; $endtime='2015-06-25'; $income='10';
        if(mktime(0,0,0)<strtotime($endtime)){
            $pro=($income/360)*$money*((mktime(0,0,0)-strtotime($starttime))/3600/24)/100;
        }else{
            $pro=((strtotime($endtime)-strtotime($starttime)+86400)/3600/24)*$income/360*$money/100;
        }
        return $pro; 
    }
    public function getExpmoneyLogById($uid=''){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'关于公司信息管理');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $asc = htmlspecialchars($this->input->request('asc'));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $data = array();
            
            $expmoney_log = $this->expmoney_log->getExpmoneLogyByUid(array('uid'=>$uid),'ctime desc',array($psize,$offset));
            foreach ($expmoney_log as $key=>$val){
                $rtnExp[$key]['uid'] = $val['uid'];
                $rtnExp[$key]['ctime'] = $val['ctime'];
                $rtnExp[$key]['log_desc'] = $val['log_desc'];
                $rtnExp[$key]['balance'] = $val['balance'];
                $rtnExp[$key]['action'] = $val['action'];
                $rtnExp[$key]['trxId'] = $val['trxId'];
                if($val['action'] == 0){
                    $rtnExp[$key]['in'] = "-----";
                    $rtnExp[$key]['out'] = $val['money'];
                    $rtnExp[$key]['exp_using'] = $val['exp_using'];
                }else if($val['action'] == 1){
                    $rtnExp[$key]['in'] = $val['money'];
                    $rtnExp[$key]['out'] = "-----"; 
                    $rtnExp[$key]['exp_using'] = 0;
                }else if($val['action'] == 2){
                    $rtnExp[$key]['in'] = "-----";
                    $rtnExp[$key]['out'] = "-----";
                    $rtnExp[$key]['exp_using'] = $val['exp_using'];
                }
            }
            $userexpmoney = $this->user_expproduct->countExpmoney($uid);
            $expmoney = $this->expmoney->getCanExpmoney($uid);
            $data['count_expmoney'] = $expmoney[0]['expmoney']+$userexpmoney[0]['sum(money)'];
            $data['list'] = $rtnExp;
            $data['uid'] = $uid;
            $count = count($this->expmoney_log->getExpmoneLogyByUid(array('uid'=>$uid),'ctime desc',''));
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
            }else{
                $data['list'] = $data['page'] = '';
            }
            $this->load->view('/userinfomanage/v_oneExpmoneyLog',$data);
        }
    }
    public function getHuodongshouyi($uid=''){
    	 $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'关于公司信息管理');
        }else{
               $list=array(
                   'uid'=>$uid
               );
               $this->load->model('admin_account_model', 'accounthq');
               $this->load->model('admin_luckybag_model', 'admin_luckybag_model');
               $this->load->model('admin_duihuan_model', 'admin_duihuan_model');
               $phone = $this->accounthq->getAccountByUid($uid);       
               $this->load->model('admin_exp_profit_log_model','tymoney');
               $tymoney=$this->tymoney->getCountExpProfit($uid);//获取体验金
               if(empty($tymoney[0]['sum(profit)'])){
               		$list['tymoney']=0;
               }else{
               		$list['tymoney']=$tymoney[0]['sum(profit)'];
               }
               $fanxin_money = $this->user_log->sumFanxin($uid);  //返现
               if(empty($fanxin_money)){
               		$list['fanxin_money']=0;
               }else{
               		$list['fanxin_money']=$fanxin_money;
               }
               $invite_money = $this->user_log->suminvite($uid);
               if(empty($invite_money)){
               		$list['invite_money']=0;
               }else{
               		$list['invite_money']=$invite_money;
               }
               $luckyBag=$this->admin_luckybag_model->sumLuckyBagByUid($uid);  //  根据电话红包查询
               if(empty($redbag)){
               		$list['luckyBag']=0;
               }else{
               		$list['luckyBag']=$luckyBag;
               }
               $redBag=$this->redbag->sumRedbagByUid($phone['account']);  //  根据电话红包查询
               if(empty($redBag)){
               		$list['redBag']=0;
               }else{
               		$list['redBag']=$redBag;
               }
               $coupon=$this->user_coupon->sumCouponMoneyByUid($uid); //抵用券收益
               if(empty($coupon)){
               		$list['coupon']=0;
               }else{
               		$list['coupon']=$coupon;
               }
               $duihuan=$this->admin_duihuan_model->sumDuihuanByUid($uid); //兑换收益
               if(empty($duihuan)){
               	$list['duihuan']=0;
               }else{
               	$list['duihuan']=$duihuan;
               }
               $list['account'] = $phone['account'];
               $list['total'] = $list['tymoney']+$list['fanxin_money']+$list['invite_money']+$list['luckyBag']+$list['redBag']+$list['coupon'];
			   return $list;
        } 
    }
    public function getlistactive($uid){
    	$flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'关于公司信息管理');
    	}else{
    		$list=array();
    		
    		$list = $this->getHuodongshouyi($uid);//这个方法返回的是一个数组    $list=array(); return $list;
    		
            $this->load->view('/userinfomanage/v_huodongshouyi',$list);
    	}
    } 
    
    public function getUserLogDetailByType(){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'定期用户购买记录');
        }else{
            $type = $this->uri->segment(3);
            $uid = $this->uri->segment(4);
            $userLogList = array();
            if($type==1){
                $userLogList = $this->user_log->getInviteUserlogList($uid);
            }else{
                $userLogList = $this->user_log->getFanxinUserlogList($uid);
            }
            
            $useridentity = $this->useridentity->getUseridentityByUid($uid);
            if(!empty($useridentity)){
                $data['phone']=$useridentity['phone'];
            }
            $count= count($userLogList);
            $data = array();
            if(!empty($userLogList)){
                $in_array = array(0, 4, 14, 13, 5, 6, 7);
                $out_array = array(1, 11, 2);
                
                foreach ($userLogList as $key=>$val){
                    if(in_array($val['action'], $in_array)){
                        //收入
                        $userLogList[$key]['in'] = $val['money'];
                        $userLogList[$key]['out'] = "--";
                    }else if(in_array($val['action'], $out_array)){
                        //支出
                        $userLogList[$key]['out'] = $val['money'];
                        $userLogList[$key]['in'] = "--";
                    }else{
                        //支出
                        $userLogList[$key]['out'] = $val['money'];
                        $userLogList[$key]['in'] = "--";
                    }
                }
                $data['list'] = $userLogList;
            }
           $data['pageNum']    = 1;
           $data['numPerPage'] = $count; 
           $data['type']=1;
        }
        $this->load->view('/userinfomanage/user_log',$data);
    }
}