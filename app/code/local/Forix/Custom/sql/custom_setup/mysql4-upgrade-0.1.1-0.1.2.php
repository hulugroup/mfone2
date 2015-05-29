<?php
$installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$installer->startSetup();

$installer->addAttribute('order_item', 'retail_id', array('type'=>'int'));
$installer->addAttribute('quote_item', 'retail_id', array('type'=>'int'));

$installer->endSetup();
	 