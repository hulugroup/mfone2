<?php
/**
 * Created by PhpStorm.
 * User: phamngochuan
 * Date: 5/18/15
 * Time: 8:40 AM
 */
class Forix_Custom_Model_Observer
{
    public function catalogProductSaveBefore($ob)
    {
        //Mage::log('Event fired',null,'admindebug.log');
        $product = $ob->getProduct();

        $admin = Mage::getSingleton('admin/session')->getUser();
        //Mage::log('Product: '.$product->getId(),null,'admindebug.log');
        if ($admin && $admin->getId()) {
            if (!$product->getCreatedBy()) {
                $product->setCreatedBy($admin->getId());
            }
        }

    }

    public function salesOrderPlaceAfter($ob)
    {
        $retailOrder = Mage::getModel('custom/retailorder');
        $order = $ob->getOrder();

        $orderId = $order->getId();

        //Loop through order items and store order Total for each retailer
        $orderedItems = $order->getAllVisibleItems();
        $orderedProductIds = array();
        $orderedItemAmt = array();
        foreach ($orderedItems as $item) {
            array_push($orderedProductIds, $item->getData('product_id'));

            $itemAmt = $item->getPrice()*$item->getQtyOrdered();
            $orderedItemAmt[$item->getData('product_id')] = $itemAmt;
        }
        $productCollection = Mage::getModel('catalog/product')->getCollection();
        $productCollection->addAttributeToSelect('created_by');
        $productCollection->addIdFilter($orderedProductIds);

        $retailData = array();
        foreach ($productCollection as $item) {
            $item->load($item->getId());
            $createdBy = $item->getCreatedBy();
            if ($createdBy) {
                $retailData[$createdBy] += $orderedItemAmt[$item->getId()];
            }
        }

        //$retailData[0]=0;
        if (sizeof($retailData) > 0) {
            foreach ($retailData as $id=>$value) {
                $baseSubTotal = $value;
                $baseGrandTotal = $value;

                $retailOrder->setData(array(
                    "retail_id"    => $id,
                    "order_id"     => $orderId,
                    "base_subtotal"  => $baseSubTotal,
                    "base_grand_total"   => $baseGrandTotal
                ));

                $retailOrder->save();
            }
        }
    }

    /*
     Mage::dispatchEvent('sales_convert_quote_item_to_order_item',
            array('order_item'=>$orderItem, 'item'=>$item)
        );
     */
    public function salesConvertQuoteItemToOrderItem($ob)
    {
        /* @var $orderItem Mage_Sales_Model_Order_Item */
        $orderItem = $ob->getEvent()->getOrderItem();
        /* @var $item Mage_Sales_Model_Quote_Item */
        $item = $ob->getEvent()->getItem();
        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        //Mage::log('createdby:'.$product->getCreatedBy(),null,'frontenddebug.log');
        if ($product && $product->getCreatedBy()) {
            $orderItem->setRetailId($product->getCreatedBy());
        }
    }
}