<?php

class Mage_Catalog_Model_Convert_Adapter_Extend_UpdateCrossSellUpSell extends
    Mage_Catalog_Model_Convert_Adapter_Productimport
{
    public function saveRow(array $importData)
    {
        if (empty($importData['ConfigurableSku'])) {
            $message = Mage::helper('catalog')->__('Skip import row, required field "%s" not defined',
                'sku');
            $this->logErrors($message);
            Mage::throwException($message);
        }

        $product = Mage::getModel('catalog/product');
        $importData['ConfigurableSku'] = substr($importData['ConfigurableSku'], 0, 64);
        $product->setStoreId(Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId());
        $productId = $product->getIdBySku($importData['ConfigurableSku']);
        $product->load($productId);

        $update = false;
        if (!empty($importData['Crosssell'])) {
            $param = array();
            $crosssells = $product->getCrossSellProducts();
            foreach ($crosssells as $item) {
                $param[$item->getId()] = array('position' => $item->getPosition());
            }
            $product2 = Mage::getModel('catalog/product');
            foreach(explode(';',$importData['Crosssell']) as $sku)
            {
                $productId = $product2->getIdBySku($sku);
                if(!array_key_exists($productId, $param))
                {
                    $param[$productId] = array('position' => 1);
                }
            }
            if(count($param) > 0)
            {
                $product->setCrossSellLinkData($param);
                $update = true;
            }
        }

        if (!empty($importData['Upsell'])) {
            $param = array();
            $upsells = $product->getUpSellProducts();
            foreach ($upsells as $item) {
                $param[$item->getId()] = array('position' => $item->getPosition());
            }
            $product2 = Mage::getModel('catalog/product');
            foreach(explode(';',$importData['Upsell']) as $sku)
            {
                $productId = $product2->getIdBySku($sku);
                if(!array_key_exists($productId, $param))
                {
                    $param[$productId] = array('position' => 1);
                }
            }
            if(count($param) > 0)
            {
                $product->setUpSellLinkData($param);
                $update = true;
            }
        }

        if($update)
        {
            $product->save();
        }

        return true;
    }

}