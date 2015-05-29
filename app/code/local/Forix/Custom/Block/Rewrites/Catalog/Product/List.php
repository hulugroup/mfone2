<?php
/**
 * Created by PhpStorm.
 * User: phamngochuan
 * Date: 5/21/15
 * Time: 10:13 PM
 */
class Forix_Custom_Block_Rewrites_Catalog_Product_List extends Mage_Catalog_Block_Product_List {

    public function __construct() {
        parent::__construct();
    }

    protected function _getProductCollection() {
        //echo __CLASS__;
        $collection = parent::_getProductCollection();

        $param = Mage::app()->getRequest()->getParams();
        if ($param['r'] && $param['r'] !== '') {
            $admin = Mage::getModel('admin/user')->load($param['r']);
            if ($admin && $admin->getUsername() !== Mage::getStoreConfig('admin_config/general/superadmin')) {
                $collection->addAttributeToSelect('created_by');
                $collection->addAttributeToFilter('created_by',array('eq' => $param['r']));
                //Mage::log($collection->getSelectSql(true),null,'frontenddebug.log');
            }
        }

        $this->_productCollection = $collection;
        return $this->_productCollection;
    }

}