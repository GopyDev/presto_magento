<?php
/**
 * Abandoned Carts Alerts Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Cartalert
 * @version      3.3.4
 * @license:     W22sKZAc65sLiShWmPFxsroCMZvSx8DyIvGLqZPs4w
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
$installer = $this;

$installer->startSetup();

$quote = $this->getTable('sales/quote');

$installer->run("

ALTER TABLE `$quote` ADD `allow_alerts` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' ;

");

$installer->endSetup();