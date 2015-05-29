<?php
/**
 * Created by PhpStorm.
 * User: phamngochuan
 * Date: 5/18/15
 * Time: 10:40 PM
 */
class Forix_Custom_Block_Rewrites_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    public function setCollection($collection)
    {
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */

        $collection->addAttributeToSelect('created_by');

        $superAdminUsername = Mage::getStoreConfig('admin_config/general/superadmin');
        $admin = Mage::getSingleton('admin/session')->getUser();
        if ($admin && $admin->getUsername() !== $superAdminUsername) {
            $collection->addAttributeToFilter('created_by',array('eq' => $admin->getUserId()));
        }
        //Mage::log($collection->getSelectSql(true),null,'admindebug.log');

        parent::setCollection($collection);

    }

    protected function _prepareColumns()
    {
        $store = $this->_getStore();
        $this->addColumnAfter('created_by',
            array(
                'header'=> Mage::helper('catalog')->__('Created By'),
                'index' => 'created_by',
                'renderer'                  => 'Forix_Custom_Block_Adminhtml_Catalog_Product_Renderer_Createdby',
                'filter_condition_callback' => array($this, '_createdByFilter')
            ),
            'status'
        );

        return parent::_prepareColumns();
    }

    protected function _createdByFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $adminUserCollection = Mage::getModel("admin/user")->getCollection()->addFieldToFilter("username", array("like" => "%$value%"));
        $adminUserIds = $adminUserCollection->getAllIds();

        if (count($adminUserIds)>0)
        {
            /* @var $select Varien_Db_Select */
//            $select = $this->getCollection()->getSelect();
//            $select->joinInner(array("indextb"=>"admin_user"),"indextb.product_id = e.entity_id and indextb.category_id IN (".implode(",",$adminUserIds).")",array());
//            $select->distinct(true);

            $this->getCollection()->addAttributeToFilter('created_by', array('in' => $adminUserIds));
            $select = $this->getCollection()->getSelect()->__toString();
            //Mage::log($select,null,'admindebug.log');
        }
        return $this;
    }
}