<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Db extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 * All tables that needs to be mapped
	 *
	 * @var array
	 */
	protected $_tablesToMap = array('commentmeta', 'comments', 'links', 'options', 'postmeta', 'posts', 'terms', 'term_relationships', 'term_taxonomy', 'usermeta', 'users');

	/**
	 * Map WordPress table names
	 *
	 * @return Fishpig_Wordpress_Helper_Db
	 */
	protected function _mapTables()
	{
		if (!$this->_isCached('wp_tables_mapped')) {

			foreach($this->_tablesToMap as $table) {
				Mage::getSingleton('core/resource')->setMappedTableName($table, $this->getTablePrefix() . $table);
			}
		
			Mage::dispatchEvent('wordpress_database_map_tables');
			
			$this->_cache('wp_tables_mapped', true);
		}

		return $this;
	}

	/**
	 * Map WordPress table names
	 * This contains tables that relate to plugins and requires a valid DB connection to
	 * test whether they should be added
	 *
	 * @return Fishpig_Wordpress_Helper_Db
	 */
	protected function _mapExtraTables()
	{
		if (!$this->_isCached('wp_extra_tables_mapped')) {
			$tables = array();

			if (Mage::helper('wordpress')->isPluginEnabled('yarpp')) {
				$tables[] = 'yarpp_related_cache';
				$tables[] = 'yarpp_keyword_cache';
			}

			if (count($tables) > 0) {
				foreach($tables as $table) {
					Mage::getSingleton('core/resource')->setMappedTableName($table, $this->getTablePrefix() . $table);
				}
			}
			
			$this->_cache('wp_extra_tables_mapped', true);
		}
		
		return $this;
	}
	
	/*
	 * Returns the table prefix used by Wordpress
	 *
	 * @return string
	 */
	public function getTablePrefix()
	{
		return $this->getConfigValue('wordpress/database/table_prefix');
	}
	
	/**
	 * Retrieve an entities table name
	 *
	 * @param string $table
	 * @return string
	 */
	public function getTableName($table)
	{
		return Mage::getSingleton('core/resource')->getTableName($table);
	}
	
	/**
	  * Returns true if it is possible to query the DB
	  *
	  * @param bool $graceful
	  * @return true
	  */
	public function isQueryable()
	{
		if (!$this->_isCached('is_queryable')) {
			if ($adapter = $this->getReadAdapter()) {
				$select = $adapter->select()
					->from($this->getTableName('wordpress/post'), 'ID')
					->limit(1);
				
				try {
					$adapter->fetchOne($select);
					$this->_cache('is_queryable', true);
				}
				catch (Exception $e) {
					$this->_cache('is_queryable', false);
					$this->log($e->getMessage());
				}
			}
			else {
				$this->_cache('is_queryable', false);
			}
		}
		
		return $this->_cached('is_queryable');
	}
	
	/**
	 * Retriev the read adapter
	 *
	 * @return false|Varien_Db_Adapter_Pdo_Mysql
	 */	
	public function getReadAdapter()
	{
		if ($this->isConnected()) {
			if ($this->isSameDatabase()) {
				return Mage::getSingleton('core/resource')->getConnection('core_read');
			}
		
			return $this->_getWordPressAdapter();
		}
		
		return false;
	}
	
	/**
	 * Retriev the write adapter
	 *
	 * @return false|Varien_Db_Adapter_Pdo_Mysql
	 */
	public function getWriteAdapter()
	{
		if ($this->isConnected()) {
			if ($this->isSameDatabase()) {
				return Mage::getSingleton('core/resource')->getConnection('core_write');
			}
		
			return $this->_getWordPressAdapter();
		}
		
		return false;
	}
	
	/**
	 * Retrieve the WordPress database adapter
	 *
	 * @return false|Varien_Db_Adapter_Pdo_Mysql
	 */
	protected function _getWordPressAdapter()
	{
		if ($this->isConnected()) {
			return Mage::getSingleton('core/resource')->getConnection('wordpress');
		}
		
		return false;
	}

	/**
	 * Determine whether the DB connection is active
	 *
	 * @return bool|null
	 */
	public function isConnected()
	{
		if (!$this->_isCached('db_connected')) {
			$this->_connect();
		}
		
		return $this->_cached('db_connected');
	}

	/**
	 * Connect to the WordPress database
	 *
	 * @return bool
	 */
	public function connect()
	{
		if (!$this->_isCached('db_connected')) {
			$this->_connect();
		}
		
		return $this->isConnected();
	}
	
	/**
	 * Connect to the database
	 *
	 * @return bool
	 */
	protected function _connect()
	{
		$this->_cache('db_connected', false);
		$this->_mapTables();

		if ($this->isSameDatabase()) {
			$this->_cache('db_connected', true);
			$this->_cache('db_connected', $this->isQueryable());

			if ($this->_cached('db_connected')) {
				$this->_mapExtraTables();
			}
		}
		else if ($configs = $this->_getDatabaseDetails()) {

			try {
				$connection = Mage::getSingleton('core/resource')->createConnection('wordpress', 'pdo_mysql', $configs);
			
				if (!is_object($connection)) {
					/* Magento 1.3.2.4 */
					throw new Exception('Error connecting to the WordPress database');
				}
				
				$connection->getConnection();

				$this->_cache('db_connected', $connection->isConnected());
				$this->_cache('db_connected', $this->isQueryable());
				
				if ($this->_cached('db_connected')) {
					$this->_mapExtraTables();
				}
			}
			catch (Exception $e) {
				$this->log($e->getMessage());
				$this->_cache('db_connected', false);
			}
		}

		return $this->_cached('db_connected');
	}
	
	/**
	 * Retrieve an array of the database connection details
	 *
	 * @return array|false
	 */
	protected function _getDatabaseDetails()
	{
		$configs = array('model' => 'mysql4', 'active' => '1', 'host' => '', 'username' => '', 'password' => '', 'dbname' => '', 'charset' => 'utf8');
		
		foreach($configs as $key => $defaultValue) {
			if ($value = $this->getConfigValue('wordpress/database/' . $key)) {
				$configs[$key] = $value;
			}
		}

		foreach(array('username', 'password', 'dbname') as $field) {
			if (isset($configs[$field])) {
				$configs[$field] = Mage::helper('core')->decrypt($configs[$field]);
			}
		}
		
		if (isset($configs['host']) && $configs['host']) {
			return $configs;
		}
		
		return false;
	}
}
