<?php
/**
 * Activo Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Activo Commercial License
 * that is available through the world-wide-web at this URL:
 * http://extensions.activo.com/license_professional
 *
 * @copyright   Copyright (c) 2013 Activo Extensions (http://extensions.activo.com)
 * @license     Commercial
 */
 
$installer = $this;
$installer->startSetup();
 
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('activo_bulkimages_import')};
CREATE TABLE {$this->getTable('activo_bulkimages_import')} (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `successfull` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `num_images` INT(10) NOT NULL DEFAULT 0,
  `num_images_unmatched` INT(10) NOT NULL DEFAULT 0,
  `num_skus` INT(10) NOT NULL DEFAULT 0,
  `num_skus_unmatched` INT(10) NOT NULL DEFAULT 0,
  `num_matches` INT(10) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00', 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
 
$installer->endSetup();
?>
