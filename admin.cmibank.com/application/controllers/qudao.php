<?php
class qudao extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '渠道统计') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_useridentity_model', 'useridentity');
        $this->load->model('admin_account_model','admin_account_model');
        $this->load->model('admin_userproduct_model','admin_userproduct_model');
        $this->load->model('admin_longproduct_model','admin_longproduct_model');
        $this->load->model('admin_longmoney_model','admin_longmoney_model');
    }
    
    
    public function index() {
        $flag = $this->op->checkUserAuthority('渠道统计', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '渠道统计');
        } else {
        	$searchparam = array();
        	$data=array();
        	$list = array();
        	$weibangka = 0;
        	$dingqi_total = 0;
        	$dingqi_counts = 0;
        	$dingqi_fugou = 0;
        	$huoqi = 0;
        	$huoqi_total = 0;
        	$huoqi_counts = 0;
        	if($this->input->request('op') == 'search'){
        		$type = trim($this->input->post('type'));
        		$stime = trim($this->input->post('stime'));
        		$etime = trim($this->input->post('etime'));
        		if(!empty($stime)){
        			$data['stime'] = $stime;
        			$stime = strtotime($stime);
        		}else {
        			$stime = strtotime(date('Y-m-d',strtotime('-30 day')));
        			$data['stime'] = date('Y-m-d',strtotime('-30 day'));
        		}
        		if(!empty($etime)){
        			$data['etime'] = $etime;
        			$etime = strtotime($etime)+86400;
        		}else{
        			$etime = NOW;
        			$data['etime'] = date('Y-m-d',NOW);
        		}
        		
        		$maxindex = $this->admin_account_model->countAccountListForCount($type,$stime,$etime);
        		$psize = 50;
        		for ($page=1;$page<=$maxindex/50;$page++){
        			$offset = ($page - 1) * $psize;
        			$accountList = $this->admin_account_model->getAccountListForCount($type,$stime,$etime,$offset,$psize);
        			if(!empty($accountList)){
        				foreach ($accountList as $account){
	        				if($account['plat']==$type){
	        					$user = $this->useridentity->getUseridentityByUid($account['uid']);
	        					if(!empty($user)){
	        						$total = $this->admin_userproduct_model->getAllMoney($account['uid']);
	        						$totalmoney = $total[0]['totalmoney']?$total[0]['totalmoney']:0;
	        						$totalcount = $total[0]['totalcount']?$total[0]['totalcount']:0;
	        						if($totalmoney>0){
	        							$dingqi_counts++;
	        							$dingqi_total = $dingqi_total+$totalmoney;
	        							if($totalcount>1){
	        								$dingqi_fugou++;
	        							}
	        						}
	        						$ltotal = $this->admin_longproduct_model->getAllMoney($account['uid']);
	        						$ltotalmoney = $ltotal[0]['totalmoney']?$ltotal[0]['totalmoney']:0;
	        						$ltotalcount = $ltotal[0]['totalcount']?$ltotal[0]['totalcount']:0;
	        						$longmoney = $this->admin_longmoney_model->getUserLongMoney($account['uid']);
	        						if($ltotalmoney>0){
	        							$huoqi_total = $huoqi_total+$ltotalmoney;
	        							$huoqi_counts++;
	        						}
	        						if($longmoney>0){
	        							$huoqi++;
	        						}
	        						$list[] = array(
	        								'uid' => $account['uid'],
	        								'realname' => $user['realname'],
	        								'phone' => $account['account'],
	        								'ctime'=>$account['ctime'],
	        								'productmoney' => $totalmoney,
	        								'count' =>$totalcount,
	        								'lproductmoney' => $ltotalmoney,
	        								'lcount' =>$ltotalcount,
	        								'longmoney' =>$longmoney
	        						);
	        					}else{
	        						$weibangka++;
	        					}
	        				}
        				}
        			}
        		}
        		$data['type']=$type;
        	}else{
        		$data['type']=1;
        	}
        	$data['total'] = $maxindex;
        	$data['weibangka']=$weibangka;
        	$data['dingqi_total']=$dingqi_total;
        	$data['dingqi_counts']=$dingqi_counts;
        	$data['dingqi_fugou']=$dingqi_fugou;
        	$data['huoqi']=$huoqi;
        	$data['huoqi_total']=$huoqi_total;
        	$data['huoqi_counts']=$huoqi_counts;
        	$data['list'] = $list;
            $this->load->view('/qudao/v_index', $data);
        }
    }
}