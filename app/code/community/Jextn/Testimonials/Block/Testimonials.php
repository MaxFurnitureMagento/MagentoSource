<?php
class Jextn_Testimonials_Block_Testimonials extends Mage_Core_Block_Template
{
  public $_count = 0;
  public $_pp = 5;
  public $_pg = 0;
  public $_range = array();
  public $_pages = 0;
  public $_next = 0;
  public $_previous = 0;
	public function _prepareLayout()
    {
// 		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('testimonials')->getTestimonialsTitle());
		return parent::_prepareLayout();
    }
    
    public function getTestimonials()     
     { 
      $collection = Mage::getModel('testimonials/testimonials')->getCollection()
                    ->addIsActiveFilter()
                    ;
      $this->_count = count($collection);
      $this->_pages = $this->_count / $this->_pp;
      if(!$pg = Mage::app()->getRequest()->getParam('pg')) $pg = 0;
      else $this->_previous = $pg - 1;
      $this->_pg = $pg;
      if($pg < ($this->_pages - 1)) $this->_next = $pg + 1;
      
      $offset_start = $offset_end = 0;
      if($this->_pages - $pg <= 2) $offset_start = -3 + ($this->_pages - $pg);
      elseif($pg < 2) $offset_end = 2 - $pg;
      
      $end = $pg + 2;
      if($end > ($this->_pages - 1)) $end = $this->_pages - 1;
      
      $start = $pg - 2;
      if($start < 0)
        {
          $start = 0;
        }
        
      
      for($i=$start+$offset_start; $i<=$end+$offset_end; $i++)
        $this->_range[] = $i;
      
      $collection = Mage::getModel('testimonials/testimonials')->getCollection()
                    ->addIsActiveFilter()
                    ;
      $collection->getSelect()
                 ->limit($this->_pp, $pg * $this->_pp)
                 ;
      return $collection;        
    }
	
	public function getSidebarTestimonials()     
     { 
		$collection = Mage::getModel('testimonials/testimonials')->getCollection()
							->addSidebarFilter()
							->addIsActiveFilter();
        return $collection;        
    }
	
	public function getFormAction()
	{
		return $this->getUrl('testimonials/submit/post', array('_secure' => true));	
	}
}