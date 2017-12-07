<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class download extends Controller {

    public function __construct()
    {
        parent::__construct();
        
    }

    public function index(){
    	$this->load->model('base/download_base', 'download_base');
    	$ip = $this->getIP();
    	$qudao = trim($this->input->get('qudao'));  
    	$data['ip'] = $ip;
    	$data['qudao'] = $qudao;
    	$data['ctime'] = NOW;
    	$this->download_base->addDownload($data);
    	if($qudao && $qudao == 'jrtt'){
	    	Header("Location: http://static1.cmibank.com/apk/jrtt/cmibank_jrtt.apk");
    	}else if($qudao && $qudao == 'jrtt2'){
	    	Header("Location: http://static1.cmibank.com/apk/jrtt/cmibank_jrtt2.apk");
    	}else if($qudao && $qudao == 'jrtt3'){
	    	Header("Location: http://static1.cmibank.com/apk/jrtt/cmibank_jrtt3.apk");
    	}else if($qudao && $qudao == 'jrtt4'){
	    	Header("Location: http://static1.cmibank.com/apk/jrtt/cmibank_jrtt4.apk");
    	}else if($qudao && $qudao == 'jrtt5'){
	    	Header("Location: http://static1.cmibank.com/apk/jrtt/cmibank_jrtt5.apk");
    	}else if($qudao && $qudao == 'uc'){
	    	Header("Location: http://static1.cmibank.com/apk/uc/cmibank.apk");
    	}else if($qudao && $qudao == 'uc1'){
	    	Header("Location: http://static1.cmibank.com/apk/uc/cmibank-uc1.apk");
    	}else if($qudao && $qudao == 'wy1'){
	    	Header("Location: http://static1.cmibank.com/apk/wy/cmibank_wy1.apk");
    	}else if($qudao && $qudao == 'wy2'){
	    	Header("Location: http://static1.cmibank.com/apk/wy/cmibank_wy2.apk");
    	}else if($qudao && $qudao == 'wy3'){
	    	Header("Location: http://static1.cmibank.com/apk/wy/cmibank_wy3.apk");
    	}else if($qudao && $qudao == 'wy4'){
	    	Header("Location: http://static1.cmibank.com/apk/wy/cmibank_wy4.apk");
    	}else if($qudao && $qudao == 'wy5'){
	    	Header("Location: http://static1.cmibank.com/apk/wy/cmibank_wy5.apk");
    	}else if($qudao && $qudao == 'dftt'){
	    	Header("Location: http://static1.cmibank.com/apk/dftt/cmibank_dftt.apk");
    	}else if($qudao && $qudao == 'baidu'){
	    	Header("Location: http://static1.cmibank.com/apk/baidu/cmibank_baidu.apk");
    	}else if($qudao && $qudao == 'hsp'){
	    	Header("Location: http://static1.cmibank.com/apk/hsp/cmibank_hsp.apk");
    	}else if($qudao && $qudao == 'kh'){
	    	Header("Location: http://static1.cmibank.com/apk/kh/cmibank_kh.apk");
    	}else if($qudao && $qudao == 'ip'){
	    	Header("Location: http://static1.cmibank.com/apk/ip/cmibank_ip.apk");
    	}else if($qudao && $qudao == 'ldw'){
	    	Header("Location: http://static1.cmibank.com/apk/ldw/cmibank_ldw.apk");
    	}else if($qudao && $qudao == 'yunos'){
	    	Header("Location: http://static1.cmibank.com/apk/yunos/cmibank_yunos.apk");
    	}else if($qudao && $qudao == 'db'){
	    	Header("Location: http://static1.cmibank.com/apk/cmibank_db.apk");
    	}else if($qudao && $qudao == 'fh'){
	    	Header("Location: http://static1.cmibank.com/apk/cmibank_fh.apk");
    	}else if($qudao && $qudao == 'liebao'){
	    	Header("Location: http://static1.cmibank.com/apk/cmibank_liebao.apk");
    	}else if($qudao && $qudao == 'liebao1'){
	    	Header("Location: http://static1.cmibank.com/apk/cmibank_liebao1.apk");
    	}else if($qudao && $qudao == 'liebao2'){
	    	Header("Location: http://static1.cmibank.com/apk/cmibank_liebao2.apk");
    	}else if($qudao && $qudao == 'hbsp'){
	    	Header("Location: http://static1.cmibank.com/apk/cmibank-hbsp.apk");
    	}else{
    		Header("Location: http://static1.cmibank.com/apk/cmibank.apk");
    	}
    }
    
    public function check(){
    	$qudao = $this->uri->segment(3);
    	$data['qudao'] = $qudao;
    	$this->load->view('download', $data);
    }
    public function regonline(){
    	$qudao = $this->uri->segment(3);
    	$data['qudao'] = $qudao;
    	$this->load->view('reg', $data);
    }
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */