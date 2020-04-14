<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('survey_answer')} ADD `survey_ans_qn_id` INT(11) NULL AFTER `survey_ans_title`;");

$installer->endSetup();
?>
