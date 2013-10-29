<?php

$installer = $this;

$installer->startSetup();

$installer->run("

	DROP TABLE IF EXISTS {$this->getTable('conversionpro_cache')};
	CREATE TABLE {$this->getTable('conversionpro_cache')} (
	  `cache_id` int(11) NOT NULL auto_increment,
	  `name` varchar(255) NULL,
	  `content` longblob,
	  PRIMARY KEY (`cache_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE IF NOT EXISTS `{$this->getTable('conversionpro_mapping')}` (
      	`id` int(11) NOT NULL auto_increment,
      	`xml_field` VARCHAR(255) NULL, 
      	`code_field` VARCHAR(255),
      	PRIMARY KEY  (`id`),
		UNIQUE `CODE_FIELD` ( `code_field` )
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	INSERT IGNORE INTO `{$this->getTable('conversionpro_mapping')}`  (id, xml_field, code_field)
		VALUES 
		(null,'title','title'),
		(null, 'link', 'link'),
		(null, 'status','status'),
		(null, 'image_link','image_link'),
		(null, 'thumbnail_label','thumbnail_label'),
		(null, 'rating','rating'),
		(null, 'short_description','short_description'),
		(null, 'mag_id', 'id'),
		(null, 'visible', 'visible'),
		(null, 'store_id', 'store_id'),
		(null, 'is_in_stock', 'is_in_stock'),
		(null, 'sku', 'sku'),
		(null, 'category', 'category'),
		(null, 'websites', 'websites'),
		(null, 'news_from_date', 'news_from_date'),
		(null, 'news_to_date', 'news_to_date');

	INSERT INTO `{$this->getTable('dataflow_profile')}` (`profile_id`, `name`, `created_at`, `updated_at`, `actions_xml`, `gui_data`, `direction`, `entity_type`, `store_id`, `data_transfer`)
		VALUES
		(null, 'Conversion Pro Exporter', '2010-03-03 10:49:35', '2010-03-08 17:54:19',
		 '<action type=\"catalog/convert_adapter_product\" method=\"load\">
			<var name=\"store\"><![CDATA[0]]></var>
		 </action>

		<action type=\"conversionpro/convert_parser_product\" method=\"unparse\">
			<var name=\"store\"><![CDATA[0]]></var>
			<var name=\"url_field\"><![CDATA[0]]></var>
		</action>

		<action type=\"conversionpro/convert_mapper_column\" method=\"map\">
			<var name=\"map\">
				<map name=\"store_id\"><![CDATA[store_id]]></map>
				<map name=\"websites\"><![CDATA[websites]]></map>
				<map name=\"id\"><![CDATA[mag_id]]></map>
				<map name=\"name\"><![CDATA[title]]></map>
				<map name=\"price\"><![CDATA[price]]></map>
				<map name=\"rating\"><![CDATA[rating]]></map>
				<map name=\"url_path\"><![CDATA[link]]></map>
				<map name=\"thumbnail\"><![CDATA[image_link]]></map>
				<map name=\"category\"><![CDATA[category]]></map>
				<map name=\"type\"><![CDATA[type]]></map>
				<map name=\"weight\"><![CDATA[weight]]></map>
				<map name=\"manufacturer\"><![CDATA[brand]]></map>
				<map name=\"color\"><![CDATA[color]]></map>
				<map name=\"thumbnail_label\"><![CDATA[thumbnail_label]]></map>
				<map name=\"description\"><![CDATA[description]]></map>
				<map name=\"short_description\"><![CDATA[short_description]]></map>
				<map name=\"is_in_stock\"><![CDATA[is_in_stock]]></map>
				<map name=\"news_from_date\"><![CDATA[news_from_date]]></map>
				<map name=\"news_to_date\"><![CDATA[news_to_date]]></map>
				<map name=\"sku\"><![CDATA[sku]]></map>
				<map name=\"status\"><![CDATA[status]]></map>
			</var>
			<var name=\"_only_specified\">true</var>
		</action>
		
		<action type=\"conversionpro/convert_adapter_io\" method=\"save\">
			<var name=\"type\">file</var>
			<var name=\"path\">var/export</var>
			<var name=\"filename\"><![CDATA[products.txt]]></var>
		</action>',
		 '', NULL, '', 0, NULL);
");

//Setting the default export path according to the server's root address.
$resource = new Mage_Core_Model_Config();
$resource->saveConfig('conversionpro/export_settings/path', Mage::getBaseDir('base').'/var/conversionpro/export', 'default', 0);

$installer->endSetup(); 