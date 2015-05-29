<?php
class Forix_Custom_Model_Mysql4_Retailorder extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("custom/retailorder", "id");
    }
}