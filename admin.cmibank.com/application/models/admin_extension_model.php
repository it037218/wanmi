<?php
/**
 * 管理后台推广管理数据处理
 * Class Adminextensionmodel
 */
class admin_extension_model extends CI_Model {
    /**
     * 初始化
     */
    function __construct() {
		# 继承CI 父类
        parent::__construct();
		#载入base/baseextensionmodel 基类
		$this->load->model('base/baseextensionmodel' , 'BaseExtension');
	}

    /************************推广信息 start**************************/
    /**
     * 获取推广信息列表 （带分页）
     *
     * @param array $where
     * @param int $page
     * @param int $page_list
     * @param null $order_by
     * @return mixed
     */
    public function getExtensionList($where = array(), $page = 1, $page_list = 30, $order_by = null){
        $ret = $this->BaseExtension->getExtensionList($where, $page, $page_list, $order_by);

        return $ret;
    }

    /**
     * 添加推广信息
     *
     * @param $data
     * @return bool
     */
    public function addExtension($data){
        $ret = $this->BaseExtension->addExtension($data);

        return $ret;
    }

    /**
     * 根据推广编号删除推广信息
     *
     * @param $extension_id
     * @return bool|mixed
     */
    public function deleteExtensionByExtensionid($extension_id){
        $ret = $this->BaseExtension->deleteExtensionByExtensionid($extension_id);

        return $ret;
    }

    /**
     * 更新推广信息
     *
     * @param $data
     * @param $where
     * @return mixed
     */
    public function updateExtension($data, $where){
        $ret = $this->BaseExtension->updateExtension($data, $where);

        return $ret;
    }

    /**
     * 根据推广编号获取推广信息
     *
     * @param $extension_id
     * @return mixed
     */
    public function getExtensionInfoByExtensionid($extension_id){
        $ret = $this->BaseExtension->getExtensionInfoByExtensionid($extension_id);

        return $ret;
    }
    /************************推广信息 end**************************/
}

