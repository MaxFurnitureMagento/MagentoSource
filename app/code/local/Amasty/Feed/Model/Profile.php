<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/  
class Amasty_Feed_Model_Profile extends Mage_Core_Model_Abstract
{
    const STATE_READY    = 0;
    const STATE_WAITING  = 1;
    const STATE_PROGRESS = 2;
    const STATE_ERROR    = 3;
    
    const MAX_ERRORS = 2;
    
    const DEBUG_MODE = true;
    
    const TYPE_CSV = 0;
    const TYPE_XML = 1;
    const TYPE_TXT = 2;
    
    const DELIVERY_TYPE_DLD = 0;
    const DELIVERY_TYPE_FTP = 2;
    
    protected $_products = null;
    
    protected $_customFields = null;
    protected $_checkedAttr = array('is_in_stock');
    protected $_selectAttr = array('is_in_stock');
    protected $_options = array('is_in_stock' => array(0 => array('value' => '0', 'label' => 'out of stock'), 1 => array ('value' => '1', 'label' => 'in stock')));
    
    public function _construct()
    {    
        $this->_init('amfeed/profile');
    }
    
    public function generate()
    {
        $hasGenerated = false;
        $oldStore = Mage::app()->getStore();
        Mage::app()->setCurrentStore($this->getStoreId());
        
        if ($this->getStatus() == self::STATE_READY || $this->getStatus() == self::STATE_ERROR) {
            $this->_saveStatus(self::STATE_PROGRESS);
            // long operation
            $this->_beforeGenerate();
            
            $this->_generate(); 
            
            if ($this->getInfoTotal()) {
                $this->_saveStatus(self::STATE_WAITING);
            }
            else {
                $this->_saveStatus(self::STATE_READY); 
                $hasGenerated = true;   
            }
        } elseif ($this->getStatus() == self::STATE_PROGRESS) {
            if ($this->getInfoErrors() > self::MAX_ERRORS) {
                $this->_saveStatus(self::STATE_ERROR);
                //@todo send email notification for automatic mode
                throw new Exception(Mage::helper('amfeed')->__('The feed generation has failed, try to decrease the batch size and run in the manual mode.'));                    
            }
            $this->_saveStatus(self::STATE_PROGRESS, $this->getInfoErrors() + 1);
        } elseif ($this->getStatus() == self::STATE_WAITING) {
                $this->_saveStatus(self::STATE_PROGRESS);
                // long operation
                $this->_generate(); 
                
                if ($this->getInfoCnt() >= $this->getInfoTotal()) {
                    $this->_afterGenerate(); 
                    $this->_saveStatus(self::STATE_READY);
                    $hasGenerated = true;
                } else {
                    $this->_saveStatus(self::STATE_WAITING);
                }
        }
        
        Mage::app()->setCurrentStore($oldStore);               
        
        return $hasGenerated;    
    }
    
    public function getProducts()
    {
        if (is_null($this->_products)) {
            $this->_products = Mage::getModel('amfeed/product_collection')
                ->initByFeed($this);
        }
        
        return $this->_products;
    }
    
    public function getParentProducts($products){
        $ids = array();
        
        $ret = array();
        
        foreach($products as $product){
            if ($product->getTypeId() == 'simple'){
                 $parentId = $product->getParentId();
                 if ($parentId)
                    $ids[$parentId] = $parentId;
            }
        }
        
        if (count($ids) > 0) {
            $products = Mage::getModel('amfeed/product_collection')
                ->initByFeed($this);
            
//            $products->getSelect()->where('e.type_id in ("simple", "grouped", "configurable", "virtual", "downloadable")', null, 'OR'); //SKIP product type filter
            $products->getSelect()->reset(Zend_Db_Select::WHERE);
            
            $products->getSelect()->where('e.entity_id in (' . implode(', ', $ids) . ')');

            $ret = $products->getItems();
        }
        
        
        return $ret;
    }
    
    /**
     * Creates SQL query, calculates the total product count, generate file with headers.
     *
     * @return Amasty_Feed_Model_Profile
     */    
    protected function _beforeGenerate()
    {
        // calculate count
        $products = $this->getProducts();
        $this->setInfoTotal($products->getCountProducts());
        $this->setInfoCnt(0);
        
        $writer = $this->_getWriter(true);
        // @todo move to writer
        $row = $this->getHeader();
        if ($row)
            $writer->writeRow($row);
        $writer->close();
        
        return $this;
    }

