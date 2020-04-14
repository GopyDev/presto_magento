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
    CREATE TABLE IF NOT EXISTS " . $installer->getTable('jtech_pl_attribute_types') . " (
		type_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		code varchar(255) NOT NULL,
		PRIMARY KEY (type_id)
	) ENGINE=INNODB DEFAULT CHARSET=utf8;
	
	INSERT INTO " . $installer->getTable('jtech_pl_attribute_types') . " (code) VALUES ('option'), ('revenue'), ('expense');
	
	CREATE TABLE IF NOT EXISTS  " . $installer->getTable('jtech_pl_attributes') . " (
		att_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL default '',
		att_type_id int(11) UNSIGNED NOT NULL,
		is_parent tinyint(1) UNSIGNED NOT NULL default '0',
		parent_id int(11) UNSIGNED default NULL,
		PRIMARY KEY (att_id),
		FOREIGN KEY (att_type_id) REFERENCES " . $installer->getTable('jtech_pl_attribute_types') . "(type_id) ON DELETE CASCADE
	) ENGINE=INNODB DEFAULT CHARSET=utf8;
	
	INSERT INTO " . $installer->getTable('jtech_pl_attributes') . " (name, att_type_id) 
	VALUES 	('store_id', 1),
		('match_period', 1),
		('period', 1),
		('date_from', 1), 
		('date_to', 1),
		('status', 1),
		('shipping_flag', 1),
		('cost_source', 1);
	
	CREATE TABLE IF NOT EXISTS  " . $installer->getTable('jtech_pl_reports') . " (
		report_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL default '',
		date_created datetime NOT NULL,
		date_updated datetime NOT NULL,
		PRIMARY KEY (report_id)
	) ENGINE=INNODB DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS  " . $installer->getTable('jtech_pl_reports_attributes') . " (
		report_att_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		report_id int(11) UNSIGNED NOT NULL,
		att_id int(11) UNSIGNED NOT NULL,
		att_name varchar(255) NOT NULL default '',
		is_parent tinyint(1) UNSIGNED NOT NULL default '0',
		parent_att_id int(11) UNSIGNED default NULL,
		att_type_id int(11) UNSIGNED NOT NULL,
		value varchar(255) NOT NULL default '',
		PRIMARY KEY (report_att_id),
		FOREIGN KEY (report_id) REFERENCES " . $installer->getTable('jtech_pl_reports') . "(report_id) ON DELETE CASCADE,
		FOREIGN KEY (att_type_id) REFERENCES " . $installer->getTable('jtech_pl_attribute_types') . "(type_id)
	) ENGINE=INNODB DEFAULT CHARSET=utf8;
");

$installer->endSetup();