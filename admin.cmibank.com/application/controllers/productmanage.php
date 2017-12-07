<?php
/**
 * 合同与产品管理->产品管理
 * * */
class contractmanage extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '合同与产品管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
    }
}