    protected function _getProductsCategories($products){
        
        $ret = array();
        $ids = array();
        foreach($products as $product){
            $ids[] = $product->getEntityId();
            
            $parentId = $product->getParentId();
            if ($parentId){
                $ids[] = $parentId;
            }
        }
        
        
        $collection = Mage::getResourceModel('catalog/category_collection');
        
        $collection->getSelect()->joinLeft(
                            array('cats' => Mage::getSingleton('core/resource')->getTableName('catalog/category_product')),
                            'cats.category_id = e.entity_id'
//                            ,array('product_id' => 'product_id')
                );

        $collection->getSelect()->where('product_id IN (?)', $ids);
        
        $productCategoriesData = $collection->getData();
        
        $collection = Mage::getModel('catalog/category')
            ->getCollection()->addNameToResult();
        
        $categoriesItems = $collection->getItems();
        
        
        foreach($productCategoriesData as $productCategoriItem){
            $product_id = $productCategoriItem['product_id'];
            $category_id = $productCategoriItem['entity_id'];
            
            if (isset($categoriesItems[$category_id])){
                $category = $categoriesItems[$category_id];
//                var_dump($category->getIsActive());
                if ($category->getIsActive() !== FALSE){
                    
                    if (!isset($ret[$product_id]))
                        $ret[$product_id] = array();
                        
                    $ret[$product_id][] = $category;
                }
                
            }
        }
        
        return $ret;
    }

    protected function _generate()
    {
        $batchSize = Mage::getStoreConfig('amfeed/system/batch_size');

        $products = $this->getProducts()->clear();
        // current page    
        $products->getSelect()->limit($batchSize, $this->getInfoCnt());
        
        $parentProducts = $this->getParentProducts($products);
        
        $productsCategories = $this->_getProductsCategories($products); 
        
        $writer = $this->_getWriter(); 
        
        $fields = array();
        $lines2fields = array();
        
        if (($this->getType() == self::TYPE_CSV) || ($this->getType() == self::TYPE_TXT)) {
            $fields = unserialize($this->getCsv());
            
        }
        
        if ($this->getType() == self::TYPE_XML) {
            $feedXML = Mage::helper('amfeed')->parseXml($this->getXmlBody());
            
            $fields = $feedXML['fields'];
            $lines2fields = $feedXML['lines2fields'];
        }
        
        foreach ($products as $product) {

            $skip = FALSE;
            
//            if (isset($parentProducts[$product->getParentId()]) && $this->getCondDisabled()){
//                $skip = $parentProducts[$product->getParentId()]->getStatus() == 0;
//            }
//            
//            if (!$skip){
                $row = $this->_prepareRow($product, $fields, $parentProducts, $productsCategories);
            $writer->writeRow($row, $lines2fields, $fields);
//            }
            
            $this->setInfoCnt($this->getInfoCnt() + 1);
        }
        
        return $this;
    }     
    
    protected function _afterGenerate()
    {
        $this->setGeneratedAt(date('Y-m-d H:i:s'));
        $writer = $this->_getWriter();
        // @todo move to writer
        $row = $this->getFooter();
        if ($row)
            $writer->writeRow($row);
        $writer->close();        
        
        if (file_exists($this->getMainPath())) {
            unlink($this->getMainPath());
        }
        rename($this->getTempPath(), $this->getMainPath());
        
        $this->setDelivered(0);
        if ($this->getDeliveryType() == self::DELIVERY_TYPE_FTP) {
            $this->_ftpUpload();
        }
        
        return $this; 
    }
    
