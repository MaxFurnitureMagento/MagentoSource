<?php
class Cofamedia_Stickers_Adminhtml_StickersController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('stickers/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Stickers Manager'), Mage::helper('adminhtml')->__('Stickers Manager'));
		
		return $this;
	}

	public function stickersAction(){
		$this->_initAction()
			->renderLayout();
	}

	/**
     * Edit CMS page
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('stickers_id');
        $model = Mage::getModel('stickers/stickers');

        if ($id)
					{
            $model->load($id);
            if(!$model->getId())
							{
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('stickers')->__('Sticker is missing'));
                $this->_redirect('*/*/');
                return;
							}
					}

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data))
					{
            $model->setData($data);
					}
        
				Mage::register('stickers_stickers', $model);

        $this->_initAction()
             ->_addBreadcrumb(Mage::helper('stickers')->__('CMS'), Mage::helper('stickers')->__('CMS'))
						 ->_addContent($this->getLayout()->createBlock('stickers/adminhtml_stickers_edit'))
             ->_addLeft($this->getLayout()->createBlock('stickers/adminhtml_stickers_edit_tabs'));

				$this->renderLayout();
    }
	
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost())
					{
						if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '')
							{
								try
									{	
										$uploader = new Varien_File_Uploader('image');
										$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
										$uploader->setAllowRenameFiles(false);
										$uploader->setFilesDispersion(false);
										// We set media as the upload dir
										$path = Mage::getBaseDir('media') .DS. 'cofa_media' .DS. 'stickers' .DS. 'image';
										$uploader->save($path, $_FILES['image']['name'] );
									}
								catch(Exception $e)
									{
										$this->_getSession()->addException($e, Mage::helper('stickers')->__('Error uploading image. Please try again later.'));
									}
								$data['image'] = 'cofa_media/stickers/image/' . $_FILES['image']['name'];
							}
						else
							{
								if(isset($data['image']['delete']) && $data['image']['delete'] == 1)
									$data["image"] = "";
								else
									unset($data["image"]);
							}
			
						if(isset($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['name'] != '')
							{
								try
									{	
										$uploader = new Varien_File_Uploader('thumbnail');
										$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
										$uploader->setAllowRenameFiles(false);
										$uploader->setFilesDispersion(false);
										// We set media as the upload dir
										$path = Mage::getBaseDir('media') .DS. 'cofa_media' .DS. 'stickers' .DS. 'thumbnail';
										$uploader->save($path, $_FILES['thumbnail']['name'] );
									}
								catch(Exception $e)
									{
										$this->_getSession()->addException($e, Mage::helper('stickers')->__('Error uploading image. Please try again later.'));
									}
								$data['thumbnail'] = 'cofa_media/stickers/thumbnail/' . $_FILES['thumbnail']['name'];
							}
						else
							{
								if(isset($data['thumbnail']['delete']) && $data['thumbnail']['delete'] == 1)
									$data["thumbnail"] = "";
								else
									unset($data["thumbnail"]);
							}
			
        $model = Mage::getModel('stickers/stickers');

        if ($id = $this->getRequest()->getParam('stickers_id'))
					{
            $model->load($id);
          }
        
// 				die(highlight_string(print_r($data, true)));
				$model->setData($data);
            
				$url = 'stickers';//$model->getCategoryUrl($model->getData("category_id"));
						
        Mage::dispatchEvent('stickers_prepare_save', array('stickers' => $model, 'request' => $this->getRequest()));
						// die(highlight_string(print_r($model, true)));
            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('stickers')->__('Sticker was successfully saved'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('stickers_id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/'.$url);
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('stickers')->__('Error while saving. Please try again later.'.$e));
            }
            
            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('stickers_id' => $this->getRequest()->getParam('stickers_id')));
            return;
        }
        $this->_redirect('*/*/'.$url);
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('stickers_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('stickers/stickers');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('stickers')->__('Sticker was successfully deleted'));
                // go to grid
                Mage::dispatchEvent('adminhtml_cmspage_on_delete', array('title' => $title, 'status' => 'success'));
                // go to grid
                $url = 'stickers';//$model->getCategoryUrl($model->getData("category_id"));
                $this->_redirect('*/*/'.$url);
                return;

            } catch (Exception $e) {
                Mage::dispatchEvent('adminhtml_stickers_on_delete', array('title' => $title, 'status' => 'fail'));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('stickers_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('stickers')->__('Unable to delete sticker.'));
        // go to grid
        $this->_redirect('*/*/');
    }
}