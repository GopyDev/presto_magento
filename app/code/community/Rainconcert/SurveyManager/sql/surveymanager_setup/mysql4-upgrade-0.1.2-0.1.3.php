<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('survey')} ADD `survey_layout_template` varchar(20) NULL AFTER `survey_url`;");

$installer->endSetup();
?>