    protected function _ftpUpload()
    {
        if (false !== strpos($this->getFtpHost(), ':')) {
            list($ftpHost, $ftpPort) = explode(':', $this->getFtpHost());
        } else {
            $ftpHost = $this->getFtpHost();
            $ftpPort = 21;
        }

        $ftp = @ftp_connect($ftpHost, $ftpPort, 10);
        if (!$ftp) {
            throw new Exception(Mage::helper('amfeed')->__('Can not connect the FTP server %s:%s.', $ftpHost, $ftpPort));                
        }

        $ftpLogin = @ftp_login($ftp, $this->getFtpUser(), $this->getFtpPass());
        if (!$ftpLogin) {
            throw new Exception(Mage::helper('amfeed')->__('Can not log in to the server with user `%s` and password `%s`.', $this->getFtpUser(), $this->getFtpPass()));
        }
        
        if ($this->getFtpIsPassive()) {
            ftp_pasv($ftp, true);
        }
        $remotePath = $this->getFtpFolder();
        if ('/' != substr($remotePath, -1, 1) && '\\' != substr($remotePath, -1, 1)) {
            $remotePath .= '/';
        }
        $remoteFileName = substr($this->getMainPath(), strrpos($this->getMainPath(), '/') + 1);
        $remotePath .= $remoteFileName;
        $upload = @ftp_put($ftp, $remotePath, $this->getMainPath(), FTP_ASCII);
        if (!$upload) {
            throw new Exception(Mage::helper('amfeed')->__('Can not upload the file to the folder %s. Please check write permissions', $remotePath));
        }
        ftp_close($ftp);
   
        $this->setDelivered(1);
        $this->setDeliveryAt(date('Y-m-d H:i:s'));

        return $this;
    }
    
    protected function isImage($code){
        return in_array($code, array(
            'image',
            'small_image',
            'thumbnail'
        ));
    }
    
    protected function _customization($value, $product, $field){
        return $value;
    }
    
    protected function _prepareDefaultCustomField($product, $field){
        
        
        
        $limit = NULL;
        
        $ret = $field->getDefaultValue();
        
        $regex = "#{(.*?)}#";
        
        preg_match_all($regex, $ret, $mathes);
        
        if (count($mathes[1]) > 0){
            $repl = array();
            
            $attributes = $mathes[1];
            
            foreach ($attributes as $placeholder){
                $format = Mage::helper('amfeed')->getCustomFieldFormat($placeholder);
                
                $attribute = Mage::helper('amfeed')->getCustomFieldAttribute($placeholder);
                
                $value = $product->getData($attribute);
                $repl['{'. $placeholder . '}'] = $this->_format($format, $value, $limit);
            }
            
            $ret = strtr($ret, $repl);
        }
        
        return $ret;
    }
    
    protected function _prepareCustomField($product, $field, $productsCategories){
        $attrValue = NULL;
        
        if ($field->getBaseAttr()){
            $this->_beforePrepareField($product, $field->getBaseAttr(), $productsCategories);
            // determine the value of attribute
            if ($product->getData($field->getBaseAttr())) {
                $attrValue = $product->getData($field->getBaseAttr());
            } else {
                $attrValue = $this->_prepareDefaultCustomField($product, $field);
            }

            if ($field->getTransform()) {
                // determine the value of transformation
                $transform = $field->getTransform();
                preg_match("/[0-9]+/", $transform, $matches);
                if ('%' == $transform[strlen($transform)-1] && $matches[0]) {
                    $delta = $attrValue*$matches[0]/100;
                } else {
                    $delta = $matches[0];
                }

                // transform the attribute value
                switch ($transform[0]) {
                    case '+':
                        $attrValue = $attrValue + $delta;
                        break;
                    case '-':
                        $attrValue = $attrValue - $delta;
                        break;
                    case '*':
                        $attrValue = $attrValue * $delta;
                        break;
                    case '/':
                        $attrValue = $attrValue / $delta;
                        break;
                }
            }
        } else {
            $attrValue = $this->_prepareDefaultCustomField($product, $field);
        }
        
        return $attrValue;
    }

