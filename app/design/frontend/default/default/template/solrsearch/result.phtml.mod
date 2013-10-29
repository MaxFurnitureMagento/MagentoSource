<?php
/**
 * Product list template
 *
 * @see Mage_Catalog_Block_Product_List
 */
?>
<?php
    $_solrDataArray = $this->getData('solrdata');
	$_helper = Mage::helper('catalog/output');
    $_productCollection = $this->getProductCollection();
    $q = "*";
    $params = $this->getRequest()->getParams();
    if (!empty($params['q'])){
    	$q = $params['q'];
    }
?>
<?php  ?>
<div id="solr_search_result_page_container">
<div class="page-title">
        <?php if($q == $_solrDataArray['responseHeader']['params']['q']):?>
        <h1>Search results for <em id="text_keyword"><?php echo $q;?></em></h1>
        <?php else:?>
        <h1>Search results for <em id="text_keyword"><?php echo $_solrDataArray['responseHeader']['params']['q'];?></em> instead.</h1>
        <?php endif;?>
</div>
<?php if(!sizeof($_productCollection)): ?>
<p class="note-msg"><?php echo $this->__('There are no products matching the selection.') ?></p>
<?php else: ?>
<div class="category-products">
    <?php echo $this->getOptionsHtml() ?>
	<?php echo $this->getToolbarHtml() ?>
    <?php // List mode ?>
    <?php if($this->getToolbarBlock()->getCurrentMode() == 'list'): ?>
    <?php $_iterator = 0; ?>
    <ol class="products-list" id="products-list">
    <?php foreach ($_productCollection as $_product): ?>    	
        <li class="item<?php if( ++$_iterator == sizeof($_productCollection) ): ?> last<?php endif; ?>">
            <?php // Product Image ?>
            <a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" class="product-image"><img src="<?php echo $this->helper('catalog/image')->init($_product, 'small_image')->resize(135); ?>" width="200" height="200" alt="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" /></a>
            <?php // Product description ?>
            <div class="product-shop">
                <div class="f-fix">
                    <?php $_productNameStripped = $this->stripTags($_product->getName(), null, true); ?>
                    <h2 class="product-name"><a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $_productNameStripped; ?>"><?php echo $_helper->productAttribute($_product, $_product->getName() , 'name'); ?></a></h2>
                    <?php if($_product->getRatingSummary()): ?>
                    <?php echo $this->getReviewsSummaryHtml($_product) ?>
                    <?php endif; ?>
                    <?php echo $this->getPriceHtml($_product, true) ?>
                    <?php if($_product->isSaleable()): ?>
                        <p><button type="button" title="<?php echo $this->__('Add to Cart') ?>" class="button btn-cart" onclick="setLocation('<?php echo $this->getAddToCartUrl($_product) ?>')"><span><span><?php echo $this->__('Add to Cart') ?></span></span></button></p>
                    <?php else: ?>
                        <p class="availability out-of-stock"><span><?php echo $this->__('Out of stock') ?></span></p>
                    <?php endif; ?>
                    <div class="desc std">
                        <?php echo $_helper->productAttribute($_product, $_product->getShortDescription(), 'short_description') ?>
                        <a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $_productNameStripped ?>" class="link-learn"><?php echo $this->__('Learn More') ?></a>
                    </div>
                    <ul class="add-to-links">
                        <?php if ($this->helper('wishlist')->isAllow()) : ?>
                            <li><a href="<?php echo $this->helper('wishlist')->getAddUrl($_product) ?>" class="link-wishlist"><?php echo $this->__('Add to Wishlist') ?></a></li>
                        <?php endif; ?>
                        <?php if($_compareUrl=$this->getAddToCompareUrl($_product)): ?>
                            <li><span class="separator">|</span> <a href="<?php echo $_compareUrl ?>" class="link-compare"><?php echo $this->__('Add to Compare') ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </li>
		<?php $_product->reset();?>
    <?php endforeach; ?>
    </ol>
    <script type="text/javascript">decorateList('products-list', 'none-recursive')</script>

    <?php else: ?>

    <?php // Grid Mode ?>
	
    <?php $_collectionSize = sizeof($_productCollection) ?>
    <?php $_columnCount = 3 ?>
    <?php $i=0;?>
	<?php foreach ($_productCollection as $_product): ?>
    	<?php if ($i++%$_columnCount==0): ?>
        <ul class="products-grid">
        <?php endif ?>
            <li class="item<?php if(($i-1)%$_columnCount==0): ?> first<?php elseif($i%$_columnCount==0): ?> last<?php endif; ?>">
                <a class="product-image" href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>"><img src="<?php echo $this->helper('catalog/image')->init($_product, 'small_image')->resize(135); ?>" width="200" height="200" alt="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" /></a>
<div class="product-list-container">
<div class="product-header">
<h2 class="product-name"><a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $this->stripTags($_product->getName(), null, true) ?>"><?php echo $_helper->productAttribute($_product, $_product->getName(), 'name') ?></a></h2></div>
                <?php if($_product->getRatingSummary()): ?>
                <?php echo $this->getReviewsSummaryHtml($_product, 'short') ?>
                <?php endif; ?>
<div class="price-custom">
<div class="product-model">Model:<?php echo $_product->getSku(); ?></div>
<div class="price-box"><?php echo $this->getPriceHtml($_product, true) ?></div>
</div>
                <div class="actions">
                    <?php if($_product->isSaleable()): ?>
                        <!--<button type="button" title="<?php echo $this->__('Add to Cart') ?>" class="button btn-cart" onclick="setLocation('<?php echo $this->getAddToCartUrl($_product) ?>')"><span><span><?php echo $this->__('Add to Cart') ?></span></span></button> -->
                    <?php else: ?>
                        <p class="availability out-of-stock"><span><?php echo $this->__('Out of stock') ?></span></p>
                    <?php endif; ?>
                    <ul class="add-to-links">
                        <?php if ($this->helper('wishlist')->isAllow()) : ?>
                            <li><a href="<?php echo $this->helper('wishlist')->getAddUrl($_product) ?>" class="link-wishlist"><?php echo $this->__('Add to Wishlist') ?></a></li>
                        <?php endif; ?>
                        <?php if($_compareUrl=$this->getAddToCompareUrl($_product)): ?>
                            <li><span class="separator">|</span> <a href="<?php echo $_compareUrl ?>" class="link-compare"><?php echo $this->__('Add to Compare') ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
</div>
            </li>
        <?php if ($i%$_columnCount==0 || $i==$_collectionSize): ?>
        </ul>
        <?php endif ?>
		<?php $_product->reset();?>
        <?php endforeach ?>
        <script type="text/javascript">decorateGeneric($$('ul.products-grid'), ['odd','even','first','last'])</script>
    <?php endif; ?>

    <div class="toolbar-bottom">
        <?php echo $this->getToolbarHtml() ?>
    </div>
</div>
<?php endif; ?>
</div>