<?php
$installer = $this;
$installer->startSetup();
$sql = <<<SQLTEXT
CREATE TABLE forix_retail_order (
id  int UNSIGNED NOT NULL AUTO_INCREMENT ,
retail_id  int(10) UNSIGNED NULL ,
order_id  int(10) UNSIGNED NULL ,
base_subtotal  decimal(12,4) NULL ,
base_grand_total  decimal(12,4) NULL ,
PRIMARY KEY (id),
FOREIGN KEY (retail_id) REFERENCES admin_user (user_id) ON DELETE SET NULL ON UPDATE SET NULL,
INDEX (retail_id, order_id) USING HASH
)
;

SQLTEXT;

$installer->run($sql);
$installer->endSetup();
	 