    protected function _prepareCell($type, $key, $product, &$fields, &$parentProducts, &$productsCategories){
        $attrValue = NULL;
        $attrData = NULL;
            if (($this->getType() == self::TYPE_CSV) || ($this->getType() == self::TYPE_TXT)) {
                $fields['optional'][$key] = 'no';
            }
        
            $isImage = false;
            
            switch ($type) {
                case 'attribute': // it's attribute field
                    $this->_beforePrepareField($product, $fields['attr'][$key], $productsCategories);
                    
                $attrData = $product->getData($fields['attr'][$key]);
                $attrValue = $this->_prepareFieldValue($attrData, $fields['attr'][$key], $fields['format'][$key], $fields['length'][$key]);
                    
                    if ($this->isImage($fields['attr'][$key])) {
                        $isImage = true;
                    }
                    break;
                case 'custom_field': // it's custom field
                    $field = $this->getCustomField($fields['custom'][$key]);
                    if ($this->isImage($fields['custom'][$key])) {
                        $isImage = true;
                    }
                    
                $attrData = $this->_prepareCustomField($product, $field, $productsCategories);
                    
                $attrData = $this->_customization($attrData, $product, $field);
                    
                $attrValue = $this->_prepareFieldValue($attrData, $field->getBaseAttr(), $fields['format'][$key], $fields['length'][$key]);
                    
                    // mapping (Convert from -> to)
                    $conversions = unserialize($field->getMapping());
                    foreach ($conversions['from'] as $i => $from) {
                        if ($from == $attrValue) {
                            $attrValue = $conversions['to'][$i];
                        }
                    }
                    
                    break;
                case 'text': // it's text field
                $attrData = $fields['txt'][$key];
                $attrValue = $this->_prepareFieldValue($attrData, false, $fields['format'][$key], $fields['length'][$key]);
                    break;
                case 'meta_tags': // it's meta tags field
                    $attrData = $this->_prepareMetaTagsValue($product, $fields['meta_tags'][$key], $fields['format'][$key], $fields['length'][$key]);       
                $attrValue = $attrData;
                    break;
                case "images": // it's image field
                $attrData = $this->_prepareImagesValue($product, $fields['images'][$key], $fields['image_format'][$key]);
                $attrValue = $attrData;
                    break;
//                case "5": //parent attribute
//                    if ($this->isImage($fields['parent_attribute'][$key])) {
//                        $isImage = true;
//                    }
//                    
//                    if (isset($parentProducts[$product->getParentId()])){
//                        $parentProduct = $parentProducts[$product->getParentId()];
//                        
//                        $this->_beforePrepareField($parentProduct, $fields['parent_attribute'][$key], $productsCategories);
//                        
//                    $attrData = $parentProduct->getData($fields['parent_attribute'][$key]);
//                    
//                        $attrValue = $this->_prepareFieldValue(
//                        $attrData, 
//                            $fields['parent_attribute'][$key], 
//                            $fields['format'][$key], 
//                            $fields['length'][$key]
//                        );
//                    }
//                    
//                if (empty($attrData)){
//                        
//                        $this->_beforePrepareField($product, $fields['parent_attribute'][$key], $productsCategories);
//                    $attrData = $product->getData($fields['parent_attribute'][$key]);
//                        
//                        $attrValue = $this->_prepareFieldValue(
//                        $attrData, 
//                            $fields['parent_attribute'][$key], 
//                            $fields['format'][$key], 
//                            $fields['length'][$key]
//                        );
//                    }
//
//                    break;
                    }
                    
        $useParentIfEmpty = empty($attrData) && $this->getFrmUseParent() == 1;
                        
        $useParent = isset($fields['parent'][$key]) && $fields['parent'][$key] == 'yes' && $product->getTypeId() == 'simple';
                        
        if (isset($parentProducts[$product->getParentId()]) && ($useParent || $useParentIfEmpty)){

            $parentProduct = $parentProducts[$product->getParentId()];
            
            $attrValue = $this->_prepareCell($type, $key, $parentProduct, $fields, $parentProducts, $productsCategories);
            
        }
        
        return $attrValue;
    }


    protected function _prepareRow($product, &$fields, &$parentProducts, &$productsCategories)
    {
        $row = array();
        
        $types = $fields['type'];
        
        foreach ($types as $key => $type) {
            $row[] = $this->_prepareCell($type, $key, $product, $fields, $parentProducts, $productsCategories);
        }
        
        return $row;
    }
    
    protected function _prepareImagesValue($product, $type, $format){
        $ret = '';
        
        $mediaConfig = Mage::getSingleton('catalog/product_media_config');
        
        $product = Mage::getModel('catalog/product')->load($product->getEntityId());
        
        $gallery = $product->getMediaGallery('images');//$product->getData('media_gallery');//$product->getMediaGalleryImages();
        
        $order = 1;
        
        foreach ($gallery as $image){
            
            if ('image_' . $order === $type){
                
                switch ($format){
                    case "135x135":
                        $ret = Mage::helper('catalog/image')->init($product, 'small_image', $image['file'])->resize(135);
                        break;
                    case "265x265":
                        $ret = Mage::helper('catalog/image')->init($product, 'small_image', $image['file'])->resize(265);
                        break;
                    case "75x75":
                        $ret = Mage::helper('catalog/image')->init($product, 'small_image', $image['file'])->resize(75);
                        break;
                    case "base":
                        $ret = $mediaConfig->getMediaUrl($image['file']);
                        break;
                }
                
                break;
            }

            $order++;
        }
       
        return $ret;
//        var_dump($type, $format);
//        exit(1);
    }
    
