<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class crontab extends Controller {

    public function __construct()
    {
        parent::__construct();
        if(!$this->input->is_cli_request()){
            die('only cli model can run!');
        }
    }
    
    public function baofooReturn(){
    	$this->load->model('base/pay_redis_base', 'pay_redis_base');
    	$lock_ret = $this->pay_redis_base->addredislock('baofooReturn', 1800);
    	if(!$lock_ret){
    		$response = array('error' => 111111, 'msg' => '脚本正在运行！');
    		$this->out_print($response, 'json', false);
    	}
    	
    	$this->load->model('base/withdraw_log_base', 'withdraw_log');
    	$this->load->model('logic/baofoopay_logic', 'baofoopay_logic');
        $this->load->model('logic/user_identity_logic', 'user_identity_logic');
        $this->load->model('base/user_log_base', 'user_log_base');
        $this->load->model('base/withdraw_failed_log_base', 'withdraw_failed_log_base');
    	$withdrawList = $this->withdraw_log->getBaofooRequest();
    	echo '{'.date("YmdHis").'|total:'.count($withdrawList).'|';
    	if(!empty($withdrawList)){
    		foreach ($withdrawList as $withdraw){
    			$orderid = $withdraw['orderid'];
    			echo $orderid.'--';
    			$times = $this->pay_redis_base->incrweehourstimes('withdraw'.$orderid);
    			if($times > 1){
    				echo '||' . $orderid . '.repeat!||';
    				continue;
    			}
		    	$return_data = $this->baofoopay_logic->query_withDraw_status($orderid);
		    	$success=true;
		    	$return_code = $return_data['data']['trans_content']['trans_head']['return_code'];
		    	$return_state =  5;
		    	$return_remark = '';
		    	$ybdrawflowid = '';
		    	if($return_code=='0000'){
		    		if(!empty($return_data['data']['trans_content']['trans_reqDatas'])){
		    			$return_state = $return_data['data']['trans_content']['trans_reqDatas'][0]['trans_reqData']['state'];
		    			$return_remark = $return_data['data']['trans_content']['trans_reqDatas'][0]['trans_reqData']['trans_remark'];
		    			$ybdrawflowid = $return_data['data']['trans_content']['trans_reqDatas'][0]['trans_reqData']['trans_orderid'];
		    			if($return_state==1){
		    				$pname = '提现成功';
			    			$action_type = USER_ACTION_PCASHOUT;
		    				$status = 2;
		    				$back_status = 'SUCCESS';
		    			}else if($return_state==-1){
				            $success = false;
				        }else{
				        	continue;
				        }
		    		}
		    	}else if($return_code!='0300' && $return_code!='0401' && $return_code!='0999'){
		    		$success = false;
		    		$return_state = $return_code;
		    		$return_remark = $return_data['data']['trans_content']['trans_head']['return_msg'];
		    	}else{
		    		continue;
		    	}
		    	if(!$success){
		    		$pname = '提现失败(' . $return_remark . ',将于次日17点之回到账户)';
	    			$status = 1;
		    		$back_status = 'BF_FAILED';
		    		$this->config->load('cfg/banklist', true, true);
		    		$banklist = $this->config->item('cfg/banklist');
		    		$action_type = USER_ACTION_WITHDRAWFAILED;
		    		$identity_result = $this->user_identity_logic->getPublicUserIdentity($withdraw['uid'], 'all');
		    		$id_num = strtoupper($identity_result['idCard']);
		    		
		    		$id_name = $identity_result['realname'];
		    		$bank_code = $identity_result['bankcode'];
		    		
		    		$bank_name = $banklist[$bank_code]['name'];
		    		$account_no = $identity_result['cardno'];
		    		$faild_log_data = array(
		    				'uid' => $withdraw['uid'],
		    				'orderid' => $orderid,
		    				'money' => $withdraw['money'],
		    				'realname' => $id_name,
		    				'bankname' => $bank_name,
		    				'bankcode' => $bank_code,
		    				'cardNo' => $account_no,
		    				'back_code' => $return_state,
		    				'back_msg' =>  $return_remark,
		    				'logid' => $withdraw['logid'],
		    				'plat' => 'baofoo',
		    				'ctime' =>NOW
		    		);
		    		$failedInfo = $this->withdraw_failed_log_base->getFailedLogByOrderId($orderid);
		    		if(empty($failedInfo)){
		    			$this->withdraw_failed_log_base->addFailedLog($faild_log_data);
		    		}
		    	}
		        $data = array('back_status' => $back_status, 'status' => $status, 'succtime' => time(), 'status_code' => $return_state,'ybdrawflowid' =>$ybdrawflowid);
		        $where = array('id' => $withdraw['id']);
		        $ret = $this->withdraw_log->updateDrawLog($data, $where, '', '');
		        if($ret){
		        	$update_data = array('orderid' => $withdraw['orderid'], 'pname' => $pname, 'paytime' => time(), 'action' => $action_type);
		        
		        	$isfind = strpos($withdraw['logid'], ',');
		        	if($isfind){
		        		$update_logid = explode(',', $withdraw['logid']);
		        	}else{
		        		$update_logid = $withdraw['logid'];
		        	}
		        
		        	if(is_array($update_logid)){
		        		foreach ($update_logid as $userlogid){
		        			$this->user_log_base->updateUserLogByIdForWithdrawNotify($withdraw['uid'],$userlogid, $update_data,$success);
		        		}
		        	}else{
		        		$update_where = array('id' => $update_logid);
		        		$ret = $this->user_log_base->updateUserLogByIdForWithdrawNotify($withdraw['uid'],$update_logid, $update_data,$success);
		        	}
		        }
    		}
    	}
    	
    	echo '|-OK}';
    	$this->pay_redis_base->delredislock('baofooReturn');
    }
    
    public function new_repayment(){
        $this->load->model('base/product_base', 'product_base');
        $this->load->model('base/product_repayment_log_base', 'repayment_log');
        $this->load->model('base/balance_base', 'balance_base');
        $this->load->model('base/userproduct_base', 'userproduct_base');
        $this->load->model('base/product_buy_info_base', 'product_buy_info_base');
        $this->load->model('base/user_notice_base', 'user_notice_base');
        $pids = array();
        $repayment_logs = $this->repayment_log->getLogsWithStatus(1);
        
        $this->product_base->deleteRePayMentList();     //请除3点半之前的预还款信息
        foreach ($repayment_logs as $_log){
            $_uid = $_log['uid'];
            $pid = $_log['pid'];
            if(!in_array($pid, $pids)){
                $pids[] = $pid;
            }
            $_product = $this->product_base->getProductDetail($pid);
            $update_data = array('repaytime' => time(), 'status' => 1);
            $update_where = array('pid' => $pid, 'uid' => $_uid);
            //更新用户产品状态
            $this->userproduct_base->updateUserProductStatus($update_data, $update_where);
            $money = $_log['money'] + $_log['profit'];
            //添加余额
            $ret = $this->balance_base->add_user_balance($_log['uid'], $money);
            if(!$ret){
                $err_uid[$_uid] = array('uid' => $_uid , 'money' => $money);
            }
            //用户回款日志
            $balance = $this->balance_base->get_user_balance($_uid);
            $orderid = date("YmdHis") . $_uid . "bm";
            $user_log_data = array(
                'uid' => $_uid,
                'pid' => $pid,
                'pname' => $_product['pname'],
                'orderid' => $orderid,
                'money' => $money,
                'balance' => $balance,
                'action' => USER_ACTION_PREPAYMENT
            );
            $this->load->model('base/user_log_base', 'user_log_base');
            $this->user_log_base->addUserLog($_uid, $user_log_data);
            
            //短信
            $this->load->model('logic/crontab_logic', 'crontab_logic');
            $this->load->model('base/user_identity_base', 'user_identity_base');
            $userIdentity = $this->user_identity_base->getUserIdentity($_uid);
            $this->load->model('logic/msm_logic', 'msm_logic');
            if($money > 100){
            	if(!empty($_log['tiqian'])){
            		$this->msm_logic->send_tiqian_repayment_msg($_uid, $userIdentity['realname'], $_product['pname'], $money, $userIdentity['phone']);
            	}else{
                 	$this->msm_logic->send_repayment_msg($_uid, $userIdentity['realname'], $_product['pname'], $money, $userIdentity['phone']);
            	}
            }
            
            //更新buyProductInfo
            $data = array('b_time' => time(), 'b_trxid' => $orderid);
            $where = array('pid' => $pid, 'uid' => $_uid);
            $this->product_buy_info_base->updateProductBuyInfo($data, $where);
            $log_data = array('status' => 2);
            $log_where = array('uid' => $_uid, 'pid' => $pid);
            $this->repayment_log->updateLogs($log_data, $log_where);
            $this->load->model('base/userproduct_base', 'userproduct_base');
            $this->userproduct_base->moveUserSumProductMoney($_uid);
            $notice_data = array(
            		'uid' => $_uid,
            		'title' => '还款成功提醒',
            		'content' => "您投资的【".$_product['pname']."】回款共计".$money."元，现已转入你的余额账户，请查收！",
            		'ctime' => NOW
            );
            $this->user_notice_base->addNotice($_uid,$notice_data);
        }
        foreach ($pids as $pid){
            //把产品标为已还款状态
            $data = array('status' => 6, 'repaytime' => time(), 'repayment_status' => 2);        //回款
            $this->product_base->updateProductStatus($pid, $data);
            //添加产品到还款队列
            $this->product_base->addProductToRePayMentList($pid);
        }
        echo 'OK';
    }
    
    
    public function todayRepaymentProduct(){
        $this->load->model('base/product_base', 'product_base');
        $this->product_base->deleteRePayMentList();     //请除3点半之前的预还款信息
        
        $repayment_productList = $this->product_base->getProductListWithStatus('2, 3, 4, 5', date("Y-m-d", strtotime('-1 day')));
        foreach ($repayment_productList as $product){
           $this->product_base->addProductTotoDayRealdyRePayMentList($product);
        }
        echo 'OK';
    }
    
    //还款查询
//     public function repayment(){
//         exit;
//         $this->load->model('base/product_base', 'product_base');
//         $this->load->model('base/userproduct_base', 'userproduct_base');
//         $this->load->model('base/balance_base', 'balance_base');
//         $this->load->model('base/product_buy_info_base', 'product_buy_info_base');
//         $repayment_productList = $this->product_base->getProductListWithStatus('2, 3, 4, 5', date("Y-m-d", strtotime('-1 day')));
//         $repayment_log = array();
//         foreach ($repayment_productList as $_product){
//             $err_uid = array();
//             $pid = $_product['pid'];
//             $data = array('status' => 6, 'repaytime' => time(), 'repayment_status' => 2);        //回款
//             $this->product_base->updateProductStatus($pid, $data);
//             $buy_info = $this->product_buy_info_base->getBuyUserByPid($pid);
//             if(empty($buy_info)){//无购买用户 的产品  到期了还是设置为回款
//                 $data = array('status' => 7, 'repaytime' => time());        //回款
//                 $this->product_base->updateProductStatus($pid, $data);
//                 continue;
//             }
//             $days = ((strtotime($_product['uietime']) - strtotime($_product['uistime']))/ 86400)  + 1;
//             $income = $_product['income'] / 360 / 100;
//             $repayment_list = array();
//             //把单个产品的用户合到一块
//             foreach ($buy_info as $_uinfo){
//                 $profit = $days * $income * $_uinfo['money'];
//                 $profit = sprintf("%.2f",substr(sprintf("%.3f", $profit), 0, -1));
//                 if(!isset($repayment_list[$_uinfo['uid']])){
//                     $repayment_list[$_uinfo['uid']]['money'] = 0;
//                     $repayment_list[$_uinfo['uid']]['profit'] = 0;
//                     $repayment_list[$_uinfo['uid']]['num'] = 0;
//                 }
//                 $repayment_list[$_uinfo['uid']]['money'] += $_uinfo['money'];
//                 $repayment_list[$_uinfo['uid']]['profit'] += $profit;
//                 $repayment_list[$_uinfo['uid']]['num']++;
//             }
//             //记账数组
//             foreach($repayment_list as $_uid => $_repayment){
//                 $update_data = array('repaytime' => time(), 'status' => 1);
//                 $update_where = array('pid' => $pid, 'uid' => $_uid);
//                 //更新用户产品状态
//                 $this->userproduct_base->updateUserProductStatus($update_data, $update_where);
//                 $money = $_repayment['money'] + $_repayment['profit'];
//                 //添加余额 
//                 $ret = $this->balance_base->add_user_balance($_uid, $money);
//                 if(!$ret){
//                     $err_uid[$_uid] = array('uid' => $_uid , 'money' => $money);
//                 }
//                 //用户回款日志
//                 $balance = $this->balance_base->get_user_balance($_uid);
//                 $orderid = date("YmdHis") . $_uid . "bm";
//                 $user_log_data = array(
//                     'uid' => $_uid,
//                     'pid' => $pid,
//                     'pname' => $_product['pname'],
//                     'orderid' => $orderid,
//                     'money' => $money,
//                     'balance' => $balance,
//                     'action' => USER_ACTION_PREPAYMENT
//                 );
//                 $this->load->model('base/user_log_base', 'user_log_base');
//                 $this->user_log_base->addUserLog($_uid, $user_log_data);
                
//                 //短信
//                 $this->load->model('logic/crontab_logic', 'crontab_logic');
//                 $this->load->model('base/user_identity_base', 'user_identity_base');
//                 $userIdentity = $this->user_identity_base->getUserIdentity($_uid);
//                 $this->load->model('logic/msm_logic', 'msm_logic');
//                 $msg = $this->msm_logic->send_repayment_msg($_uid, $userIdentity['realname'], $_product['pname'], $money, $userIdentity['phone']);
                
//                 //更新buyProductInfo
//                 $data = array('b_time' => time(), 'b_trxid' => $orderid);
//                 $where = array('pid' => $pid, 'uid' => $_uid);
//                 $this->product_buy_info_base->updateProductBuyInfo($data, $where);
                
//                 //统计每个用户的账 A1 先放到一个大数组中保存。  A2统一入库
//                 $repayment_log[] = array('uid' => $_uid, 'pid' => $pid, 'pname' => $_product['pname'], 'money' => $_repayment['money'], 'income' => $_product['income'], 'profit' => $_repayment['profit'], 'days' => $days, 'ctime' => time());
//             }
//             //把产品标为已还款状态
//             $data = array('status' => 6, 'repaytime' => time(), 'repayment_status' => 2);        //回款
//             $this->product_base->updateProductStatus($pid, $data);
//             //添加产品到还款队列
//             $this->product_base->addProductToRePayMentList($pid);
//         }
//         //脚本跑完把每个用户的账入库    A2
//         $this->load->model('base/product_repayment_log_base', 'product_repayment_log_base');
//         foreach ($repayment_log as $_rp_log){
//             $this->product_repayment_log_base->createLog($_rp_log);
//         }
//         echo "OK";
//     }
    
    
    public function queryLastWeekWithDraw(){
        $data_y = date('Y', strtotime("-7 day"));
        $data_w = date('W', strtotime("-7 day"));
//         echo $data_w;
        $this->doqueryWithDraw($data_y, $data_w);
    }
    
    
    public function queryWithDraw(){
        $data_y = date('Y');
        $data_w = date('W');
        $this->doqueryWithDraw($data_y, $data_w);
    }
    
    private function doqueryWithDraw($data_y, $data_w){
        $this->load->model('base/withdraw_log_base', 'withdraw_log_base');
        $withDrawList = $this->withdraw_log_base->getDrawLogTableList(array('status' => 0, 'plat' => 'yee'), $data_y, $data_w);
        //print_r($withDrawList);
        $this->load->model('logic/yeepay_logic', 'yeepay_logic');
        $this->load->model('base/user_log_base', 'user_log_base');
        if(empty($withDrawList)){
            $msg = array();
            $msg['date'] = date("Y-m-d H:i:s",time());
            $msg['type'] = 'queryWithDrawNull';
            $this->crontab_run(json_encode($msg));
            echo 'OK';
            exit;
        }
        foreach ($withDrawList as $_draw){
            $orderid = $_draw['orderid'];
            $ybdrawflowid = $_draw['ybdrawflowid'];
            $yee_data = $this->yeepay_logic->withdrawQuery($orderid, $ybdrawflowid);

            //             DOING：处理中
            //             FAILURE：提现失败
            //             REFUND：提现退回
            //             SUCCESS：提现成功
            //             UNKNOW：未知
            if(!isset($yee_data['status'])){
                continue;
            }
            if($yee_data['status'] == 'DOING' || $yee_data['status'] == 'UNKNOW'){
                $this->crontab_run(json_encode($yee_data));
                continue;
            }
            echo '|'.json_encode($yee_data). '|';
            if($yee_data['status'] == 'FAILURE'
                || $yee_data['status'] == 'REFUND'
                || $yee_data['status'] == 'SUCCESS')
            {
                $status = 1;
                $pname = '提现退回';
                if($yee_data['status'] == 'SUCCESS'){
                    $pname = '提现成功';
                    $status = 2;
                }else if($yee_data['status'] == 'FAILURE'){
                    $pname = '提现失败';
                }
                $data = array('back_status' => $yee_data['status'], 'status' => $status, 'succtime' => time());
                $where = array('id' => $_draw['id']);
                $ret = $this->withdraw_log_base->updateDrawLog($data, $where, $data_y, $data_w);
                if($ret){
                    $update_data = array('orderid' => $_draw['orderid'], 'pname' => $pname, 'paytime' => time());
                    $update_where = array('id' => $_draw['logid']);
                    $ret = $this->user_log_base->updateUserLogOnlyWithDraw($_draw['uid'], array('all', 'out'), $update_data, $update_where);
                }
            }
        }
        $msg = array();
        $msg['date'] = date("Y-m-d H:i:s",time());
        $msg['type'] = 'queryWithDraw';
        $this->crontab_run(json_encode($msg));
        echo 'OK';
    }
    
    public function productdownline(){
        $this->load->model('base/ptype_base', 'ptype_base');
        $this->load->model('base/product_base', 'product_base');
        $this->load->model('logic/crontab_logic', 'crontab_logic');
        $this->load->model('base/ptype_product_base', 'ptype_product_base');
        $ptypeList = $this->ptype_base->getPtypeList();
        $odate = date('Y-m-d');
//        $odate = date('2015-07-15');
        foreach ($ptypeList as $ptid => $value){
            $productList = $this->ptype_product_base->getPtypeProductByPtid($ptid, $odate);
//             echo $ptid;
//             print_r($productList);
            
            if(empty($productList)){
                continue;
            }
//             echo "<br />===================<br />";
//             print_r($productList);
            foreach($productList as $_product){
                if($_product['status'] != 0){
                    continue;
                }
                //echo $_product['pid'];
                //更新状态 防BUG
                $this->product_base->updateProductStatusWithSql($_product['pid']);
                $this->crontab_logic->setProductDown($_product['pid']);
                $this->product_base->addProductToSellOutList($_product['pid']);
            }
            //删除备份数据
            $this->ptype_product_base->deletePtypeProduct($ptid, $odate);
            $this->product_base->moveOnlineProductByPtid($ptid, $odate);
            $this->product_base->moveYugaoProductByPtid($ptid, $odate);
        }
        echo 'ok';
    }
    
    public function longproductdownline(){
        $msg = array();
        $msg['name'] = 'longproduct';
        $msg['date'] = date("Y-m-d H:i:s",time());
        $msg['step'] = 'longproductdownline_start';
        $this->crontab_run(json_encode($msg));
        $this->load->model('base/ltype_longproduct_base', 'ltype_longproduct_base');
        $odate = date('Y-m-d');
        //$odate = date('2015-07-16');
        $this->ltype_longproduct_base->deleteLtypeLongProductByOdate($odate);
        $this->load->model('base/longproduct_base', 'longproduct_base');
        
        $longproductlist = $this->longproduct_base->getOnlineLongProductListAllMem(LONGPRODUCT_PTID, $odate);
        foreach ($longproductlist as $_lp){
            $this->longproduct_base->updateLongProductStatus($_lp, array('status' => 2));
            $this->longproduct_base->addLongProductToSellOutList($_lp);
        }
        $this->longproduct_base->delOnlineLongProductList(LONGPRODUCT_PTID, $odate);
        $msg['name'] = 'longproduct';
        $msg['date'] = date("Y-m-d H:i:s",time());
        $msg['step'] = 'longproductdownline_end';
        $this->crontab_run(json_encode($msg));
        echo 'ok';
    }
    
    public function klproductdownline(){
        $msg = array();
        $msg['name'] = 'klproduct';
        $msg['date'] = date("Y-m-d H:i:s",time());
        $msg['step'] = 'klproductdownline_start';
        $this->crontab_run(json_encode($msg));
        $this->load->model('base/kltype_klproduct_base', 'kltype_klproduct_base');
        $odate = date('Y-m-d');
        //$odate = date('2015-07-16');
        $this->kltype_klproduct_base->deleteKltypeKlProductByOdate($odate);
        $this->load->model('base/klproduct_base', 'klproduct_base');
    
        $klproductlist = $this->klproduct_base->getOnlineKlProductListAllMem(LONGPRODUCT_PTID, $odate);
        foreach ($klproductlist as $_klp){
            $this->klproduct_base->updateKlProductStatus($_klp, array('status' => 2));
            $this->klproduct_base->addKlProductToSellOutList($_klp);
        }
        $this->klproduct_base->delOnlineKlProductList(KLPRODUCT_PTID, $odate);
        $msg['name'] = 'klproduct';
        $msg['date'] = date("Y-m-d H:i:s",time());
        $msg['step'] = 'klproductdownline_end';
        $this->crontab_run(json_encode($msg));
        echo 'ok';
    }
    
    //计算每日用户活期利息
    public function countKlProductProfit(){
        $msg = array();
        $msg['date'] = date("Y-m-d H:i:s",time());
        $msg['type'] = 'klprofit_start';
        $this->crontab_run(json_encode($msg));
        $this->load->model('base/klmoney_base', 'klmoney_base');
        $todaytime = mktime(0,0,0);
        $psize = 30;
        $count = $this->klmoney_base->countKlmoney($todaytime);
        $page_count = 0 ;
        $max_page = ceil($count/$psize);
        $runcount = 0;
        $this->load->model('base/klproductcontract_base', 'klproductcontract_base');
        $this->load->model('base/uklp_profit_log_base', 'uklp_profit_log_base');
        $this->load->model('base/userklproduct_base', 'userklproduct_base');
        $klcontract = $this->klproductcontract_base->getContractByCid(KLPRODUCT_CID);
        $income = $klcontract['income'];
        $change_uids = array();
        $count_chongfu = 0;
        $remove_num = 0;
        for($page = 1; $page <= $max_page; $page++){
            $offset = ($page - 1) * $psize;
            $data = $this->klmoney_base->getklmoneyList($offset, $psize);
            $page_count += $psize;
            //处理业务逻辑
            foreach ($data as $user_klmoney){
                if($user_klmoney['counttime'] == $todaytime){
                    $count_chongfu++;
                    continue;
                }
                $remove_money = 0;
                if($user_klmoney['updatetime'] > $todaytime){
                    $remove_money = $this->userklproduct_base->count_klproduct_money($user_klmoney['uid'], mktime(0,0,0));
                }
                $count_money = $user_klmoney['money'] - $remove_money;
                if($count_money == 0){
                    $remove_num++;
                    continue;
                }
                $profit = round($count_money * $income / 360 / 100, 2);
                $klmoney = $user_klmoney['money'] + $profit;
                
                //取用户小活期信息，如果这个时间数据有变动，则不更新
                $u_kl_money = $this->klmoney_base->getUserKlMoney($user_klmoney['uid']);
                if($u_kl_money != $user_klmoney['money']){
                    $change_uids[] = $user_klmoney['uid'];
                    continue;
                }
                //用户结算日志
                $uklp_data = array();
                $uklp_data['uid'] = $user_klmoney['uid'];
                $uklp_data['profit'] = $profit;
                $uklp_data['f_klmoney'] = $user_klmoney['money'];    //计算利息前
                $uklp_data['b_klmoney'] = $klmoney;                  //计算利息后
                $uklp_data['time'] = $todaytime;
                $this->uklp_profit_log_base->add_uklp_profit_log($user_klmoney['uid'], $uklp_data);
                //更新用户活期金额信息
                $updateInfo = array();
                $updateInfo['money'] = $klmoney;
                $updateInfo['counttime'] = $todaytime;
                $where = array();
                $where['uid'] = $user_klmoney['uid'];
                $this->klmoney_base->updateUserKlmoney($updateInfo, $where);
                $runcount++;
            }
            if($page_count % 2000 == 0){
                sleep(1);
            }
        }
        $this->load->model('base/klmoney_income_log', 'klmoney_income_log');
        $data = $this->klmoney_income_log->add($income, $todaytime);
        $msg = array();
        $msg['date'] = date("Y-m-d H:i:s",time());
        $msg['type'] = 'klprofit_end';
        $msg['changeUids'] = $change_uids;
        $msg['count_chongfu'] = $count_chongfu;
        $msg['remove_num'] = $remove_num;
        $msg['countnum'] = $count;
        $msg['runnum'] = $runcount;
        $this->crontab_run(json_encode($msg));
        echo 'OK';
    }
    
    //计算每日用户活期利息
    public function countLongProductProfit(){
        
        $msg = array();
        $msg['date'] = date("Y-m-d H:i:s",time());
        $msg['type'] = 'longprofit_start';
        $this->crontab_run(json_encode($msg));
        $this->load->model('base/longmoney_base', 'longmoney_base');
        $todaytime = mktime(0,0,0);
        $psize = 30;
        $count = $this->longmoney_base->countLongmoney();
        if($count == 0){
            $this->crontab_run(json_encode(array('date' => date("Y-m-d H:i:s",time()),'msg' => '没有用户投资活期，停止计算')));
            exit('ok');
        }
        $max_page = ceil($count/$psize);
        $runcount = 0;
        $this->load->model('base/longproductcontract_base', 'longproductcontract_base');
        $longcontract = $this->longproductcontract_base->getContractByCid(LONGPRODUCT_CID);
        $this->load->model('base/userlongproduct_base', 'userlongproduct_base');
        $this->load->model('base/ulp_profit_log_base', 'ulp_profit_log_base');
        $income = $longcontract['income'];
        $page_count = 0 ;
        $change_uids = array();
        $count_chongfu = 0;
        $remove_num = 0;
        for($page = 1; $page <= $max_page; $page++){
            $offset = ($page - 1) * $psize;
            $data = $this->longmoney_base->getLongmoneyList($offset, $psize);
            $page_count += $psize;
            //处理业务逻辑
            foreach ($data as $user_longmoney){
                if($user_longmoney['counttime'] == $todaytime){
                    $count_chongfu++;
                    continue;
                }
                $remove_money = 0;
                if($user_longmoney['updatetime'] > $todaytime){
                    $remove_money = $this->userlongproduct_base->count_longproduct_money($user_longmoney['uid'], mktime(0,0,0));
                }
                $count_money = $user_longmoney['money'] - $remove_money;
                if($count_money == 0){
                    $remove_num++;
                    continue;
                }
                $profit = round($count_money * $income / 365 / 100, 2);
                $longmoney = $user_longmoney['money'] + $profit;
                //用户结算日志
                $u_l_money = $this->longmoney_base->getUserLongMoney($user_longmoney['uid']);
                if($u_l_money != $user_longmoney['money']){
                    $change_uids[] = $user_longmoney['uid'];
                    continue;
                }
                $ulp_data = array();
                $ulp_data['uid'] = $user_longmoney['uid'];
                $ulp_data['profit'] = $profit;
                $ulp_data['f_longmoney'] = $user_longmoney['money'];    //计算利息前
                $ulp_data['b_longmoney'] = $longmoney;                  //计算利息后
                $ulp_data['time'] = $todaytime;
                $this->ulp_profit_log_base->add_ulp_profit_log($user_longmoney['uid'], $ulp_data);
                //更新用户活期金额信息
                $updateInfo = array();
                $updateInfo['money'] = $longmoney;
                $updateInfo['counttime'] = $todaytime;
                $where = array();
                $where['uid'] = $user_longmoney['uid'];
                $this->longmoney_base->updateUserLongmoneyWithCrontab($user_longmoney['uid'], $todaytime, $profit);
                $runcount++;
            }
            if($page_count % 2000 == 0){
                sleep(1);
            }
        }
        $this->load->model('base/longmoney_income_log', 'longmoney_income_log');
        $data = $this->longmoney_income_log->add($income, $todaytime);
        $msg = array();
        $msg['date'] = date("Y-m-d H:i:s",time());
        $msg['type'] = 'longprofit_end';
        $msg['changeUids'] = $change_uids;
        $msg['count_chongfu'] = $count_chongfu;
        $msg['remove_num'] = $remove_num;
        $msg['countnum'] = $count;
        $msg['runnum'] = $runcount;
        $this->crontab_run(json_encode($msg));
    }
    
    //定期每日利息
    public function countProductProfit(){
        $msg = array();
        $msg['name'] = 'countProductProfit';
        $msg['date'] = date("Y-m-d H:i:s",time());
        $msg['step'] = 'countProductProfit_start';
        $this->crontab_run(json_encode($msg));
        
        $odate = date('Y-m-d', strtotime("-1 day"));
        //取出所有末到期，已下架和已售馨的产品(2,3)
        $this->load->model('base/product_base', 'product_base');
        $this->load->model('base/userproduct_base', 'userproduct_base');
        $repayment_productList = $this->product_base->getProductListWithCountProfit($odate);

//         $re_a = array(100267);
//         foreach ($repayment_productList as $key => $v){
//             if(!in_array($v['pid'], $re_a)){
//                 echo $v['pid'];
//                 unset($repayment_productList[$key]);
//             }
//         }
        //开始计算每个产品当日利息
        foreach ($repayment_productList as $_product){
            //单个产品购买用户
            $this->load->model('base/product_buy_info_base', 'product_buy_info_base');
            $pid = $_product['pid'];
            $buy_info = $this->product_buy_info_base->getBuyUserByPid($pid);
            if(empty($buy_info)){
                continue;
            }
            //单日利息
            $income = $_product['income'] / 365 / 100;
            //从起息日到今日
            $days = ((strtotime($odate) - strtotime($_product['uistime']))/ 86400) + 1;
            //起息日到昨天
            $yesterday = $days - 1;
            foreach ($buy_info as $_uinfo){
                //到昨日的利息
                $yesterday_profit = $yesterday * $income * $_uinfo['money'];
                $yesterday_profit = sprintf("%.2f",substr(sprintf("%.3f", $yesterday_profit), 0, -1));
                //到今天的利息
                $today_profit = $days * $income * $_uinfo['money'];
                //今天一天的利息
                $one_day_profit = $today_profit - $yesterday_profit;                
                $one_day_profit = sprintf("%.2f",substr(sprintf("%.3f", $one_day_profit), 0, -1));
//                 print_r($_uinfo);
//                 echo '昨天：' .$yesterday_profit . '|';
//                 echo '今天：' .$today_profit . '|';
//                 echo '今天单天:' . $on_day;
//                 echo '<br />';
                //保存到利息表
                $this->load->model('base/up_profit_log_base', 'up_profit_log_base');
                $up_profit_data = array();
                $up_profit_data['uid'] = $_uinfo['uid'];
                $up_profit_data['pid'] = $_product['pid'];
                $up_profit_data['profit'] = $one_day_profit;
                $up_profit_data['trxid'] = $_uinfo['trxId'];
                $up_profit_data['money'] = $_uinfo['money'];
                $up_profit_data['odate'] = $odate;
                $up_profit_data['time'] = NOW;
                $this->up_profit_log_base->add_up_profit_log($_uinfo['uid'], $up_profit_data);
            }
        }
        $msg = array();
        $msg['name'] = 'countProductProfit';
        $msg['date'] = date("Y-m-d H:i:s",time());
        $msg['step'] = 'countProductProfit_end';
        $this->crontab_run(json_encode($msg));
        echo 'ok';
    }
    
    public function return_user_money(){
        $this->load->model('base/withdraw_failed_log_base', 'withdraw_failed_log_base');
//        $this->load->model('logic/jytpay_logic', 'jytpay_logic');
//        $this->load->model('logic/baofoopay_logic', 'baofoopay_logic');
        $this->load->model('logic/fuioupay_logic', 'fuioupay_logic');
        $failed_list = $this->withdraw_failed_log_base->getDrawFailedLogTableList(array('status' => 0));
        foreach ($failed_list as $_failed_order){
            if($_failed_order['back_code'] == '' || $_failed_order['back_msg'] == ''){
                continue;
            }
            //更新订单状态
            $this->withdraw_failed_log_base->updateDrawFailedLog(array('status' => 1,'utime' =>NOW), array('id' => $_failed_order['id']));
            //给用户加日志
            $uid = $_failed_order['uid'];
            $money = $_failed_order['money'];
            $orderid = $_failed_order['orderid'];
            if($_failed_order['plat']=='fuiou'){
                $stamp_now = time();
                $enddt = date("Ymd",$stamp_now);
                $startdt = date("Ymd",strtotime("-14 day", $stamp_now));
                $queryInfo = $this->fuioupay_logic->queryWithDrawStatus($orderid,$startdt,$enddt, '');
                if(isset($queryInfo['trans']['state']) && $queryInfo['trans']['state'] != 1){
                    $msg = array();
                    $msg['orderid'] = $orderid;
                    $msg['money'] = $_failed_order['money'];
                    $msg['uid'] = $_failed_order['uid'];
                    $this->no_failed_log(json_encode($msg));
                    echo $orderid . 'NO_FEAILED';
                    continue;
                }
            }else{
                echo $orderid . 'unkown plat';
	        continue;
            }
            
            /**多种提现平台情况**/
//            if($_failed_order['plat']=='jyt'){
//	            $queryInfo = $this->jytpay_logic->queryPayOrdid($orderid, 'withDraw');
//	            if((string)$queryInfo->head->resp_code == 'S0000000' && (string)$queryInfo->body->tran_state == '01'){
//	                $msg = array();
//	                $msg['orderid'] = $orderid;
//	                $msg['money'] = $_failed_order['money'];
//	                $msg['uid'] = $_failed_order['uid'];
//	                $this->no_failed_log(json_encode($msg));
//	                echo $orderid . 'NO_FEAILED';
//	                continue;
//	            }
//            }else{
//            	$return_data = $this->baofoopay_logic->query_withDraw_status($orderid);
//            	if(!empty($return_data) && $return_data['data']['trans_content']['trans_head']['return_code']=='0000'){
//            		if($return_data['data']['trans_content']['trans_reqDatas'][0]['trans_reqData']['state']==1){
//            			$msg = array();
//            			$msg['orderid'] = $orderid;
//            			$msg['money'] = $_failed_order['money'];
//            			$msg['uid'] = $_failed_order['uid'];
//            			$this->no_failed_log(json_encode($msg));
//            			echo $orderid . 'NO_FEAILED';
//            			continue;
//            		}
//            	}
//            }
            $this->load->model('base/balance_base', 'balance_base');
            //给用户加钱
            $this->balance_base->add_user_balance($uid, $money, true);
            $balance = $this->balance_base->get_user_balance($uid);
            $user_log_data = array(
                'uid' => $uid,
                'pid' => 0,
                'pname' => '提现资金退回',
                'orderid' => $orderid,
                'money' => $money,
                'balance' => $balance,
                'action' => USER_ACTION_WITHDRAWBACK
            );
            $this->load->model('base/user_log_base', 'user_log_base');
            $last_id = $this->user_log_base->addUserLog($uid, $user_log_data);
            
            $this->load->model('logic/cd_logic', 'cd_logic');
            $cd_data = $this->cd_logic->getUserCd($uid);
            $cd_data['free_withDraw']++;
            $this->cd_logic->setUserCd($uid, $cd_data);
        }
    }
    
//     public function buchang1212(){
//         exit;
//         $odate = date('Ymd');
//         $income = 12.12;
//         $this->load->model('base/ulp_profit_log_base', 'ulp_profit_log_base');
//         $this->load->model('base/longmoney_base', 'longmoney_base');
//         for($i = 0 ; $i < 16 ; $i++){
//             $ulp_list = $this->ulp_profit_log_base->get_ulplist_with_odate($i, strtotime($odate));
            
//             foreach ($ulp_list as $_ulp){
                
//                 $uid = $_ulp['uid'];
//                 $profit = round($_ulp['f_longmoney'] * $income / 360 / 100, 2);
//                 $diff_profit = (float)($profit - $_ulp['profit']);
//                 echo 'uid:' . $uid . '---profit:' . $diff_profit . '<br />';
//                 $this->longmoney_base->add_longmoney($uid, $diff_profit);
//                 $update_data = array();
//                 $update_data['profit'] = $profit;
//                 $update_data['b_longmoney'] = $_ulp['f_longmoney'] + $profit;
//                 $update_where = array();
//                 $update_where['id'] = $_ulp['id'];
//                 $update_where['uid'] = $_ulp['uid'];
//                 $this->ulp_profit_log_base->update_ulp_data_by_id($i, $update_data, $update_where);
//             }
            
//         }
        
//     }
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */