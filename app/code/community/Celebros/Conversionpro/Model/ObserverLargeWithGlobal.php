<?php
ini_set('memory_limit','1024M');
set_time_limit(7200);
ini_set('max_execution_time',7200);
ini_set('display_errors', 1);
ini_set('output_buffering', 0);

/**
 * Celebros Qwiser - Magento Extension
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish correct extension functionality.
 * If you wish to customize it, please contact Celebros.
 *
 * @category    Celebros
 * @package     Celebros_Salesperson
 * @author		Omniscience Co. - Dan Aharon-Shalom (email: dan@omniscience.co.il)
 *
 */
 
//include_once("createZip.php");
class Celebros_Salesperson_Model_ObserverLarge extends Celebros_Salesperson_Model_Exporter
{
	protected static $_profilingResults;
	protected $bExportProductLink = true;
	protected $_product_entity_type_id = null;
	protected $_category_entity_type_id = null;
	protected $prod_file_name="source_products";
	protected $isLogProfiler=true;
	protected $_categoriesForStore = false;

	function __construct() {
		$this->_read=Mage::getSingleton('core/resource')->getConnection('core_read');
		$this->_product_entity_type_id = $this->get_product_entity_type_id();
		$this->_category_entity_type_id = $this->get_category_entity_type_id();
	}
	
	private function logProfiler($msg, $isSpaceLine=false)
	{
		if (!($this->isLogProfiler))
			return;

		Mage::log(date("Y-m-d, H:i:s:: ").$msg, null, 'celebros.log',true);

		if ($isSpaceLine)
			Mage::log('', null, 'celebros.log',true);
	}
	
	public function isGlobalExport()
	{
		return Mage::app()->getConfig('salesperson/export_settings/global_export');
	}
	
	public function export_celebros() {
		//self::startProfiling(__FUNCTION__);
		
		if ($this->isGlobalExport()) {
			
			$this->_fStore_id = 0;
			$this->export_config($this->_fStore_id);
			
			$this->export_tables();
			
			foreach (Mage::app()->getStores() as $store) {
				if ($store->getConfig('salesperson/export_settings/export_enabled')) {
				
					$this->_fStore_id = $store->getId();
					$this->export_config($this->_fStore_id);
					$this->_categoriesForStore = false;
				
					$this->export_products();
					
					$zipFilePath = $this->zipLargeFiles();

					echo "<BR>Checking FTP upload<BR>";
					echo "=======================<BR>";
					echo "Memory usage: ".memory_get_usage(true)."<BR>";

					if($this->_fType==="ftp" && $this->_bUpload)
					{
						echo "Uploading export file<BR>";

						$this->logProfiler('Uploading export file');
						$this->logProfiler('---------------------',true);

						$ftpRes = $this->ftpfile($zipFilePath);
						if(!$ftpRes)
						{
							echo "Could not upload " . $zipFilePath . ' to ftp';
							$this->logProfiler('FTP upload ERROR',true);
						}
						else
							$this->logProfiler('FTP upload success',true);
					}
					else
					{
						echo "No need to upload export file<BR>";
						$this->logProfiler('No need to upload export file',true);
					}
					
				}
			}
			
			return;
		}
		
		foreach (Mage::app()->getStores() as $store) {
			if ($store->getConfig('salesperson/export_settings/export_enabled')) {
			
				$this->_fStore_id = $store->getId();
				$this->export_config($this->_fStore_id);
				$this->_categoriesForStore = false;
				
				$this->logProfiler('===============');
				$this->logProfiler('Starting Export');
				$this->logProfiler('===============',true);
				$this->logProfiler('Mem usage: '.memory_get_usage(true),true);

				
				echo "<BR>".date('Y/m/d H:i:s');
				echo "<BR>Starting export<BR>";
				echo "===============<BR>";
				echo "Memory usage: ".memory_get_usage(true)."<BR>";

				echo "<BR>Exporting tables<BR>";
				echo "================<BR>";
				echo "Memory usage: ".memory_get_usage(true)."<BR>";
				echo str_repeat(" ", 4096);
				
				$this->logProfiler('Exporting tables');
				$this->logProfiler('----------------',true);

				$this->export_tables();

				
				echo "<BR>Exporting products<BR>";
				echo "==================<BR>";
				echo "Memory usage: ".memory_get_usage(true)."<BR>";
				echo str_repeat(" ", 4096);
				
				$this->logProfiler('Writing products file');
				$this->logProfiler('---------------------',true);
				
				$this->export_products();
				
				echo "<BR>Creating ZIP file<BR>";
				echo "=================<BR>";
				echo "Memory usage: ".memory_get_usage(true)."<BR>";
				echo str_repeat(" ", 4096);
				
				$this->logProfiler('Creating ZIP file');
				$this->logProfiler('-----------------',true);

				$zipFilePath = $this->zipLargeFiles();

				echo "<BR>Checking FTP upload<BR>";
				echo "=======================<BR>";
				echo "Memory usage: ".memory_get_usage(true)."<BR>";

				if($this->_fType==="ftp" && $this->_bUpload)
				{
					echo "Uploading export file<BR>";

					$this->logProfiler('Uploading export file');
					$this->logProfiler('---------------------',true);

					$ftpRes = $this->ftpfile($zipFilePath);
					if(!$ftpRes)
					{
						echo "Could not upload " . $zipFilePath . ' to ftp';
						$this->logProfiler('FTP upload ERROR',true);
					}
					else
						$this->logProfiler('FTP upload success',true);
				}
				else
				{
					echo "No need to upload export file<BR>";
					$this->logProfiler('No need to upload export file',true);
				}
				
				echo "<BR>Finished<BR>";
				echo "========<BR>";
				echo "Memory usage: ".memory_get_usage(true)."<BR>";
				echo "Memory peek usage: ".memory_get_peak_usage(true)."<BR>";

				echo "<BR><BR>".date('Y/m/d H:i:s');
				echo str_repeat(" ", 4096);

				$this->logProfiler('Mem usage: '.memory_get_usage(true),true);
				$this->logProfiler('Mem peek usage: '.memory_get_peak_usage(true),true);
				
				//self::stopProfiling(__FUNCTION__);
				
				//$html = self::getProfilingResultsString();
				//$this->log_profiling_results($html);
				//echo $html;
			}
		}
	}
	
	protected function log_profiling_results($html) {
		$fh = $this->create_file("profiling_results.log", "html");
		$this->write_to_file($html, $fh);
	}
	
	protected function get_status_attribute_id() {
		$table = $this->getTableName("eav_attribute");
		$sql = "SELECT attribute_id
		FROM {$table}
		WHERE entity_type_id ={$this->_product_entity_type_id} AND attribute_code='status'";
		return $this->_read->fetchOne($sql);
	}
	
	protected function get_product_entity_type_id() {
		$table = $this->getTableName("eav_entity_type");
		$sql = "SELECT entity_type_id
		FROM {$table}
		WHERE entity_type_code='catalog_product'";
		return $this->_read->fetchOne($sql);
	}
	
	protected function get_category_entity_type_id() {
		$table = $this->getTableName("eav_entity_type");
		$sql = "SELECT entity_type_id
		FROM {$table}
		WHERE entity_type_code='catalog_category'";
		return $this->_read->fetchOne($sql);
	}
	
	protected function get_visibility_attribute_id() {
		$table = $this->getTableName("eav_attribute");
		$sql = "SELECT attribute_id
		FROM {$table}
		WHERE entity_type_id ={$this->_product_entity_type_id} AND attribute_code='visibility'";
		return $this->_read->fetchOne($sql);
	}
	
	protected function get_category_name_attribute_id() {
		$table = $this->getTableName("eav_attribute");
		$sql = "SELECT attribute_id
		FROM {$table}
		WHERE entity_type_id ={$this->_category_entity_type_id} AND attribute_code='name'";
		return $this->_read->fetchOne($sql);
	}

	protected function get_category_is_active_attribute_id() {
		$table = $this->getTableName("eav_attribute");
		$sql = "SELECT attribute_id
		FROM {$table}
		WHERE entity_type_id ={$this->_category_entity_type_id} AND attribute_code='is_active'";
		return $this->_read->fetchOne($sql);
	}
	
	protected function export_tables() {
		//self::startProfiling(__FUNCTION__);
		$read = Mage::getModel('core/resource')->getConnection('core_read');
		
		$table = $this->getTableName("catalog_eav_attribute");
		$sql = $read->select()
			->from($table,
					array('attribute_id', 'is_searchable', 'is_filterable', 'is_comparable'));
		
		if ($this->isGlobalExport()) {
			$sql->columns('is_global');
		}
		
		$this->export_table($sql, "catalog_eav_attribute");
		
		$table = $this->getTableName("eav_attribute");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$sql = $read->select()
			->from($table,
				array('attribute_id', 'attribute_code', 'backend_type', 'frontend_input'))
			->where('entity_type_id = ?', $this->_product_entity_type_id);
		$this->export_attributes_table($sql, "attributes_lookup");
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_product_entity");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$categories = implode(',', $this->_getAllCategoriesForStore());
		$sql = $read->select()
			->from($table,
				array('entity_id', 'type_id', 'sku'))
			->where('`catalog_product_entity`.`entity_type_id` = ?', $this->_product_entity_type_id);
		if (!$this->isGlobalExport()) {
			$sql->joinLeft(	'catalog_category_product', 
						"`catalog_product_entity`.`entity_id` = `catalog_category_product`.`product_id`",
						array())
			->where("`catalog_category_product`.`category_id` IN ({$categories})")
			->group('entity_id');
		}
		$this->export_table($sql, $table);
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);
		

		$table = $this->getTableName("catalog_product_entity_int");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$status_attribute_id = $this->get_status_attribute_id();
		
		$sql = $read->select()
		
			->from($table, 
				array('entity_id'))
			->where('entity_type_id = ?', $this->_product_entity_type_id)
			
			->where('attribute_id = ?', $status_attribute_id)
			->where('value = ?', '2');
		