    protected function _prepareMetaTagsValue($product, $type, $format, $limit){
        $hlp = Mage::helper('ammeta');
        
        $product = Mage::getModel('catalog/product')->load($product->getEntityId());
        
        if (Mage::helper('amfeed')->isMetaTagsInstalled()){
            //templates configuration for products in categories
            $config = $hlp->getConfigByProduct($product);

            // product attribute => template name
            $pairs = array(
                'meta_title'        => 'title',
                'meta_description'  => 'description',
                'meta_keyword'      => 'keywords',
                'short_description' => 'short_description',
                'description'       => 'full_description',
            );

            foreach ($pairs as $attrCode => $patternName) {

                if ($product->getData($attrCode)){
                    continue;
                }

                $pattern = Mage::getStoreConfig('ammeta/product/' . $patternName);
                foreach ($config as $item){
                    if ($item->getData($patternName)){
                        // get first not empty pattern
                        $pattern = $item->getData($patternName);
                        break;
                    }    
                }

                if ($pattern) {
                    $tag = $hlp->parse($product, $pattern);
                    $max = (int)Mage::getStoreConfig('ammeta/general/max_' . $attrCode);
                    if ($max) {
                        $tag = substr($tag, 0, $max);
                    }
                    $product->setData($attrCode, $tag);    
                }

            }
        }
        return $this->_format($format, $product->getData($type), $limit);

    }
    
    protected function _beforePrepareField(&$product, $code, &$productsCategories){
        if ($code == 'price'){
            if ($product->getTypeId() == 'bundle' && intval($product->getPrice()) === 0){
                $product->setData('price', $product->getMinPrice());
                $product->setData('sale_price', $product->getMinPrice());
            }
        }
        
        if ($code == 'category_name'){
            $categoryName = explode(' > ', $product->getCategoryName());
            
            if (count($categoryName) > 1){
                $product->setCategoryName($categoryName[count($categoryName) - 1]);
            }
        }
        
        if ($code == 'parent_id'){
            if ($product->getTypeId() == 'configurable'){
                $product->setData('parent_id', $product->getEntityId());
            }
        }
        
        if ($code == 'sale_price_effective_date'){
            $sale_price_effective_date = '';
            $special_from_date = $product->getSpecialFromDate();
            $special_to_date = $product->getSpecialToDate();
            
            if ($this->getFrmDate() && !empty($special_from_date) && !empty($special_to_date)) {
                $special_from_date = date($this->getFrmDate(), strtotime($special_from_date)); 
                $special_to_date = date($this->getFrmDate(), strtotime($special_to_date)); 
            }
                
            if (!empty($special_from_date) && !empty($special_to_date)){
                $sale_price_effective_date = $special_from_date.'/'.$special_to_date;
            }

            $product->setData('sale_price_effective_date', $sale_price_effective_date);
            
        }
        
        if ($code == 'categories'){
             
            $productId = $product->getEntityId();
//            exit(1);
            $categoriesNames = array();
            if (isset($productsCategories[$productId])){
            
                foreach($productsCategories[$productId] as $category){
                        
                    $categoriesNames[] = $category->getName();
                }
                
                $product->setData('categories', implode(', ', $categoriesNames));
            }
        }
        
        if ($code == 'category_name'){
            $productId = $product->getEntityId();
             
            $categoriesLevels = array();
            if (isset($productsCategories[$productId])){
                foreach($productsCategories[$productId] as $category){
                    $categoriesLevels[$category->getLevel()] = $category->getName();
                }
                
                krsort($categoriesLevels);
                
                $categoriesLevels = array_values($categoriesLevels);
                
                if (isset($categoriesLevels[0])){
                    $product->setData('category_name', $categoriesLevels[0]);
                }
                
            }
            
        }
    }
    
