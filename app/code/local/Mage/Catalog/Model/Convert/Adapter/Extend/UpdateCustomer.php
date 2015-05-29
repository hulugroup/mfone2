<?php

class Mage_Catalog_Model_Convert_Adapter_Extend_UpdateCustomer extends
    Mage_Catalog_Model_Convert_Adapter_Productimport
{

    public function saveRow(array $importData, $keepImage = false)
    {
        $email = $importData["Email"];
        $profileId = $importData["ProfileId"];
        $customer = Mage::getModel("customer/customer")->setWebsiteId(1)->loadByEmail($email);
        $customer->setData("customer_profile_id",$profileId)->save();
        return true;
    }
}

