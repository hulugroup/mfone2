<?php

class Mage_Catalog_Model_Convert_Adapter_Extend_ImportDownloadable extends
    Mage_Catalog_Model_Convert_Adapter_Productimport
{

    var $__defaultAttributes = array(
        'attribute_set'              => 'Default',
        'qty'                        => '0',
        'is_in_stock'                => '1',
        'visibility'                 => 'Not Visible Individually',
        'weight'                     => '1',
        'type'                       => 'downloadable',
        'links_purchased_separately' => 0
    );
    var $__vendorName = 'default';
    var $__skuFieldName = 'SKU';
    var $__FileName = 'FileName';
    var $__imgFieldName = 'image';
    var $__pricingFields = 'digital';
    var $__categoriesFieldName = 'Category';

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
        $this->__dataBeSaved["product_category_attr"] = str_replace(";",self::MULTI_DELIMITER,$importData["CategoryAttr"]);
        $importData[$this->__categoriesFieldName] = preg_replace('/\s*;\s*/i', ',', $importData[$this->__categoriesFieldName]);
        $this->__dataBeSaved["categories"] = $importData[$this->__categoriesFieldName];
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
        if(isset($data[$this->__FileName]))
        {
            $fileName = trim($data[$this->__FileName]);
        }
        $file[0] = $fileName;
        $file[1] = '';
        $file[2] = 1000; //limit download
        $file[3] = 'file';
        if(isset($data[$this->__FileName]))
        {
            $file[4] = $fileName;
        }
        else
        {
            $file[4] = $fileName . ".zip";
        }
        $download_files[] = implode(',', $file);
        $data["downloadable_options"] = implode("|", $download_files);

        parent::doDataFilter($data);
        $this->convertData($data, $this->__dataBeSaved, array(
            'name'                  => 'Name',
            'meta_keyword'          => 'Key Words',
            'price'                 => 'Price',
//            'manufacturer'         => 'brand',
            'description'           => 'Name',
            'short_description'     => 'Name',
            'image'                 => 'image',
            'small_image'           => 'small_image',
            'thumbnail'             => 'small_image',
            'gallery'               => 'gallery',
//            'color'                 => 'color',
//            'size'                  => 'size',
            'digital'               => 'digital',
            'anne_usability'        => 'Usability',
            'anne_custom_only_sub'  => 'Custom Only Sub',
            'anne_density'          => 'Density',
            'anne_gender'           => 'Gender',
            'anne_quilting_level'   => 'Quilting Level',
            'product_category_attr' => "CategoryAttr",
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

