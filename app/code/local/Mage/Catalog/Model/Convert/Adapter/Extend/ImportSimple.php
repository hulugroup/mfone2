<?php

class Mage_Catalog_Model_Convert_Adapter_Extend_ImportSimple extends
    Mage_Catalog_Model_Convert_Adapter_Productimport
{

    var $__defaultAttributes = array(
        'attribute_set'              => 'Default',
        'qty'                        => '0',
        'is_in_stock'                => '1',
        'visibility'                 => 'Catalog, Search',
        'weight'                     => '1',
        'type'                       => 'simple',
        'links_purchased_separately' => 1
    );
    var $__vendorName = 'default';
    var $__skuFieldName = 'SKU';
    var $__imgFieldName = 'image';
    var $__pricingFields = 'digital';
    var $__categoriesFieldName = 'CategoryID';

    /**
     * Save product (import)
     *
     * @param array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData, $keepImage = false)
    {
        $this->__dataBeSaved = $this->__defaultAttributes;

        if (!$this->checkBeforeSave($importData)) {
            return;
        }
        //Mage::log(print_r($this->preProcessCategory($importData),true),null,'import.log');
        $this->doImageDownload($importData);
        $this->doDataFilter($importData);
        $websites = array();
        foreach (Mage::app()->getWebsites() as $web) {
            $websites[] = $web->getCode();
        }

        $this->__dataBeSaved = array_merge(
            $this->__dataBeSaved, array(
                'sku'      => $importData[$this->__skuFieldName],
                'store'    => "default",
                'websites' => implode(',', $websites)
            )
        );
        //$this->__dataBeSaved["product_category_attr"] = str_replace(";",self::MULTI_DELIMITER,$importData["CategoryAttr"]);
        //$importData[$this->__categoriesFieldName] = preg_replace('/\s*;\s*/i', ',', $importData[$this->__categoriesFieldName]);
        //$this->__dataBeSaved["categories"] = $importData[$this->__categoriesFieldName];
        $this->__dataBeSaved['category_ids'] = explode(";", $importData[$this->__categoriesFieldName]);
        if (is_array($this->__dataBeSaved['category_ids'])) {
            foreach ($this->__dataBeSaved['category_ids'] as $key => $cId) {
                if (empty($this->__dataBeSaved['category_ids'][$key])) unset ($this->__dataBeSaved['category_ids'][$key]);
            }
        }
        parent::saveRow($this->__dataBeSaved, $keepImage);
        return true;
    }

    protected function doImageDownload(&$data)
    {
        $images = explode(";", $data["Image"]);
        if (count($images)) {
            foreach ($images as $key => $img) {
                $images[$key] = $img;
            }

            $data['image'] = array_shift($images);
            $data['small_image'] = $data['image'];
            $data['thumbnail'] = $data['image'];

            $data['gallery'] = join(',', $images);
        } else {
            $this->logErrors('[sku:' . $data[$this->__skuFieldName] . ']' . ' have no Image');
        }
    }

    protected function doDataFilter($data)
    {
        $download_files = array();
        $file = array();
        //$fileName = trim(strtolower($data[$this->__skuFieldName]));
        $fileName = trim($data[$this->__skuFieldName]);
        $file[0] = $fileName;
        $file[1] = '';
        $file[2] = 1000; //limit download
        $file[3] = 'file';
        $file[4] = $fileName . ".zip";
        $download_files[] = implode(',', $file);
        $data["downloadable_options"] = implode("|", $download_files);

        parent::doDataFilter($data);
        $this->convertData($data, $this->__dataBeSaved, array(
            'name'                  => 'Name',
            'meta_keyword'          => 'Key Words',
            'price'                 => 'Price',
//            'manufacturer'         => 'brand',
            'description'           => 'Description',
            'short_description'     => 'Description',
            'image'                 => 'Image',
            'small_image'           => 'Image',
            'thumbnail'             => 'Image',
            'gallery'               => 'gallery',
//            'color'                 => 'color',
//            'size'                  => 'size',
            'serial_number'         => 'Serial Number',
            'sim_number'                => 'SIM Number',
            'sim_head'              => 'SIM Head',
            'sim_number_type'       => 'SIM Number Type',
            'attribute_set'         => 'Type',
//------------ For download product ------------------------
            'downloadable_options'  => 'downloadable_options',

//------------ For configurable ----------------------------
            'associated'            => 'associated',
            'visibility'            => 'visibility',
            'config_attributes'     => 'config_attributes',
            'type'                  => 'type',
            'product_type_id'       => 'product_type_id'
        ));

        // process custom option
        return $this->__dataBeSaved;
    }

}

