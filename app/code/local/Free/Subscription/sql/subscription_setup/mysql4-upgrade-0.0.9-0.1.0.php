<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table customer_subsciption(id int not null auto_increment, customer_name varchar(100), customer_emailid varchar(255) , customer_id int(11) , start_date varchar(255) , expiry_date varchar(255) , duration varchar(255) , amount varchar(255) primary key(tablename_id))
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 