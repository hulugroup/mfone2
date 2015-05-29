<?php

class Mage_Catalog_Model_Convert_Adapter_Extend_UpdateProduct extends
    Mage_Catalog_Model_Convert_Adapter_Productimport
{
    var $__defaultAttributes = array(
        'attribute_set' => 'Default',
    );
    var $__isUpdate = true;
    var $__vendorName = 'default';
    var $__skuFieldName = 'SKU';
    var $__imgFieldName = 'image';
    var $__pricingFields = 'color';
    var $__categoriesFieldName = 'CategoryID';

    public function saveRow(array $importData, $keepImage = true)
    {
        $this->__dataBeSaved = $this->__defaultAttributes;

        if (!$this->checkBeforeSave($importData)) {
            return;
        }

        $this->doDataFilter($importData);

        $this->__dataBeSaved = array_merge(
            $this->__dataBeSaved, array(
                'sku'                => $importData[$this->__skuFieldName]
            )
        );
        
        if ($importData['Shipping']) {
        	$this->__dataBeSaved['allowed_shipping_method'] = explode(";",$importData['Shipping']);
        }
        
        if ($importData['Quantity'] > 0) {
        	$this->__dataBeSaved['is_in_stock'] = "1";
        }

        if ($importData[$this->__categoriesFieldName]) {
            $this->__dataBeSaved['category_ids'] = explode(";",$importData[$this->__categoriesFieldName]);

            foreach($this->__dataBeSaved['category_ids'] as $key=>$cId){
                if(empty($this->__dataBeSaved['category_ids'][$key])) unset ($this->__dataBeSaved['category_ids'][$key]);
            }
        }
        
        parent::saveRow($this->__dataBeSaved, true);
        return true;
    }

	protected function _beforeProductSave($product, $importData)
    {
    	if ($importData["allowed_shipping_method"]) {
        	$product->setData("allowed_shipping_method",$importData["allowed_shipping_method"]);
        }
    }

    protected function doDataFilter($data)
    {

        parent::doDataFilter($data);
        $this->convertData($data, $this->__dataBeSaved, array(
            'description'             => 'Description',
            'manufacturer'            => 'Brand',
            'allowed_shipping_method' => 'Shipping',
            'qty'                     => 'Quantity',
            'cost'                     => 'Cost',
//------------ For configurable -----------------------------------------------
            'associated'              => 'associated',
            'visibility'              => 'visibility',
            'config_attributes'       => 'config_attributes',
            'type'                    => 'type',
            'product_type_id'         => 'product_type_id'
        ));
        // process custom option
        return $this->__dataBeSaved;
    }

}

