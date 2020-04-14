<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('survey')};
CREATE TABLE {$this->getTable('survey')} (
  `survey_id` int(11) unsigned NOT NULL auto_increment,
  `survey_name` varchar(2000) NOT NULL default '',
  `survey_desc` text NULL default '',
  `survey_url` text NOT NULL default '',
  `is_active` smallint(6) NOT NULL default '0', 
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- DROP TABLE IF EXISTS {$this->getTable('survey_questions')};
CREATE TABLE {$this->getTable('survey_questions')} (
  `survey_qn_id` int(11) unsigned NOT NULL auto_increment,
  `survey_qn_title` varchar(2000) NOT NULL,
  `survey_qn_desc` text NULL default '',  
  `survey_ans_type` int(3) unsigned  default '0', 
  `is_active` smallint(6) NOT NULL default '0',  
  `created_time` datetime NULL,
  `update_time` datetime NULL, 
  PRIMARY KEY (`survey_qn_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('survey_answer')};
CREATE TABLE {$this->getTable('survey_answer')} (
  `survey_ans_id` int(11) unsigned NOT NULL auto_increment,
  `survey_ans_title` varchar(2000) NOT NULL,
  `is_active` smallint(6) NOT NULL default '0',  
  `created_time` datetime NULL,
  `update_time` datetime NULL, 
  PRIMARY KEY (`survey_ans_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('survey_answer_value')};
CREATE TABLE {$this->getTable('survey_answer_value')} (
  `survey_ans_value_id` int(11) unsigned NOT NULL auto_increment,
  `survey_ans_id` int(11) unsigned NOT NULL,
  `survey_ans_value` text  NULL,
  `is_active` smallint(6) NOT NULL default '0',  
  `created_time` datetime NULL,
  `update_time` datetime NULL, 
  PRIMARY KEY (`survey_ans_value_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('survey_result')};
CREATE TABLE {$this->getTable('survey_result')} (
  `survey_result_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NULL,
  `session_id` text  NULL,
  `survey_qn_id` int(11) NULL,
  `is_active` smallint(6) NOT NULL default '0',  
  `created_time` datetime NULL,
  `update_time` datetime NULL, 
  PRIMARY KEY (`survey_result_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('survey_result_value')};
CREATE TABLE {$this->getTable('survey_result_value')} (
  `survey_result_value_id` int(11) unsigned NOT NULL auto_increment,
  `survey_result_id` int(11) unsigned NOT NULL,
  `survey_result_value` text  NULL,
  `survey_ans_type_id` int(11) NULL,  
  `created_time` datetime NULL,
  `update_time` datetime NULL, 
  PRIMARY KEY (`survey_result_value_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

