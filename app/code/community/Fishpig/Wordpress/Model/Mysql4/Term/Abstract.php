<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Model_Mysql4_Term_abstract extends Fishpig_Wordpress_Model_Mysql4_Abstract
{
	/**
	 * Custom load SQL to combine required tables
	 *
	 * @param string $field
	 * @param string|int $value
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function _getLoadSelect($field, $value, $object)
	{
		$select = $this->_getReadAdapter()->select()
			->from(array('main_table' => $this->getMainTable()));
		
		if (strpos($field, '.') !== false) {
			$select->where($field . '=?', $value);
		}
		else {
			$select->where("main_table.{$field}=?", $value);
		}
			
		$select->join(
			array('taxonomy' => $this->getTable('wordpress/term_taxonomy')),
			'`main_table`.`term_id` = `taxonomy`.`term_id`',
			array('term_taxonomy_id', 'taxonomy', 'description', 'count', 'parent')
		);
		
		if ($object->getTaxonomyType()) {
			$select->where('taxonomy.taxonomy=?', $object->getTaxonomyType());
		}

		return $select->limit(1);
	}
}