		if ($this->isGlobalExport()) {
			$sql->columns('store_id');
			$this->export_table($sql, "disabled_products");
		} else {
			$this->export_product_att_table($sql, "disabled_products");
		}
		
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);
		
		
		$table = $this->getTableName("catalog_product_entity_int");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$visibility_attribute_id = $this->get_visibility_attribute_id();
		
		$sql = $read->select()
			->distinct()
			->from($table, 
				array('entity_id'))
			->where('entity_type_id = ?', $this->_product_entity_type_id)
			->where('attribute_id = ?', $visibility_attribute_id)
			->where('value = ?', '1');
		
		if ($this->isGlobalExport()) {
			$sql->columns('store_id');
			$this->export_table($sql, "not_visible_individually_products");
		} else {
			$this->export_product_att_table($sql, "not_visible_individually_products");		
		}
		
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);
		

		$table = $this->getTableName("catalog_product_entity_varchar");		
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		
		$sql = $read->select()
			->from($table, 
				array('entity_id', 'value', 'attribute_id'))
			->where('entity_type_id = ?', $this->_product_entity_type_id);
		
		if ($this->isGlobalExport()) {
			$sql->columns('store_id');
			$this->export_table($sql, $table);
		} else {
			$this->export_product_att_table($sql, $table);
		}
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);
		
		
		$table = $this->getTableName("catalog_product_entity_int");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$sql = $read->select()
			->from($table,
					array('entity_id', 'value', 'attribute_id'))
			->where('entity_type_id = ?', $this->_product_entity_type_id);
		
		if ($this->isGlobalExport()) {
			$sql->columns('store_id');
			$this->export_table($sql, $table);
		} else {		
			$this->export_product_att_table($sql, $table);
		}
		
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);
		

		$table = $this->getTableName("catalog_product_entity_text");		
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$sql = $read->select()
			->from($table,
					array('entity_id', 'value', 'attribute_id'))
			->where('entity_type_id = ?', $this->_product_entity_type_id);
			
		if ($this->isGlobalExport()) {
			$sql->columns('store_id');
			$this->export_table($sql, $table);
		} else {	
			$this->export_product_att_table($sql, $table);
		}
		
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_product_entity_decimal");		
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$sql = $read->select()
			->from($table,
					array('entity_id', 'value', 'attribute_id'))
			->where('entity_type_id = ?', $this->_product_entity_type_id);
		
		if ($this->isGlobalExport()) {
			$sql->columns('store_id');
			$this->export_table($sql, $table);
		} else {
			$this->export_product_att_table($sql, $table);
		}
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);

		
		$table = $this->getTableName("catalog_product_entity_datetime");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$sql = $read->select()
			->from($table,
					array('entity_id', 'value', 'attribute_id'))
			->where('entity_type_id = ?', $this->_product_entity_type_id);
		
		if ($this->isGlobalExport()) {
			$sql->columns('store_id');
			$this->export_table($sql, $table);
		} else {
			$this->export_product_att_table($sql, $table);
		}
		
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("eav_attribute_option_value");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$sql = $read->select()
			->from($table,
					array('option_id', 'value'));
		
		if ($this->isGlobalExport()) {
			$sql->columns('store_id');
			$this->export_table($sql, $table);
		} else {
			$this->export_table($sql, $table, array('option_id'));
		}
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);
		
		
		$table = $this->getTableName("eav_attribute_option");		
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$sql = $read->select()
			->from($table,
				array('option_id', 'attribute_id'));
				
		$this->export_table($sql, $table);
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);
		

		$table = $this->getTableName("catalog_category_product");		
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$sql = $read->select()
			->from($table,
					array('category_id', 'product_id'));
					
		if (!$this->isGlobalExport()) {
			$categories = implode(',', $this->_getAllCategoriesForStore());
			$sql->where("`category_id` IN ({$categories})");
		}
		
		$this->export_table($sql, $table);
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);
	
		
		$table = $this->getTableName("catalog_category_entity");		
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$sql = $read->select()
			->from($table,
					array('entity_id', 'parent_id', 'path'));
		
		if (!$this->isGlobalExport()) {
			$categories = implode(',', $this->_getAllCategoriesForStore());
			$sql->where("`category_id` IN ({$categories})");
		}
		
		$this->export_table($sql, $table);
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);

		
		$table = $this->getTableName("catalog_category_entity_varchar");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$name_attribute_id = $this->get_category_name_attribute_id();
		$sql = $read->select()
			->from($table,
					array('entity_id', 'value'))
			->where('attribute_id = ?', $name_attribute_id);
		
		if (!$this->isGlobalExport()) {
			$categories = implode(',', $this->_getAllCategoriesForStore());
			$sql->where("`category_id` IN ({$categories})");
			$this->export_table($sql, "category_lookup", array('entity_id'));
		} else {
			$sql->columns('store_id');
			$this->export_table($sql, "category_lookup");
		}
		
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);
		
		
		$table = $this->getTableName("catalog_category_entity_int");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$is_active_attribute_id = $this->get_category_is_active_attribute_id();
		$sql = $read->select()
			->from($table,
					array('entity_id'))
			->where('attribute_id = ?', $is_active_attribute_id)
			->where('value = 0')
			->where('entity_type_id = ?', $this->_category_entity_type_id);
		
		if (!$this->isGlobalExport()) {
			$categories = implode(',', $this->_getAllCategoriesForStore());
			$sql->where("`category_id` IN ({$categories})");
			$this->export_table($sql, "disabled_categories", array('entity_id'));
		} else {
			$sql->columns('store_id');
			$this->export_table($sql, "disabled_categories");
		}

		
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_product_super_link");		
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$sql = $read->select()
			->from($table,
					array('product_id', 'parent_id'));
					
		$this->export_table($sql, $table);
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);

		
		$table = $this->getTableName("catalog_product_super_attribute");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$sql = $read->select()
			->from($table,
					array('product_id', 'attribute_id'));
					
		$this->export_table($sql, $table);
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);		

		
		$table = $this->getTableName("salesperson_mapping");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$sql = $read->select()
			->from($table,
					array('xml_field', 'code_field'));
					
		$this->export_table($sql, $table);
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);		
		
		//self::stopProfiling(__FUNCTION__);
	}
	
	protected function export_table_rows($sql, $fields, $fh)
	{
		$str = "";
		$query = $sql->query();
		$rowCount=0;
		$processedRows = array();
		
		while ($row = $query->fetch()) {
		
			//$this->logProfiler("Block read start ({$this->_limit} products");
			//$this->logProfiler('Mem usage: '.memory_get_usage(true));

			//remember all the rows we're processing now, so we won't go over them again when we iterate over the default store.
			if (isset($fields)) {
				$concatenatedRow = '';
				foreach ($fields as $field) {
					$concatenatedRow .= $row[$field] . '-';
				}
				$processedRows[] = substr($concatenatedRow, 0, -1);
			}
			
			$str.= "^" . implode("^	^",$row) . "^" . "\r\n";
			$rowCount++;
			
			if (($rowCount%1000)==0)
			{
				//$this->logProfiler("Write block start");
				$this->write_to_file($str , $fh);
				//$this->logProfiler("Write block end");
				$str="";
			}
		}
		
		if (($rowCount%1000)!=0)
		{
			$this->logProfiler("Write last block start");
			$this->write_to_file($str , $fh);
			$this->logProfiler("Write last block end");
		}
		
		$this->logProfiler("Total rows: {$rowCount}");
		
		return $processedRows;
	}
	
	protected function write_headers($sql, $fh)
	{	
		$header = "^";
		$columns = $sql->getPart('columns');
		foreach ($columns as $column) {
			if ($column[1] != '*') {
				$fields[] = $column[1];
			}
		}
		$header .= implode("^	^", $fields);
		$header .= "^" . "\r\n";
		$this->write_to_file($header, $fh);
		
		return $columns;
	}
	
	/* This is a separate function because of two changes from export_table(): 
	 * 1. We're adding another column header at the start for the frontend_label (which isn't selected in the first run)
	 * 2. On the first run, we've added a join statement to get the labels from eav_attribute_label. The second run covers all
	 * cases where eav_attribute_label didn't have a value for a specific attribute.
	 */
	protected function export_attributes_table($sql, $filename)
	{
		$fh = $this->create_file($filename);
		
		//Adding another column header before the call to write_headers().
		$columns = $sql->getPart('columns');
		$sql->columns('frontend_label');
		
		if ($this->isGlobalExport()) {
			$sql->columns('store_id');
		}
		
		$this->write_headers($sql, $fh);
		$sql->setPart('columns', $columns);
	
		$sql->limit(100000000, 0);

		//Preparing the select object for the second query.
		$secondSql = clone($sql);
		
		//Adding a join statement to the first run alone, to get labels from eav_attribute_label.
		$table = $sql->getPart('from');
		$table = array_shift($table);
		
		if ($this->isGlobalExport()) {
			$sql->joinLeft('eav_attribute_label', 
					"{$table['tableName']}.`attribute_id` = `eav_attribute_label`.`attribute_id`",
					array('value', 'store_id'))
				->where("`eav_attribute_label`.`value` IS NOT NULL");
		} else {
			$sql->joinLeft('eav_attribute_label', 
					"{$table['tableName']}.`attribute_id` = `eav_attribute_label`.`attribute_id`
					AND `eav_attribute_label`.`store_id` = {$this->_fStore_id}",
					array('value'))
				->where("`eav_attribute_label`.`value` IS NOT NULL")
				->group('attribute_id');
		}
		
		//Process the rows that are covered by eav_attribute_label.
		$processedRows = $this->export_table_rows($sql, array('attribute_id'), $fh);
		
		//run a second time with only ids that are not in the list from the first run.
		$secondSql->columns('frontend_label');
		if (count($processedRows) && !$this->isGlobalExport()) {
			$secondSql->where("`attribute_id` NOT IN (?)", $processedRows);
		}
		
		//We're not using export_table_rows(), so we'll be able to add the admin store view at the end of each row.
		$str = "";
		$query = $secondSql->query();
		$rowCount=0;
		
		while ($row = $query->fetch()) {
			
			$str.= "^" . implode("^	^",$row) . "^	^". "0" . "^" . "\r\n";
			$rowCount++;
			
			if (($rowCount%1000)==0)
			{
				//$this->logProfiler("Write block start");
				$this->write_to_file($str , $fh);
				//$this->logProfiler("Write block end");
				$str="";
			}
		}
		
		if (($rowCount%1000)!=0)
		{
			$this->logProfiler("Write last block start");
			$this->write_to_file($str , $fh);
			$this->logProfiler("Write last block end");
		}
		
		$this->logProfiler("Total rows: {$rowCount}");
		
		fclose($fh);
		//self::stopProfiling(__FUNCTION__. "({$filename})");
	}
	
	protected function export_table($sql, $filename, $main_fields = null)
	{
		$fh = $this->create_file($filename);
		
		$this->write_headers($sql, $fh);
		
		$sql->limit(100000000, 0);
		
		//This part is only for tables that should be run twice - once with the store view, and again with the default.
		if (isset($main_fields)) {
			//preparing the query for the second run on the default store view.
			$secondSql = clone($sql);

			//On the first run, we'll only get the current store view.
			$sql->where('store_id = ?', $this->_fStore_id);
		}
		
		//Run the actual process of getting the rows and inserting them to the file,
		// and output the list of rows you covered to $processedRows.
		$processedRows = $this->export_table_rows($sql, $main_fields, $fh);

		//This part is only for tables that should be run twice - once with the store view, and again with the default.
		if (isset($main_fields)) {
			//Specifying the default store view.
			$secondSql->where('store_id = 0');
			
			//Only add the where statement in case items were found in the first run.
			if (count($processedRows)) {
				$concat_fields = implode('-', $main_fields);
				$secondSql->where("CONCAT({$concat_fields}) NOT IN (?)", $processedRows);
			}
			
			//Run the actual process of getting each row again, this time selecting rows with the default store view.
			$this->export_table_rows($secondSql, null, $fh);
		}
		
		fclose($fh);
		//self::stopProfiling(__FUNCTION__. "({$filename})");
	}
	
	/*
	 * This version of the export_table function is meant for entity attribute tables, that have store view specific values.
	 * Differences:
	 * 1. We check whether the current store view has any categories assigned, and return nothing if it does not.
	 * 2. We've added a join statement to only get rows that correspond to products that are assigned to categories that are 
	 * under the current store view.
	 * 3. Before running export_table_rows() for the first time, we execute the query, and withdraw a list of rows that will
	 * be covered once the first run is complete. We then use that list in to exclude those rows from the second run. This is
	 * essential because we have to include some columns (entity_id, attribute_id) that might not be in the select statement.
	 */
	protected function export_product_att_table($sql, $filename) {
		
		$fh = $this->create_file($filename);

		$columns = $this->write_headers($sql, $fh);
		
		$sql->limit(100000000, 0);							
			
		//Get Relevant Categories for the query.
		$categoriesForStore = $this->_getCategoriesForStore();
		
		//Don't run the query at all if no categories were found to match the current store view.
		if (!$categoriesForStore || !count($categoriesForStore)) {
			$this->logProfiler("Total rows: 0");
		
			fclose($fh);
			return;
		}
	
		//Only get products that match a category in the current store view.
		$table = $sql->getPart('from');
		$table = array_shift($table);
		$sql->joinLeft(	'catalog_category_product', 
						"{$table['tableName']}.`entity_id` = `catalog_category_product`.`product_id`",
						array())
			->where("`catalog_category_product`.`category_id` IN ({$categoriesForStore})")
			->group('value_id');
			
		$secondSql = clone($sql);

		$sql->where('`store_id` = ?', $this->_fStore_id);
		
		//Get list of rows with this specific store view, to exclude when running on the default store view.
		$sql->columns('entity_id');
		$sql->columns('attribute_id');
		$query = $sql->query();
		$processedRows = array();
		while ($row = $query->fetch()) {
			$processedRows[] = $row['attribute_id'] . '-' . $row['entity_id'];
		}
		$sql->setPart('columns', $columns);

		//Run the query on each row and save results to the file.
		$this->export_table_rows($sql, null, $fh);

		//Prepare the second query.
		$secondSql->where('store_id = 0');
		if (count($processedRows)) {
			$secondSql->where("CONCAT(`attribute_id`, '-', `entity_id`) NOT IN (?)", $processedRows);
		}
		
		//Run for the second time, now with the default store view.
		$this->export_table_rows($secondSql, null, $fh);
		
		fclose($fh);
		//self::stopProfiling(__FUNCTION__. "({$filename})");
	}
	
	protected function create_file($name, $ext = "txt") {
		//self::startProfiling(__FUNCTION__);
		if (!is_dir($this->_fPath)) $dir=@mkdir($this->_fPath,0777,true);
		$filePath = $this->_fPath . DIRECTORY_SEPARATOR . $name . "." . $ext;
		
		//if (file_exists($filePath)) unlink($filePath);
		$fh = fopen($filePath, 'ab');
		//self::stopProfiling(__FUNCTION__);
		return $fh;
	}
	
	protected function write_to_file($str, $fh){
		//self::startProfiling(__FUNCTION__);
		fwrite($fh, $str);

		//self::stopProfiling(__FUNCTION__);
	}
	
	public function zipLargeFiles() {
		//self::startProfiling(__FUNCTION__);
		
		$out = false;
		$zipPath = $this->_fPath . DIRECTORY_SEPARATOR . "products_file.zip";//$this->_fileNameZip;
		
		$dh=opendir($this->_fPath);
		$filesToZip = array();
		while(($item=readdir($dh)) !== false && !is_null($item)){
			$filePath = $this->_fPath . DIRECTORY_SEPARATOR . $item;
			$ext = pathinfo($filePath, PATHINFO_EXTENSION);
			if(is_file($filePath) && ($ext == "txt" || $ext == "log")) {
				$filesToZip[] = $filePath;
			}
		}
		
		for($i=0; $i < count($filesToZip); $i++) {
			$filePath = $filesToZip[$i];
			$out = $this->zipLargeFile($filePath, $zipPath);
		}

		//self::stopProfiling(__FUNCTION__);
		return $out ? $zipPath : false;
	}
	
	public function zipLargeFile($filePath, $zipPath)
	{
		//self::startProfiling(__FUNCTION__);
		
		$out = false;
		
		$zip = new ZipArchive();
		if ($zip->open($zipPath, ZipArchive::CREATE) == true) {
			$fileName = basename($filePath);
			$out = $zip->addFile($filePath, basename($filePath));
			if(!$out) throw new  Exception("Could not add file '{$fileName}' to_zip_file");
			$zip->close();
			$ext = pathinfo($fileName, PATHINFO_EXTENSION);
			if($ext != "log") unlink($filePath);
		}
		else
		{
			throw new  Exception("Could not create zip file");
		}
		
		//self::stopProfiling(__FUNCTION__);
		return $out;
	}
	
	protected function _getCategoriesForStore()
	{
		if (!$this->_categoriesForStore) {
			$rootCategoryId = $this->_fStore->getRootCategoryId();
			$rootCategory = Mage::getModel('catalog/category')->load($rootCategoryId);
			$rootResource = $rootCategory->getResource();
			$this->_categoriesForStore = implode(',', $rootResource->getChildren($rootCategory));
		}
		return $this->_categoriesForStore;
	}
	
	/*
	 * This function gets root categories too, as well as disabled categories.
	 * We've left these in so as not to create holes in the tables export.
	 */
	protected function _getAllCategoriesForStore()
	{
		$read = Mage::getModel('core/resource')->getConnection('core_read');
		$sql2 = $read->select()
			->from('catalog_category_entity',
					array('entity_id', 'path'));
					
		$results = $read->fetchPairs($sql2);
		$rootCategoryId = $this->_fStore->getRootCategoryId();
		$categories = array();
		foreach ($results as $entity_id => $path) {
			$path = explode('/', $path);
			if (count($path) > 1) {
				if ($path[1] == $rootCategoryId) {
					$categories[] = $entity_id;
				}
			} else {
				$categories[] = $entity_id;
			}
		}
		
		return $categories;
	}
	
	protected function is_process_running($PID)
	{
		exec("ps $PID", $ProcessState);
		return(count($ProcessState) >= 2);
	}

	protected function export_products()
	{
		echo "Begining products export<BR>Memory usage: ".memory_get_usage(true)."<BR>";
		$startTime = time();
	
		$fh = $this->create_file($this->prod_file_name);
		if (!$fh) {
			$this->comments_style('error','Could not create the file in ' . $this->_fPath . DIRECTORY_SEPARATOR . $this->prod_file_name . ' path','problem with file');
			$this->logProfiler('Could not create the file in ' . $this->_fPath . DIRECTORY_SEPARATOR . $this->prod_file_name . ' path');
			return;
		}
		
		$fields = array("id", "price", "image_link", "thumbnail", "type_id", "sku");

		if($this->bExportProductLink) $fields[] = "link";

		foreach ($fields as $key => $field) {
			$fields[$key] = Mage::helper('salesperson/mapping')->getMapping($field);
		}
		
		$header = "^" . implode("^	^",$fields) . "^" . "\r\n";
		$this->write_to_file($header, $fh);
	
		// *********************************
	
		if (!$this->_getCategoriesForStore() || !count($this->_getCategoriesForStore())) {			
			fclose($fh);
			return;
		}
	
		$table = $this->getTableName("catalog_product_entity");
		$sql = "SELECT DISTINCT(entity_id), type_id, sku
				FROM {$table}
				LEFT JOIN (`catalog_category_product`)
					ON (`catalog_category_product`.`category_id` IN ({$this->_getCategoriesForStore()}))
				WHERE {$table}.entity_type_id ={$this->_product_entity_type_id}
					AND {$table}.`entity_id` = `catalog_category_product`.`product_id`";
	
		$stm = $this->_read->query($sql . " LIMIT 0, 100000000");

		$str='';
		$rows=$stm->fetchAll();
		$chunks = array_chunk($rows, Mage::helper('salesperson')->getExportChunkSize());
		$i = 1;
		$pids = array();
		$finished = array();
		
		foreach ($chunks as $chunk) {
			if (count($pids) >= Mage::helper('salesperson')->getExportProcessLimit()) {
				$counter = 10;
				do {
					$counter--;
					if ($counter == 0) break;
					sleep(1);
					$state = true;
					foreach ($pids as $key => $pid) {
						if (!$this->is_process_running($pid)) {
							$state = false;
							$finished[] = $key;
							unset($pids[$key]);
						}
					}
				} while ($state);
			}
			Mage::getModel('salesperson/cache')->setName('export_chunk_'.$i)->setContent(json_encode($chunk))->save();
			$pids[$i] = (int)shell_exec('nohup php ' . Mage::getModuleDir('', 'Celebros_Salesperson') . '/Model/processChunkCol.php '.$i.' '.$this->_fStore_id.' > /dev/null & echo $!');
			if (!$pids[$i]) {
				$this->comments_style('error','Could not create a new process.','problem with process');
				$this->logProfiler('Failed creating a new system process for export parsing.');
				return;
			}
			$i++;
		}

		do {
			foreach ($pids as $key => $pid) {
				if (!$this->is_process_running($pid)) {
					$finished[] = $key;
					unset($pids[$key]);
				}
			}
			sleep(1);
		} while (count($pids));
		
		//Mage::log('running time is:');
		//Mage::log(time() - $startTime);
		//Mage::log(date('Y/m/d H:i:s'));

		$_fPath = Mage::app()->getStore(0)->getConfig('salesperson/export_settings/path').'/'.$this->_fStore->getWebsite()->getCode().'/'.$this->_fStore->getCode();
		if (!is_dir($_fPath)) $dir=@mkdir($_fPath,0777,true);
		if (!is_dir($_fPath)) {
			$this->comments_style('error','Could not create the directory in ' . $_fPath . ' path','problem with dir');
			$this->logProfiler('Failed creating a directory at: '. $_fPath);
			return;
		}
		foreach ($finished as $key) {
			$filePath = $_fPath . '/' . 'export_chunk_' . $key . "." . 'txt';
			fwrite($fh, file_get_contents($filePath));
			unlink($filePath);
		}
		
		fclose($fh);
		$this->logProfiler("Done.");
	}
	
	protected static function startProfiling($key) {
		if(!isset(self::$_profilingResults[$key])) {
			$profile = new stdClass();
			$profile->average =0 ;
			$profile->count = 0;
			$profile->max = 0;
			self::$_profilingResults[$key] = $profile;
		}
		$profile = self::$_profilingResults[$key];
		if(isset($profile->start) && $profile->start > $profile->end) throw new Exception("The start of profiling timer '{$key}' is called before the stop of it was called");
		$profile->start = (float) array_sum(explode(' ',microtime()));
	}
	
	protected static function stopProfiling($key) {
		if(!isset(self::$_profilingResults[$key])) throw new Exception("The stop of profiling timer '{$key}' was called while the start was never declared");
		
		$profile = self::$_profilingResults[$key];
		if($profile->start == -1) throw new Exception("The start time of '{$key}' profiling is -1");
		
		$profile->end = (float) array_sum(explode(' ',microtime()));
		$duration = $profile->end - $profile->start;
		if($profile->max < $duration) $profile->max = $duration;
		
		$profile->average = ($profile->average * $profile->count + $duration)/($profile->count +1);
		$profile->count++;
	}
	
	protected static function getProfilingResultsString() {
		$html = "";
		if(count(self::$_profilingResults)) {
			$html.= "In sec:";
			$html.=  '<table border="1">';
			$html.=  "<tr><th>Timer</th><th>Total</th><th>Average</th><th>Count</th><th>Peak</th></tr>";
			foreach(self::$_profilingResults as $key =>$profile) {
				$total = $profile->average * $profile->count;
				$html.=  "<tr><td>{$key}</td><td>{$total}</td><td>{$profile->average}</td><td>{$profile->count}</td><td>{$profile->max}</td></tr>";
			}
			$html.=  "</table>";
		}
		
		$html.= 'PHP Memory peak usage: ' . memory_get_peak_usage();
		
		return $html;
	}

}