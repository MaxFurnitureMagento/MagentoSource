<?php
/**
 * Mxp 
 * Menuadmin
 * 
 * @category    Mxp
 * @package     Mxp_Menuadmin
 * @copyright   Copyright (c) 2011 Mxp (http://www.magentoxp.com)
 * @author      Magentoxp (Mxp)Magentoxp Team <support@magentoxp.com>
 */

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('menuadmin')};
CREATE TABLE {$this->getTable('menuadmin')} (
  `menuadmin_id` int(11) unsigned NOT NULL auto_increment,
  `pid` int(11),
  `store_id` int(11),
  `region` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `type` smallint(6) NOT NULL default '1',
  `link` varchar(255) NOT NULL default '',
  `cssclass` varchar(255) NOT NULL default '',
  `target` varchar(255) NOT NULL default '',
  `position` int(11),
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`menuadmin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();