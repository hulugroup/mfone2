<?php
/**
 * Created by PhpStorm.
 * User: phamngochuan
 * Date: 5/21/15
 * Time: 10:25 PM
 */
class Forix_Custom_Model_Rewrites_Catalog_Layer extends Mage_Catalog_Model_Layer
{

    public function __construct()
    {
        parent::__construct();
    }

    public function prepareProductCollection($collection)
    {
        $collection
            ->addAttributeToFilter('created_by', array('eq'=>'2'))
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite($this->getCurrentCategory()->getId())
            ;
        //Mage::log($collection->getSelectSql(true),null,'frontenddebug.log');
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        return $this;
    }
}