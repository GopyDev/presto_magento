<?php
$installer = $this;
$installer->startSetup();
$installer->run("

CREATE TABLE {$this->getTable('shipping_zipcodes')} (
	`id` int(11) NOT NULL auto_increment,
	`zipcode` varchar(60) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 
