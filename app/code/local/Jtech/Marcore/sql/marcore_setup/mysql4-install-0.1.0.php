<?php
/************************************************************************
 * 
 * jtechextensions @ J-Tech LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.jtechextensions.com/LICENSE-M1.txt
 *
 * @package    Inventory Report
 * @copyright  Copyright (c) 2012-2013 jtechextensions @ J-Tech LLC. (http://www.jtechextensions.com)
 * @license    http://www.jtechextensions.com/LICENSE-M1.txt
************************************************************************/

$installer = $this;
$installer->startSetup();

$installer->run("
	CREATE TABLE IF NOT EXISTS " . $installer->getTable('jtech_core_attributes') . " (
		att_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		code varchar(255) NOT NULL UNIQUE,
		value varchar(255) NOT NULL,
		PRIMARY KEY (att_id)
	) ENGINE=INNODB DEFAULT CHARSET=utf8;

	INSERT IGNORE INTO " . $installer->getTable('jtech_core_attributes') . "
	SET	att_id = 1,
		code = 'cost_att_code',
		value = 'cost';

	INSERT IGNORE INTO " . $installer->getTable('jtech_core_attributes') . "
	SET	att_id = 2,
		code = 'cost_att_id',
		value = (
			Select attribute_id
			From " . $installer->getTable('eav_attribute') . "
			Where attribute_code = 'cost' and entity_type_id = (
				Select entity_type_id
				From " . $installer->getTable('eav_entity_type') . "
				Where entity_type_code = 'catalog_product'
			)
	);

	INSERT IGNORE INTO " . $installer->getTable('jtech_core_attributes') . "
	SET	att_id = 3,
		code = 'name_att_code',
		value = 'name';

	INSERT IGNORE INTO " . $installer->getTable('jtech_core_attributes') . "
	SET	att_id = 4,
		code = 'name_att_id',
		value = (
			Select attribute_id
			From " . $installer->getTable('eav_attribute') . "
			Where attribute_code = 'name' and entity_type_id = (
				Select entity_type_id
				From " . $installer->getTable('eav_entity_type') . "
				Where entity_type_code = 'catalog_product'
			)
	);
");

$installer->endSetup();
