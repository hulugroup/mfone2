<?php
/**
 * Created by PhpStorm.
 * User: phamngochuan
 * Date: 5/23/15
 * Time: 2:23 PM
 */
class Forix_Custom_Block_Rewrites_Adminhtml_Sales_Order_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals//Mage_Adminhtml_Block_Sales_Order_Abstract
{
    protected function _initTotals()
    {
        parent::_initTotals();

        $admin = Mage::getSingleton('admin/session')->getUser();
        if ($admin && $admin->getUsername() == Mage::getStoreConfig('admin_config/general/superadmin')) {
            return $this;
        }

        $order = $this->getSource();
        $retailOrderCollection = Mage::getModel('custom/retailorder')->getCollection()
            ->addFieldToFilter('order_id',array('eq'=>$order->getId()))
            ->addFieldToFilter('retail_id',array('eq'=>$admin->getUserId()));

        $retailOrder = $retailOrderCollection->getFirstItem();
        //Mage::log($retailOrderCollection->getSelectSql(true),null,'admindebug.log');
        if (!$retailOrder) {
            return $this;
        }

        $this->_totals['subtotal'] = new Varien_Object(array(
            'code'      => 'subtotal',
            'value'     => $retailOrder->getBaseSubtotal(),
            'base_value'=> $retailOrder->getBaseSubtotal(),
            'label'     => $this->helper('sales')->__('Subtotal')
        ));

//        $this->_totals['shipping'] = new Varien_Object(array(
//            'code'      => 'shipping',
//            'value'     => $this->getSource()->getShippingAmount(),
//            'base_value'=> $this->getSource()->getBaseShippingAmount(),
//            'label' => $this->helper('sales')->__('Shipping & Handling')
//        ));
        unset($this->_totals['shipping']);

        $discountLabel = $this->helper('sales')->__('Discount');
        $this->_totals['discount'] = new Varien_Object(array(
            'code'      => 'discount',
            'value'     => $this->getSource()->getDiscountAmount(),
            'base_value'=> $this->getSource()->getBaseDiscountAmount(),
            'label'     => $discountLabel
        ));

        $this->_totals['grand_total'] = new Varien_Object(array(
            'code'      => 'grand_total',
            'strong'    => true,
            'value'     => $retailOrder->getBaseGrandTotal(),
            'base_value'=> $retailOrder->getBaseGrandTotal(),
            'label'     => $this->helper('sales')->__('Grand Total'),
            'area'      => 'footer'
        ));

//        $this->_totals['paid'] = new Varien_Object(array(
//            'code'      => 'paid',
//            'strong'    => true,
//            'value'     => $this->getSource()->getTotalPaid(),
//            'base_value'=> $this->getSource()->getBaseTotalPaid(),
//            'label'     => $this->helper('sales')->__('Total Paid'),
//            'area'      => 'footer'
//        ));
        unset($this->_totals['paid']);

//        $this->_totals['refunded'] = new Varien_Object(array(
//            'code'      => 'refunded',
//            'strong'    => true,
//            'value'     => $this->getSource()->getTotalRefunded(),
//            'base_value'=> $this->getSource()->getBaseTotalRefunded(),
//            'label'     => $this->helper('sales')->__('Total Refunded'),
//            'area'      => 'footer'
//        ));
        unset($this->_totals['refunded']);

//        $this->_totals['due'] = new Varien_Object(array(
//            'code'      => 'due',
//            'strong'    => true,
//            'value'     => $this->getSource()->getTotalDue(),
//            'base_value'=> $this->getSource()->getBaseTotalDue(),
//            'label'     => $this->helper('sales')->__('Total Due'),
//            'area'      => 'footer'
//        ));
        unset($this->_totals['due']);

        return $this;
    }
}