<?php

/**
 * 合作方管理
 * * */
class corporation extends Controller {

    
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '合同管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_corporation_model', 'corporation');
        $this->load->model('admin_creditor_infomation_model', 'creditor_infomation');
        $this->load->model('admin_contract_model','contract');
    }

    
    
    public function index() {
        $flag = $this->op->checkUserAuthority('债权公司管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '债权公司管理');
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $count = $this->corporation->getCorporationCount();
            $data = array();
            $data['list'] = $this->corporation->getCorporationList('', 'ctime desc', array($psize, $offset));
            //获取债权人信息
            $this->load->model('admin_creditor_infomation_model', 'creditor_infomation');
            foreach( $data['list'] as $key =>$value){
                $creditor_information = $this->creditor_infomation->getInformationByCorporationid($value['corid']);
                //获取 债权人
                $creditor = array();
                //获取 债权人身份证号/营业执照号
                $identity = array();
                //获取印章
                $seal = array();
                foreach($creditor_information as $k => $v){
                    $creditor[] = $v['creditor'];
                    $identity[] = $v['identity'];
                    $seal[] = $v['seal'];
                }
                $data['list'][$key]['creditor'] = implode('|',$creditor);
                $data['list'][$key]['identity'] = implode('|',$identity);
                $data['list'][$key]['seal'] = $seal;

            }
        //    var_dump($data);exit;
            $data['pageNum'] = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $data['rel'] = OP_DOMAIN . 'corporation/index?page=' . $page;
            $edatable = $this->op->getEditable($this->getSession('uid'),'1032');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            //user action log
            $log = $this->op->actionData($this->getSession('name'), '合同管理', '', '债权公司管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/corporation/v_index', $data);
        }
    }
    
    public function addcorporation(){
        $flag = $this->op->checkUserAuthority('债权公司管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '新增债权公司');
        } else {
            if($this->input->request('op') == 'addcorporation'){
                $cname = trim($this->input->post('cname'));
                $stamp = trim($this->input->post('stamp'));
                $ccname = trim($this->input->post('ccname'));
                $ccard = trim($this->input->post('ccard'));
                $bankname = trim($this->input->post('bankname'));
                $subbank = trim($this->input->post('subbank'));
                $banknum = trim($this->input->post('banknum'));
                $province = trim($this->input->post('province'));
                $guar_corp = trim($this->input->post('guar_corp'));
                $guarantee = trim($this->input->post('guarantee'));
                //获取债权人信息
                //债权人
                $creditor = $this->input->post('creditor');
                //验证号码
                $identity = $this->input->post('identity');
                $city = trim($this->input->post('city'));
                //印章
                $seal = $this->input->post('seal');
                //
                $count = count($creditor);
                $creditorData = array();
                for($i=0;$i<$count;$i++){
                    $creditorData[$i]['creditor']=$creditor[$i];
                    $creditorData[$i]['identity']=$identity[$i];
                    $creditorData[$i]['seal']=$seal[$i];
                    if(!$seal[$i] || !$identity[$i] || !$creditor[$i])
                    {
                        exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'缺少必要的信息1!')));
                    }
                }

                $data['cname'] = $cname;
                $data['stamp'] = $stamp;
                $data['ccname'] = $ccname;
                $data['ccard'] = $ccard;
                $data['bankname'] = $bankname;
                $data['subbank'] = $subbank;
                $data['banknum'] = $banknum;                
                $data['province'] = $province;                
                $data['city'] = $city;
                $data['ctime'] = time();        //创建时间
                $data['updatetime'] = time();        //修改时间
                $data['guar_corp'] = $guar_corp;     //担保法人
                $data['guarantee'] = $guarantee;     //担保人
                // var_dump($data);exit;
                if(!$cname || !$ccname || !$ccard || !$bankname || !$subbank || !$banknum || !$province || !$city || !$guar_corp || !$guarantee){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'缺少必要的信息!')));
                }
                $ret = $this->corporation->addCorporation($data);

                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'新增债权公司失败')));
                }else{
                    for($i=0;$i<$count;$i++){
                        $creditorData[$i]['corporationid']=$ret;
                    }
                    $cre=$this->creditor_infomation->insertCreditor($ret, $creditorData);

                }
                $log = $this->op->actionData($this->getSession('name'), '合同管理', '', '新增债权公司', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '新增成功', array(), '新增债权公司 ', 'forward', OP_DOMAIN.'/corporation'));
            }else{
                $data['province'] = $this->cityInfo();
                $this->load->view('/corporation/v_addCorporation', $data);
            }
        }
    }
    
    
    public function editcorporation() {
        $flag = $this->op->checkUserAuthority('债权公司管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '编辑产品');
        } else {
            
            if($this->input->request('op') == 'editcorporation'){
                $cid = $this->input->post('corid');
                if(!$cid){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $cname = trim($this->input->post('cname'));
                $stamp = trim($this->input->post('stamp'));
                $ccname = trim($this->input->post('ccname'));
                $ccard = trim($this->input->post('ccard'));
                $bankname = trim($this->input->post('bankname'));
                $subbank = trim($this->input->post('subbank'));
                $banknum = trim($this->input->post('banknum'));
                $province = trim($this->input->post('province'));
                $city = trim($this->input->post('city'));
                $guar_corp = trim($this->input->post('guar_corp'));
                $guarantee = trim($this->input->post('guarantee'));

                //获取债权人信息
                //债权人
                $id = $this->input->post('id');
                $creditor = $this->input->post('creditor');
                //验证号码
                $identity = $this->input->post('identity');
                //印章
                $seal = $this->input->post('seal');
                //
                $count = count($creditor);
                $creditorData = array();
            //    var_dump($this->input->post());exit;
                for($i=0;$i<$count;$i++){
                    $creditorData[$i]['id']=$id[$i];
                    $creditorData[$i]['creditor']=$creditor[$i];
                    $creditorData[$i]['identity']=$identity[$i];
                    $creditorData[$i]['seal']=$seal[$i];
                    if(!$seal[$i] || !$identity[$i] || !$creditor[$i])
                    {
                        exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'缺少必要的信息1!')));
                    }

                }

                if(!$cname || !$ccname || !$ccard || !$bankname || !$subbank || !$banknum || !$province || !$city || !$guar_corp || !$guarantee){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'缺少必要的信息2!')));
                }
                $data['cname'] = $cname;
                $data['stamp'] = $stamp;
                $data['ccname'] = $ccname;
                $data['ccard'] = $ccard;
                $data['bankname'] = $bankname;
                $data['subbank'] = $subbank;
                $data['banknum'] = $banknum;
                $data['province'] = $province;
                $data['city'] = $city;
                $data['updatetime'] = time();        //修改时间
                $data['guar_corp'] = $guar_corp;     //担保法人
                $data['guarantee'] = $guarantee;     //担保人
              //  var_dump($data);exit;
                $ret = $this->corporation->updateCorporation($cid, $data);


                
                //同时修改合同里面的公司名字
                $data_contract['corname'] = $cname;
                $contract = $this->contract->getContractList(array('corid'=>$cid),'','');
                foreach ($contract as $val){
                    $this->contract->updateContract($val['cid'], $data_contract);
                }

                if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
                }else{
                  //  var_dump($creditorData);exit;
                  //  var_dump($creditorData);
                    $cre=$this->creditor_infomation->updateCreditor($cid, $creditorData);

                }
                $log = $this->op->actionData($this->getSession('name'), '金融产品', '', '修改长期产品信息', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改产品信息 ', 'forward', OP_DOMAIN.'/corporation'));
            }else{
                $cid = $this->uri->segment(3);
                if($cid < 0 || !is_numeric($cid)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                //获取债权人的信息
                $creditor_information = $this->creditor_infomation->getInformationByCorporationid($cid);
                //
                $data['creditor']=$creditor_information;
                $data['detail'] = $this->corporation->getcorporationBycid($cid);
                $data['province'] = $this->cityInfo();
                $data['subprovince'] = $this->cityInfo($data['detail']['province'], false);
                $this->load->view('/corporation/v_editCorporation', $data);
            }
        }
    }

    
	public function delCorporation(){
		$flag=$this->op->checkUserAuthority('债权公司管理',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '债权公司管理');
        }else{
            $corid = $this->uri->segment(3);
            $ret = $this->corporation->delCorporationByCorid($corid);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '债权公司管理', '', '删除债权公司', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除债权公司', 'forward', OP_DOMAIN.'/corporation'));
	}
	
	
    
    
    public function cityInfo($province = '', $json = true){
        $cityInfo['北京'] = array('东城','西城','崇文','宣武','朝阳','丰台','石景山','海淀','门头沟','房山','通州','顺义','昌平','大兴','平谷','怀柔','密云','延庆');
        $cityInfo['上海'] = array('黄浦','卢湾','徐汇','长宁','静安','普陀','闸北','虹口','杨浦','闵行','宝山','嘉定','浦东','金山','松江','青浦','南汇','奉贤','崇明');
        $cityInfo['天津'] = array('和平','东丽','河东','西青','河西','津南','南开','北辰','河北','武清','红挢','塘沽','汉沽','大港','宁河','静海','宝坻','蓟县');
        $cityInfo['重庆'] = array('万州','涪陵','渝中','大渡口','江北','沙坪坝','九龙坡','南岸','北碚','万盛','双挢','渝北','巴南','黔江','长寿','綦江','潼南','铜梁','大足','荣昌','壁山','梁平','城口','丰都','垫江','武隆','忠县','开县','云阳','奉节','巫山','巫溪','石柱','秀山','酉阳','彭水','江津','合川','永川','南川');
        $cityInfo['河北'] = array('石家庄','邯郸','邢台','保定','张家口','承德','廊坊','唐山','秦皇岛','沧州','衡水');
        $cityInfo['山西'] = array('太原','大同','阳泉','长治','晋城','朔州','吕梁','忻州','晋中','临汾','运城');
        $cityInfo['内蒙古'] = array('呼和浩特','包头','乌海','赤峰','呼伦贝尔盟','阿拉善盟','哲里木盟','兴安盟','乌兰察布盟','锡林郭勒盟','巴彦淖尔盟','伊克昭盟');
        $cityInfo['辽宁'] = array('沈阳','大连','鞍山','抚顺','本溪','丹东','锦州','营口','阜新','辽阳','盘锦','铁岭','朝阳','葫芦岛');
        $cityInfo['吉林'] = array('长春','吉林','四平','辽源','通化','白山','松原','白城','延边');
        $cityInfo['黑龙江'] = array('哈尔滨','齐齐哈尔','牡丹江','佳木斯','大庆','绥化','鹤岗','鸡西','黑河','双鸭山','伊春','七台河','大兴安岭');
        $cityInfo['江苏'] = array('南京','镇江','苏州','南通','扬州','盐城','徐州','连云港','常州','无锡','宿迁','泰州','淮安');
        $cityInfo['浙江'] = array('杭州','宁波','温州','嘉兴','湖州','绍兴','金华','衢州','舟山','台州','丽水');
        $cityInfo['安徽'] = array('合肥','芜湖','蚌埠','马鞍山','淮北','铜陵','安庆','黄山','滁州','宿州','池州','淮南','巢湖','阜阳','六安','宣城','亳州');
        $cityInfo['福建'] = array('福州','厦门','莆田','三明','泉州','漳州','南平','龙岩','宁德');
        $cityInfo['江西'] = array('南昌市','景德镇','九江','鹰潭','萍乡','新馀','赣州','吉安','宜春','抚州','上饶');
        $cityInfo['山东'] = array('济南','青岛','淄博','枣庄','东营','烟台','潍坊','济宁','泰安','威海','日照','莱芜','临沂','德州','聊城','滨州','菏泽');
        $cityInfo['河南'] = array('郑州','开封','洛阳','平顶山','安阳','鹤壁','新乡','焦作','濮阳','许昌','漯河','三门峡','南阳','商丘','信阳','周口','驻马店','济源');
        $cityInfo['湖北'] = array('武汉','宜昌','荆州','襄樊','黄石','荆门','黄冈','十堰','恩施','潜江','天门','仙桃','随州','咸宁','孝感','鄂州');
        $cityInfo['湖南'] = array('长沙','常德','株洲','湘潭','衡阳','岳阳','邵阳','益阳','娄底','怀化','郴州','永州','湘西','张家界');
        $cityInfo['广东'] = array('广州','深圳','珠海','汕头','东莞','中山','佛山','韶关','江门','湛江','茂名','肇庆','惠州','梅州','汕尾','河源','阳江','清远','潮州','揭阳','云浮');
        $cityInfo['广西'] = array('南宁','柳州','桂林','梧州','北海','防城港','钦州','贵港','玉林','南宁地区','柳州地区','贺州','百色','河池');
        $cityInfo['海南'] = array('海口','三亚');
        $cityInfo['四川'] = array('成都','绵阳','德阳','自贡','攀枝花','广元','内江','乐山','南充','宜宾','广安','达川','雅安','眉山','甘孜','凉山','泸州');
        $cityInfo['贵州'] = array('贵阳','六盘水','遵义','安顺','铜仁','黔西南','毕节','黔东南','黔南');
        $cityInfo['云南'] = array('昆明','大理','曲靖','玉溪','昭通','楚雄','红河','文山','思茅','西双版纳','保山','德宏','丽江','怒江','迪庆','临沧');
        $cityInfo['西藏'] = array('拉萨','日喀则','山南','林芝','昌都','阿里','那曲');
        $cityInfo['陕西'] = array('西安','宝鸡','咸阳','铜川','渭南','延安','榆林','汉中','安康','商洛');
        $cityInfo['甘肃'] = array('兰州','嘉峪关','金昌','白银','天水','酒泉','张掖','武威','定西','陇南','平凉','庆阳','临夏','甘南');
        $cityInfo['宁夏'] = array('银川','石嘴山','吴忠','固原');
        $cityInfo['青海'] = array('西宁','海东','海南','海北','黄南','玉树','果洛','海西');
        $cityInfo['新疆'] = array('乌鲁木齐','石河子','克拉玛依','伊犁','巴音郭勒','昌吉','克孜勒苏柯尔克孜','博尔塔拉','吐鲁番','哈密','喀什','和田','阿克苏');
        $cityInfo['北京'] = array('东城','西城','崇文','宣武','朝阳','丰台','石景山','海淀','门头沟','房山','通州','顺义','昌平','大兴','平谷','怀柔','密云','延庆');
        $cityInfo['上海'] = array('黄浦','卢湾','徐汇','长宁','静安','普陀','闸北','虹口','杨浦','闵行','宝山','嘉定','浦东','金山','松江','青浦','南汇','奉贤','崇明');
        $cityInfo['天津'] = array('和平','东丽','河东','西青','河西','津南','南开','北辰','河北','武清','红挢','塘沽','汉沽','大港','宁河','静海','宝坻','蓟县');
        $cityInfo['重庆'] = array('万州','涪陵','渝中','大渡口','江北','沙坪坝','九龙坡','南岸','北碚','万盛','双挢','渝北','巴南','黔江','长寿','綦江','潼南','铜梁','大足','荣昌','壁山','梁平','城口','丰都','垫江','武隆','忠县','开县','云阳','奉节','巫山','巫溪','石柱','秀山','酉阳','彭水','江津','合川','永川','南川');
        $cityInfo['河北'] = array('石家庄','邯郸','邢台','保定','张家口','承德','廊坊','唐山','秦皇岛','沧州','衡水');
        $cityInfo['山西'] = array('太原','大同','阳泉','长治','晋城','朔州','吕梁','忻州','晋中','临汾','运城');
        $cityInfo['内蒙古'] = array('呼和浩特','包头','乌海','赤峰','呼伦贝尔盟','阿拉善盟','哲里木盟','兴安盟','乌兰察布盟','锡林郭勒盟','巴彦淖尔盟','伊克昭盟');
        $cityInfo['辽宁'] = array('沈阳','大连','鞍山','抚顺','本溪','丹东','锦州','营口','阜新','辽阳','盘锦','铁岭','朝阳','葫芦岛');
        $cityInfo['吉林'] = array('长春','吉林','四平','辽源','通化','白山','松原','白城','延边');
        $cityInfo['黑龙江'] = array('哈尔滨','齐齐哈尔','牡丹江','佳木斯','大庆','绥化','鹤岗','鸡西','黑河','双鸭山','伊春','七台河','大兴安岭');
        $cityInfo['江苏'] = array('南京','镇江','苏州','南通','扬州','盐城','徐州','连云港','常州','无锡','宿迁','泰州','淮安');
        $cityInfo['浙江'] = array('杭州','宁波','温州','嘉兴','湖州','绍兴','金华','衢州','舟山','台州','丽水');
        $cityInfo['安徽'] = array('合肥','芜湖','蚌埠','马鞍山','淮北','铜陵','安庆','黄山','滁州','宿州','池州','淮南','巢湖','阜阳','六安','宣城','亳州');
        $cityInfo['福建'] = array('福州','厦门','莆田','三明','泉州','漳州','南平','龙岩','宁德');
        $cityInfo['江西'] = array('南昌市','景德镇','九江','鹰潭','萍乡','新馀','赣州','吉安','宜春','抚州','上饶');
        $cityInfo['山东'] = array('济南','青岛','淄博','枣庄','东营','烟台','潍坊','济宁','泰安','威海','日照','莱芜','临沂','德州','聊城','滨州','菏泽');
        $cityInfo['河南'] = array('郑州','开封','洛阳','平顶山','安阳','鹤壁','新乡','焦作','濮阳','许昌','漯河','三门峡','南阳','商丘','信阳','周口','驻马店','济源');
        $cityInfo['湖北'] = array('武汉','宜昌','荆州','襄樊','黄石','荆门','黄冈','十堰','恩施','潜江','天门','仙桃','随州','咸宁','孝感','鄂州');
        $cityInfo['湖南'] = array('长沙','常德','株洲','湘潭','衡阳','岳阳','邵阳','益阳','娄底','怀化','郴州','永州','湘西','张家界');
        $cityInfo['广东'] = array('广州','深圳','珠海','汕头','东莞','中山','佛山','韶关','江门','湛江','茂名','肇庆','惠州','梅州','汕尾','河源','阳江','清远','潮州','揭阳','云浮');
        $cityInfo['广西'] = array('南宁','柳州','桂林','梧州','北海','防城港','钦州','贵港','玉林','南宁地区','柳州地区','贺州','百色','河池');
        $cityInfo['海南'] = array('海口','三亚');
        $cityInfo['四川'] = array('成都','绵阳','德阳','自贡','攀枝花','广元','内江','乐山','南充','宜宾','广安','达川','雅安','眉山','甘孜','凉山','泸州');
        $cityInfo['贵州'] = array('贵阳','六盘水','遵义','安顺','铜仁','黔西南','毕节','黔东南','黔南');
        $cityInfo['云南'] = array('昆明','大理','曲靖','玉溪','昭通','楚雄','红河','文山','思茅','西双版纳','保山','德宏','丽江','怒江','迪庆','临沧');
        $cityInfo['西藏'] = array('拉萨','日喀则','山南','林芝','昌都','阿里','那曲');
        $cityInfo['陕西'] = array('西安','宝鸡','咸阳','铜川','渭南','延安','榆林','汉中','安康','商洛');
        $cityInfo['甘肃'] = array('兰州','嘉峪关','金昌','白银','天水','酒泉','张掖','武威','定西','陇南','平凉','庆阳','临夏','甘南');
        $cityInfo['宁夏'] = array('银川','石嘴山','吴忠','固原');
        $cityInfo['青海'] = array('西宁','海东','海南','海北','黄南','玉树','果洛','海西');
        $cityInfo['新疆'] = array('乌鲁木齐','石河子','克拉玛依','伊犁','巴音郭勒','昌吉','克孜勒苏柯尔克孜','博尔塔拉','吐鲁番','哈密','喀什','和田','阿克苏');
        if($province){
            $province = urldecode($province);
            if($json){
                $rtn = array();
                foreach($cityInfo[$province] as $key=>$val){
                    $rtn[$val][0] = $val;
                    $rtn[$val][1] = $val;
                }
                echo json_encode($rtn);
                exit;
            }else{
                return $cityInfo[$province];
            }
        }
        return array_keys($cityInfo);
    }
    public function getcnnamelistBycname(){
        $cname = $this->input->post('cname');
        $data = $this->corporation->getcnnamelistBycname($cname);
        foreach ($data as $key => $val){
            $rtn[$key][0] = $val['cname'];
        }
        echo json_encode($rtn);
        exit;
    }

    public function getCreditorByid(){
        $cid = $this->input->post('cid');
        if($cid==""){
            echo "";exit;
        }
        $return = $this->creditor_infomation->getInformationByCorporationid($cid);
        echo json_encode($return);exit;

    }

    public function getCreditorBycreid(){
        $creid = $this->input->post('creid');
        if($creid==""){
            echo "";exit;
        }
        $return = $this->creditor_infomation->getInformationByid($creid);
        echo json_encode($return);exit;

    }
    
    
}