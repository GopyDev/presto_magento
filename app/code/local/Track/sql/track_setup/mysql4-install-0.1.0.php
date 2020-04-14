<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table 'user_tracking(id int not null auto_increment, email varchar(255),trackdevice varchar(255), primary key(id));
		
SQLTEXT;

$installer->run($sql);
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE {$this->getTable('user_tracking')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,,
  `email` varchar(255) NOT NULL default '',
  `trackdevice` varchar(255) NOT NULL default '',
 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
$installer->endSetup();
	 