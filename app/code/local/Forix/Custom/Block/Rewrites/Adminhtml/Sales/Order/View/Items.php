<?php
/**
 * Created by PhpStorm.
 * User: phamngochuan
 * Date: 5/18/15
 * Time: 10:40 PM
 */
class Forix_Custom_Block_Rewrites_Adminhtml_Sales_Order_View_Items extends Mage_Adminhtml_Block_Sales_Order_View_Items
{
    /**
     * Retrieve order items collection
     *
     * @return unknown
     */
    public function getItemsCollection()
    {
        $collection = parent::getItemsCollection();

        $admin = Mage::getSingleton('admin/session')->getUser();
        if ($admin && $admin->getUsername() !== Mage::getStoreConfig('admin_config/general/superadmin')) {
            $collection->addFieldToFilter('retail_id', array('eq' => $admin->getUserId()));
            $collection->clear()->load();
        }

        return $collection;
    }
}
