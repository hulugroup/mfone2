<?php
/**
 * Created by PhpStorm.
 * User: phamngochuan
 * Date: 5/23/15
 * Time: 11:19 AM
 */
class Forix_Custom_Model_Rewrites_Sales_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    public function setProduct($product)
    {
        parent::setProduct($product);

        $this->setRetailId($product->getCreatedBy());

        if ($product->getStockItem()) {
            $this->setIsQtyDecimal($product->getStockItem()->getIsQtyDecimal());
        }

        Mage::dispatchEvent('sales_quote_item_set_product', array(
            'product' => $product,
            'quote_item' => $this
        ));


//        if ($options = $product->getCustomOptions()) {
//            foreach ($options as $option) {
//                $this->addOption($option);
//            }
//        }
        return $this;
    }
}

