<?php
class Jextn_Testimonials_SubmitController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		
		$this->loadLayout();     
		$this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->renderLayout();
    }
	
    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        if ( $post ) {
		
			$model = Mage::getModel('testimonials/testimonials');		
			$model->setData($post)
				->setId($this->getRequest()->getParam('id'));
            try {
	             $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }
				 if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }
                if (!Zend_Validate::is(trim($post['content']) , 'NotEmpty')) {
                    $error = true;
                }
							// validate  captcha!
				$recaptcha = Mage::helper('testimonials')->getRecaptcha();

				if(!empty($post["recaptcha_response_field"])) {
					$result = $recaptcha->verify($post["recaptcha_challenge_field"], $post["recaptcha_response_field"]);

					if(!$result->isValid()) { // failed validation
						Mage::getSingleton('customer/session')->addError(Mage::helper('testimonials')->__('The reCAPTCHA wasn\'t entered correctly. Go back and try it again'));
						$error = true;
						}
				}
				else {
					Mage::getSingleton('customer/session')->addError(Mage::helper('testimonials')->__('The reCAPTCHA wasn\'t entered correctly. Go back and try it again'));
					$error = true;
				}
				if ($error) {
                    throw new Exception();
                }
				$url = $post['url'];
				if (! $model->getId()) {
					$model->setCreatedTime(now());
				}
				$model->setUpdateTime(now());
				
				$model->save();
				if(Mage::helper('testimonials')->getAutoApproved()== "0"){
					Mage::getSingleton('customer/session')->addSuccess(Mage::helper('testimonials')->__('Your testimonial has been accepted for moderation.'));
				} else {
					Mage::getSingleton('customer/session')->addSuccess(Mage::helper('testimonials')->__('Your testimonial has been accepted.'));
				}
                $this->_redirect('*/*');

                return;
            } catch (Exception $e) {

                Mage::getSingleton('customer/session')->addError(Mage::helper('testimonials')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*');
                return;
            }

        } else {
            $this->_redirect('*/*');
        }
    }
}