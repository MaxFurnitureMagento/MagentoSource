<?php
class MageDevelopers_QtyPromotions_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup
{
    public function getDefaultEntities()
    {
        return array(
            'catalog_product' => array(
                'entity_model'      => 'catalog/product',
                'attribute_model'   => 'catalog/resource_eav_attribute',
                'table'             => 'catalog/product',
                'additional_attribute_table' => 'catalog/eav_attribute',
                'entity_attribute_collection' => 'catalog/product_attribute_collection',                
                'attributes'        => array(
                    'md_qty_promotions' => array(
                        'group'             => 'General',
                        'label'             => 'Stock Qty',
                        'type'              => 'varchar',
                        'input'             => 'text',
                        'default'           => '',
                        'class'             => '',
                        'backend'           => '',
                        'frontend'          => '',
                        'source'            => '',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'visible'           => false,
                        'required'          => false,
                        'user_defined'      => false,
                        'searchable'        => true,
                        'filterable'        => true,
                        'comparable'        => false,
                        'visible_on_front'  => true,
                        'visible_in_advanced_search' => false,
                        'unique'            => false
                    ),
               )
           ),
      );
    }
}