    protected function _prepareFieldValue($value, $code, $format, $limit)
    {
        if ($code) {
            if (!in_array($code, $this->_checkedAttr)) {
                $this->_checkedAttr[] = $code;
                $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                    ->addVisibleFilter()
                    ->addStoreLabel($this->getStoreId());
                foreach ($attributes as $attribute) {
                    if ($code == $attribute->getAttributeCode()) {
                        switch ($attribute->getFrontendInput()) {
                            case 'select':
                            case 'multiselect':
                                $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                    ->setAttributeFilter($attribute->getId())
                                    ->setStoreFilter($this->getStoreId(), true)
                                    ->load();
                                if ($valuesCollection->getSize() > 0) {
                                    foreach ($valuesCollection as $item) {
                                        $selectOptions[] = array('value' => $item->getId(), 'label' => $item->getValue());
                                    }
                                } else {
                                    $selectOptions = $attribute->getFrontend()->getSelectOptions();
                                }
                                $this->_selectAttr[] = $code;
                                $this->_options[$code] = $selectOptions;
                                break;
                        }
                    }
                }
            }
            
            // replacement value of the label
            if (in_array($code, $this->_selectAttr) && isset($this->_options[$code])) {
                $values = explode(',', $value);
                $temp = array();
                foreach ($values as $val) {
                    foreach ($this->_options[$code] as $option) {
                        if ($val == $option['value']) {
                            $temp[] = Mage::helper('amfeed')->__($option['label']);
                            break;
                        }
                    }
                }
                $value = implode(',', $temp);
            }
            
            if (($this->isImage($code)) && (('no_selection' == $value) || ('' == $value)) && (Mage::getStoreConfig('amfeed/system/image_url'))) {
                switch ($this->getFrmImageUrl()) {
                    case '0': // empty value
                        $value = '';
                        break;
                    case '1': // default image
                        $value = Mage::getBaseUrl('media') . 'amfeed/images/' . $this->getId() . '.jpg';
                        break;
                }
            } elseif ($this->isImage($code)) { // full url for images
                $baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
                $mediaBaseDir = Mage::getBaseDir('media') . DS;
                $path = str_replace($mediaBaseDir, Mage::getBaseUrl('media'), $baseDir);
                $url = str_replace(DS, '/', $path);
                $value = $url . $value;
            }
        }
        
        
        
        return $this->_format($format, $value, $limit);
    }
    
    protected function _format($format, $value, $limit){
        // field format
        switch ($format) {
            case 'as_is': // as is
                $value = $this->_lengthLimitation($value, $limit);
                break;
            case 'strip_tags': // strip tags
                $value = strtr($value, array("\n" => '', "\r" => ''));
                $value = strip_tags($value);
                $value = $this->_lengthLimitation($value, $limit);
                break;
            case 'html_escape': // xml or html escape
                if ($this->getType() == self::TYPE_XML) {
                    $value = '<![CDATA[' . $this->_lengthLimitation($value, $limit) . ']]>';
                } else {
                    $value = htmlspecialchars($value);
                    $value = $this->_lengthLimitation($value, $limit);
                }
                break;
            case 'date': // date
                if ($this->getFrmDate() && !empty($value)) {
                    $value = date($this->getFrmDate(), strtotime($value)); // may be magento functionality ???
                }
                break;
            case 'price': // price
                if ($this->getFrmPrice()) {
                    $value = sprintf('%01.' . $this->getFrmPrice() . 'f', $value); // add $this->_getStore()->getBaseCurrency()->getCode() ???
                }
                break;
            case 'lowercase': // lowercase
                $value = mb_strtolower($this->_lengthLimitation($value, $limit));
                break;
            case 'integer': // integer
                $value = intval($this->_lengthLimitation($value, $limit));
                break;
        }
        return $value;
    }
    
    protected function _lengthLimitation($value, $limit)
    {
        if ($limit) {
            $value = substr($value, 0, $limit);
        }
        return $value;
    }
    
    public function getCustomField($code)
    {
        if (is_null($this->_customFields)){
            $this->_customFields = Mage::getModel('amfeed/field')->getCollection();
        }
        $result = false;
        foreach ($this->_customFields as $field) {
            if ($code == $field->getCode()) {
                $result = $field;
                break;
            }
        }
        
        return $result;
    }
    
    protected function getHeader()
    {
        $headers = $this->getXmlHeader();
        if (($this->getType() == self::TYPE_CSV) || ($this->getType() == self::TYPE_TXT)) {
            if ($this->getCsvHeader()){
                $headers = unserialize($this->getCsv());
                $headers = $headers['name'];
            }
            else {
                $headers = '';
            }
        }        
        
        return $headers;
    }
    
    protected function getFooter()
    {
        $footer   = $this->getXmlFooter();
        if (($this->getType() == self::TYPE_CSV) || ($this->getType() == self::TYPE_TXT)) {
            $footer = '';
        }        
        
        return $footer;
    } 
    
