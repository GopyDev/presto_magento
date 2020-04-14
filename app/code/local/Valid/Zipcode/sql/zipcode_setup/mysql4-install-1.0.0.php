<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('zipcode')};
CREATE TABLE {$this->getTable('zipcode')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `postcode` varchar(255) NOT NULL default '',
  
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 