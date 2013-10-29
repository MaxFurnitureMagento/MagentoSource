<?php
$installer = $this;

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup->addAttribute('catalog_category', 'home_position', array(
                        'type'              => 'int',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Position on Home Page',
                        'input'             => 'text',
                        'class'             => 'validate-digits',
                        'source'            => '',
                        'global'            => 0,
                        'visible'           => 1,
                        'required'          => 0,
                        'user_defined'      => 0,
                        'default'           => '',
                        'searchable'        => 0,
                        'filterable'        => 0,
                        'comparable'        => 0,
                        'visible_on_front'  => 1,
                        'unique'            => 0,
                        'position'          => 1,
                    ));

$setup->addAttribute('catalog_category', 'home_thumbnail', array(
                        'type'              => 'varchar',
                        'backend'           => 'catalog/category_attribute_backend_image',
                        'frontend'          => '',
                        'label'             => 'Home Thumbnail',
                        'input'             => 'image',
                        'class'             => '',
                        'source'            => '',
                        'global'            => 0,
                        'visible'           => 1,
                        'required'          => 0,
                        'user_defined'      => 0,
                        'default'           => '',
                        'searchable'        => 0,
                        'filterable'        => 0,
                        'comparable'        => 0,
                        'visible_on_front'  => 1,
                        'unique'            => 0,
                        'position'          => 1,
                    ));

$setup->addAttribute('catalog_category', 'show_categories', array(
                        'type'              => 'int',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Show Categories',
                        'input'             => 'select',
                        'class'             => '',
                        'source'            => 'catalog/product_attribute_source_boolean',
                        'global'            => 0,
                        'visible'           => 1,
                        'required'          => 0,
                        'user_defined'      => 0,
                        'default'           => '',
                        'searchable'        => 0,
                        'filterable'        => 0,
                        'comparable'        => 0,
                        'visible_on_front'  => 1,
                        'unique'            => 0,
                        'position'          => 1,
                    ));

$installer->endSetup();