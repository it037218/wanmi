<?php

class ljy_text extends Controller{
    
    public function __construct(){
        parent::__construct();
        
    }
    
    public function index(){
        $this->load->model('admin_txredistest_model','txredistest');
        $this->txredistest->test();
    }
    
    
}