    protected function _getWriter($create=false)
    {
        $config = array('create' => $create);
        
        if ($this->getType() == self::TYPE_XML) {
            $type = 'xml';
            $config['xml_item'] = $this->getXmlItem();
        }
        
        if (($this->getType() == self::TYPE_CSV) || ($this->getType() == self::TYPE_TXT)) {
            $type = 'csv';
            $config['csv_header']    = $this->getCsvHeader();            
            $config['csv_delimiter'] = chr($this->getCsvDelimiter());            
            $config['csv_enclosure'] = chr($this->getCsvEnclosure());            
        }
        
        $writer = Mage::getSingleton('amfeed/writer_' . $type); 
        $writer->init($this->getTempPath(), $config);
        
        return $writer;       
    }
    
    public function getTempPath()
    {
        return Mage::helper('amfeed')->getDownloadPath('feeds', $this->getId());
    }
    
    public function getFileExt($feed = null)
    {
        if (!$feed) {
            $feed = $this;
        }
        $fileExt = '.xml';
        if ($feed->getType() == self::TYPE_CSV) {
            $fileExt = '.csv';
        }
        if ($feed->getType() == self::TYPE_TXT) {
            $fileExt = '.txt';
        }
        return $fileExt;
    }
    
    public function getMainPath()
    {
        return Mage::helper('amfeed')->getDownloadPath('feeds', $this->getFilename() . $this->getFileExt());
    } 
    
    public function getUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'amfeed/' . $this->getFilename() . $this->getFileExt();
    }   
    
    public function getGeneratedAt()
    {
        $v = $this->getData('generated_at');
        if ('0000-00-00 00:00:00' == $v)
            $v = '';
            
        return $v;
    } 
    
    public function getDeliveryAt()
    {
        $v = $this->getData('delivery_at');
        if ('0000-00-00 00:00:00' == $v)
            $v = '';
            
        return $v;
    } 
    
    protected function _saveStatus($status, $err=0)
    {
        $this->setStatus($status)
            ->setInfoErrors($err)
            ->save();
            
        return $this;
    }

    protected function _beforeSave()
    {
        if ($id = $this->getId()) {
            $temp = Mage::getModel('amfeed/profile')->load($id);
            $tempPath = Mage::helper('amfeed')->getDownloadPath('feeds', $temp->getFilename() . $this->getFileExt($temp));
            if (file_exists($tempPath)) {
                $deleted = false;
                if (($temp->getType() != $this->getType())) {
                    Mage::helper('amfeed')->deleteFile($tempPath);
                    $deleted = true;
                }
                if (!$deleted && ($temp->getFilename() != $this->getFilename())) {
                    rename($tempPath, $this->getMainPath());
                }
            }
        }
        
        return parent::_beforeSave();
    }
    
    protected function _afterSave()
    {
        if ($this->getFrmImageUrl()) {
            if (isset($_FILES['upload_image']['error']) && UPLOAD_ERR_OK == $_FILES['upload_image']['error'])
            {
                try {
                    // trying to upload image
                    $uploader = new Varien_File_Uploader('upload_image');
                    $uploader->setFilesDispersion(false);
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->save(Mage::helper('amfeed')->getDownloadPath('images'), $this->getId() . '.jpg');
                } catch (Exception $e) {
                    throw new Exception(Mage::helper('amfeed')->__('An error occurred while saving the feed: %s', $e->getMessage()));
                }
                unset($_FILES['upload_image']);
                $this->setDefaultImage(1)->save();
            }
        } else {
            $path = Mage::helper('amfeed')->getDownloadPath('images', $this->getId() . '.jpg');
            if (file_exists($path)) {
                Mage::helper('amfeed')->deleteFile($path);
                $this->setDefaultImage(0)->save();
            }
        }
        
        return parent::_afterSave();
    }
    
    protected function _beforeDelete()
    {
        if ($this->getDefaultImage()) { // delete default image
            $path = Mage::helper('amfeed')->getDownloadPath('images', $this->getId() . '.jpg');
            Mage::helper('amfeed')->deleteFile($path);
            $this->setDefaultImage(0)->save();
        }
        $path = $this->getMainPath();
        if (file_exists($path)) { // delete feed file
            Mage::helper('amfeed')->deleteFile($path);
        }
        
        return parent::_beforeDelete();
    }
}