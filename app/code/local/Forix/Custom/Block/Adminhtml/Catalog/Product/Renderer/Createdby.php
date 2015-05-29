<?php

class Forix_Custom_Block_Adminhtml_Catalog_Product_Renderer_Createdby extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
//        $storeId = Mage::app()->getRequest()->getParam("store", 0);

//        if (Mage::registry('admin_user_on_product_grid')) {
//            $admin = Mage::registry('admin_user_on_product_grid');
//        }
//        else {
//            $admin = Mage::getModel("admin/user");
//            Mage::register('admin_user_on_product_grid',$admin);
//        }
        $admin = Mage::getModel("admin/user")->load($value);
        $html = $admin->getUsername();

        return $html;
    }

}
