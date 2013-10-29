<?php
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('catalog/eav_attribute'), "solr_search_field_weight", "TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($installer->getTable('catalog/eav_attribute'), "solr_search_field_boost", "VARCHAR( 255 ) NOT NULL DEFAULT ''");
$installer->getConnection()->addColumn($installer->getTable('catalog/eav_attribute'), "solr_search_field_facet", "TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0'");

$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('webmods_solrsearch_logs')};
	
	CREATE TABLE {$this->getTable('webmods_solrsearch_logs')} (
      `logs_id` int(10) NOT NULL AUTO_INCREMENT,
      `store_id` int(5) NOT NULL DEFAULT '0',
      `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `logs_type` enum('INDEXEDFIELDS','DEFAULT','CHANGEDFIELDS','INDEXEDPRODUCT'),
      `value` int(10) DEFAULT NULL,
      PRIMARY KEY (`logs_id`, `store_id`, `value`),
      KEY `IDX_WEBMODS_SOLRSEARCH_LOGS_LOGS_ID` (`logs_id`),
      KEY `IDX_WEBMODS_SOLRSEARCH_LOGS_STORE_ID` (`store_id`),
      KEY `IDX_WEBMODS_SOLRSEARCH_LOGS_LOGS_TYPE` (`logs_type`),
      KEY `IDX_WEBMODS_SOLRSEARCH_LOGS_VALUE` (`value`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
     
    REPLACE INTO {$this->getTable('core_config_data')} (`scope`, `scope_id`, `path`, `value`) VALUES
	('default', 0, 'webmods_solrsearch/settings/solr_server_url', 'http://localhost:8080/solr/'),
	('default', 0, 'webmods_solrsearch/settings/solr_server_url_auth', '0'),
	('default', 0, 'webmods_solrsearch/settings/solr_server_url_auth_username', NULL),
	('default', 0, 'webmods_solrsearch/settings/solr_server_url_auth_password', NULL),
	('default', 0, 'webmods_solrsearch/settings/solr_quick_search_display_thumnail', '1'),
	('default', 0, 'webmods_solrsearch/settings/solr_quick_search_allow_filter', '1'),
	('default', 0, 'webmods_solrsearch/settings/solr_index_auto_when_product_save', '1'),
	('default', 0, 'webmods_solrsearch/settings/solr_search_in_category', '1'),
	('default', 0, 'webmods_solrsearch/settings/use_category_as_facet', '1'),
	('default', 0, 'webmods_solrsearch/settings/items_per_page', '32'),
	('default', 0, 'webmods_solrsearch/settings/items_per_commit', '10'),
	('default', 0, 'webmods_solrsearch/settings/use_ajax_result_page', '0'),
	('default', 0, 'webmods_solrsearch_indexes/english/label', 'English'),
	('default', 0, 'webmods_solrsearch_indexes/french/label', 'French'),
	('default', 0, 'webmods_solrsearch_indexes/polish/label', 'Polish'),
	('default', 0, 'webmods_solrsearch_indexes/dutch/label', 'Dutch'),
	('default', 0, 'webmods_solrsearch_indexes/german/label', 'German'),
	('default', 0, 'webmods_solrsearch_indexes/italian/label', 'Italian'),
	('default', 0, 'webmods_solrsearch/settings/allow_multiple_filter', '1'),
	('default', 0, 'webmods_solrsearch/settings/currency_position', '1');
  ");

$installer->endSetup();


//Uninstallationi scripts
/*
delete from core_config_data where `path` like 'webmods_solrsearch%';
delete from core_resource where `code` = 'solrsearch_setup';
*/