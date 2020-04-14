<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('survey_qn_relation')};
CREATE TABLE {$this->getTable('survey_qn_relation')} (
  `survey_qn_relation_id` int(11) unsigned NOT NULL auto_increment,
  `survey_id` int(11) unsigned  NOT NULL, 
  `survey_qn_id` int(11) unsigned  NOT NULL, 
  PRIMARY KEY (`survey_qn_relation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->endSetup();
?>
