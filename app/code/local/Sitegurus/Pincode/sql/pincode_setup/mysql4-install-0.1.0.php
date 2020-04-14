<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('pincode')};
CREATE TABLE {$this->getTable('pincode')} (
  `pincode_id` int(11) unsigned NOT NULL auto_increment,
  `area_name` varchar(255) NOT NULL default '',
  `pin_code` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`pincode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 