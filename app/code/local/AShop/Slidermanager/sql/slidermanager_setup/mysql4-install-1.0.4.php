<?php
/**
 * Accessshop
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the AccessshopThemes.com license that is
 * available through the world-wide-web at this URL:
 * http://www.accessshopThemes.com
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Accessshop
 * @package 	Accessshop_Slidermanager
 * @copyright 	Copyright (c) 2015 Accessshop (http://www.accessshopThemes.com)
 * @license     http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

/**
 * Accessshop Block
 * 
 * @category 	Accessshop
 * @package 	Accessshop_Slidermanager
 * @author  	Accessshop Developer
 */
$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('slidermanager')};
DROP TABLE IF EXISTS {$this->getTable('bannermanager')};

CREATE TABLE {$this->getTable('slidermanager')} (
  `slidermanager_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `content` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `background_image` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  `link_button` smallint(6) NOT NULL default '0',
  `link` text NOT NULL default '',
  `button_title` varchar(255) NOT NULL default '',
  `bannermanager_id` int(11) NOT NULL default '0',
  `target` varchar(255) NOT NULL default '',
  PRIMARY KEY (`slidermanager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('bannermanager')} (
  `bannermanager_id` int(11) unsigned NOT NULL auto_increment,
  `slidername` varchar(255) NOT NULL default '',
  `slider_key` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`bannermanager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup(); 