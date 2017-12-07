<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class homepage extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/homepage_logic', 'homepage_logic');
        $this->check_link();
    }

    public function servertime(){
        $response = array('error'=> 0, 'data'=> array('servertime' =>NOW));
        $this->out_print($response);
    }
    
    public function index(){
        $rtn = array();
        $uid = $this->uid ? $this->uid : false;
        $product = $this->homepage_logic->homepage_product_list($uid);
        $data['product'] = $product;  //热销产品
        $longproduct = $this->homepage_logic->homepage_longproduct_list($uid);
        $data['longproduct'] = $longproduct;    //活期产品
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
    public function product(){
        $rtn = array();
        $ptypeList = $this->homepage_logic->homepage_product_list();
        $data['product'] = $ptypeList;  //热销产品
        $data['complete'] = $this->homepage_logic->getCompleteProduct();    //已完成产品
        $selloutproduct = $this->homepage_logic->getSelloutProduct();
        $data['sellout'] = $selloutproduct;     //售罄产品
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
    
    public function longproduct(){
        $rtn = array();
        $uid = $this->uid ? $this->uid : false;
        $ptypeList = $this->homepage_logic->homepage_longproduct_list($uid);
        $data['longproduct'] = $ptypeList;                                //热销产品
        $selloutproduct = $this->homepage_logic->getSelloutLongProduct();
        //$data['sellout'] = array();                                     //售罄产品
        $data['sellout'] =$selloutproduct;                                //售罄产品
        
        $this->load->model('base/longmoney_income_log', 'longmoney_income_log');
        $defaultIncome = $this->longmoney_income_log->getLongIncome();
        
        if(!empty($ptypeList)){
        	$defaultIncome = $ptypeList[0]['income'];
        }else if(!empty($selloutproduct)){
        	$defaultIncome = $selloutproduct[0]['income'];
        }
        $response = array('error'=> 0, 'data'=> $data,'defaultIncome'=>$defaultIncome);
        $this->out_print($response);
    }

    public function gotoDetail(){
    	$type = trim($this->input->post('type')); //longproduct,product
    	$ptid = trim($this->input->post('ptid'));
    	if($type=='longproduct'){
    		$longproductList = $this->homepage_logic->homepage_longproduct_list();
    		if(empty($longproductList)){
    			$selloutproduct = $this->homepage_logic->getSelloutLongProduct();
    			$response = array('error'=> 0, 'data'=> $selloutproduct[0]);
    			$this->out_print($response);
    		}else{
    			$response = array('error'=> 0, 'data'=> $longproductList[0]);
    			$this->out_print($response);
    		}
    	}else{
    		$productList = $this->homepage_logic->homepage_product_list();
    		$productDetail = null;
    		foreach ($productList as $_product){
    			if($_product['ptid']==$ptid){
    				$productDetail = $_product;
    				break;
    			}
    		}
    		if(empty($productDetail)){
    			$selloutproduct = $this->homepage_logic->getSelloutProduct();
    			$productDetail = $selloutproduct[0];
    		}
    		$response = array('error'=> 0, 'data'=> $productDetail);
    		$this->out_print($response);
    	}
    }
    

    //活期、定期所有产品investment.php
     public function investmentDetail(){
        $ptid = trim($this->input->post('ptid'));
        $status = trim($this->input->post('status'));
        //$longproductList = $this->homepage_logic->homepage_longproduct_list();//热销产品
        //$selloutlongproduct = $this->homepage_logic->getSelloutLongProduct();//售罄产品
        $productList = $this->homepage_logic->homepage_product_list();        //热销产品
        $productcompleteList= $this->homepage_logic->getCompleteProduct();    //已完成产品
        $selloutproduct = $this->homepage_logic->getSelloutProduct();         //售罄产品
 
        // foreach($longproductList as $key=>$val){
        //     $longproductList[$key]['type'] = 'longproduct';
        // }
        // foreach($selloutlongproduct as $key=>$val){
        //     $selloutlongproduct[$key]['type'] = 'longproduct';
        // }
        foreach($productList as $key=>$val){
            $productList[$key]['type'] = 'product';
        }
        foreach($selloutproduct as $key=>$val){
            $selloutproduct[$key]['type'] = 'sellout';
        }
        foreach($productcompleteList as $key=>$val){
            $productcompleteList[$key]['type'] = 'complete';
        }
        $data["productList"]=$productList;                   //在售
        $data["selloutproductList"]=$selloutproduct;       //售罄
        $data["productcompleteList"]=$productcompleteList;                     //还款完成
        $ii=0;    //所有产品数量
        $page=6; //每页显示产品数
        $arr=array('productList','selloutproductList','productcompleteList');
        for($i=0;$i<count($arr);$i++){
            if(!empty($data[$arr[$i]])){
                foreach($data[$arr[$i]] as $val){
                    $ii++;
                }
            } 
        }
        $response = array('error'=> 0,'data'=> $data,'number'=>$ii,'page'=>$page); 
        if(!empty($ptid) || !empty($status)){  
            $productType=array();$ii=0;
            foreach($data as $key=>$val){
                foreach($val as $val_1){
                    if(!empty($ptid) && $ptid=="all"){
                        if(!empty($status) && $status=="all"){
                            $productType[$key][]=$val_1;
                        }else if(!empty($status)){
                            if($val_1["status"]==$status){           
                                $productType[$key][]=$val_1;
                            }
                        }else{
                            $productType[$key][]=$val_1;
                        }   
                    }else if(!empty($ptid)){
                        if(!empty($status) && $status=="all"){
                            if($val_1["ptid"]==$ptid){
                                $productType[$key][]=$val_1;
                            }   
                        }else if(!empty($status)){
                            if($val_1["ptid"]==$ptid && $val_1["status"]==$status){
                                $productType[$key][]=$val_1;
                            }
                        }else{
                            if($val_1["ptid"]==$ptid){
                                $productType[$key][]=$val_1;
                            }
                        } 
                    }else{
                        if(!empty($status) && $status=="all"){
                            $productType[$key][]=$val_1;
                        }else{
                            if($val_1["status"]==$status){           
                                $productType[$key][]=$val_1;
                            }
                        }
                    }
                }
            }
            foreach($productType as $val){
                if(is_array($val)){
                    foreach($val as $val_1){
                        $ii++;
                    }
                }else{
                    $ii++;
                }
            }     
            $response = array('error'=> 0,'data'=> $productType,'number'=>$ii,'page'=>$page);
        }       
        $this->out_print($response);
    }

    public function klproduct(){
        $rtn = array();
        $ptypeList = $this->homepage_logic->homepage_klproduct_list();
        $data['klproduct'] = $ptypeList;                                //热销产品
        $selloutproduct = $this->homepage_logic->getSelloutKlProduct();
        //$data['sellout'] = array();                                     //售罄产品
        $data['sellout'] =$selloutproduct;                                //售罄产品
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
    public function txt(){
    	$this->load->view('/text');
    }
    
    public function equalproduct(){
        $rtn = array();
        $ptypeList = $this->homepage_logic->homepage_equalproduct_list();
        $data['equalproduct'] = $ptypeList;                                 //热销产品
        $selloutproduct = $this->homepage_logic->getequalSelloutEqualProduct();
//         $data['sellout'] = array();                                      //售罄产品
        $data['sellout'] =$selloutproduct;                                  //售罄产品
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */