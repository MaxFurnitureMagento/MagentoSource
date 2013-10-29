<?php
class MageDevelopers_MaxFurniture_ImportController extends Mage_Adminhtml_Controller_Action
{
  public function indexAction() {
    $this->loadLayout()
          ->_setActiveMenu('cofamedia/import')
          ->renderLayout()
          ;
  }
  
  public function startAction() {
    $DIR = Mage::getBaseDir()."/custom/add_products/";
    system('php '.$DIR.'add_products.php');
    
    $DIR = Mage::getBaseDir()."/custom/add_products/csv/";
    $folder = scandir($DIR);
    foreach($folder as $file) {
      if($file[0] == '.') continue;
//       @ unlink($DIR.$file);
    }
    
    Mage::getSingleton('core/session')->addSuccess(Mage::helper('maxfurniture')->__('Products imported successfully.'));
    $this->_redirect('*/*/');
  }
  
  public function relatedAction() {
    $DIR = Mage::getBaseDir()."/custom/add_products/";
    system('php '.$DIR.'related_products.php');
    
    $DIR = Mage::getBaseDir()."/custom/add_products/csv/";
    $folder = scandir($DIR);
    foreach($folder as $file) {
      if($file[0] == '.') continue;
//       @ unlink($DIR.$file);
    }
    
    Mage::getSingleton('core/session')->addSuccess(Mage::helper('maxfurniture')->__('Product links imported successfully.'));
    $this->_redirect('*/*/');
  }
  
  public function uploadAction() {
//     $data = $this->getRequest()->getPost();
    $file = $_FILES['xls'];
    if($file['error'] == 4) {
      Mage::getSingleton('core/session')->addError(Mage::helper('maxfurniture')->__('Please choose file to upload.'));
      $this->_redirect('*/*/');
      return;
    } elseif($file['error']) {
      Mage::getSingleton('core/session')->addError(Mage::helper('maxfurniture')->__('Upload error. Please try again.'));
      $this->_redirect('*/*/');
      return;
    } elseif(!preg_match('/\.xlsx{0,1}$/', $file['name'])) {
      Mage::getSingleton('core/session')->addError(Mage::helper('maxfurniture')->__('You can only use XLS or XLSX files.'));
      $this->_redirect('*/*/');
      return;
    }
    $name = $file['name'];
    $ext = preg_match('/xlsx$/', $name) ? 'xlsx' : 'xls';
    $xls_file_name = Mage::getBaseDir()."/custom/add_products/xls/import.$ext";
    @ unlink($xls_file_name);
    move_uploaded_file($file['tmp_name'], $xls_file_name);
    
    try {
      require_once  Mage::getBaseDir()."/custom/include/PHPExcel/PHPExcel.php";
      
      $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
      $cacheSettings = array( 'cacheTime' => 6000 );
      PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

      if($ext == 'xls') $objReader = new PHPExcel_Reader_Excel5();
      else $objReader = new PHPExcel_Reader_Excel2007();
      $objReader->setReadDataOnly(true);
      $names = $objReader->listWorksheetNames($xls_file_name);

      // ww($CUSTOM);
      $DIR = Mage::getBaseDir()."/custom/add_products/";

      $files = array();
      foreach($names as $key => $name) {
        $name = strtolower($name);
        $name = preg_replace('/\d$/', '', $name);
        $files[$name] = $name;
      }
      
      foreach($files as $file) {
        @ unlink($DIR.'csv/'.$file.'.csv');
      }
      
      $i = 0;
      foreach($names as $key => $name) {
        $file_name = strtolower($name);
        $file_name = preg_replace('/\d$/', '', $file_name);
        
        $i++;
        
        $file_path = $DIR.'csv/'.$file_name.'.csv';
        
        $skip_first_row = false;
        if(file_exists($file_path)) {
  //         wlog("Appending: $file_name.csv", false);
          $skip_first_row = true;
        }/* else wlog("Creating: $file_name.csv", false);*/
        
        $fp = fopen($file_path, 'a');
        
        $objReader->setLoadSheetsOnly( array($name) );
        $objPHPExcel = $objReader->load($xls_file_name);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        
        $j = 0;
        foreach($objWorksheet->getRowIterator() as $row) {
          $j++;
          
          if(($j == 1) && $skip_first_row) continue;
          
          $cellIterator = $row->getCellIterator();
          $cellIterator->setIterateOnlyExistingCells(false);
          $fields = array();
          foreach($cellIterator as $cell) {
            $value = $cell->getValue();
            $fields[] = $value;
          }
          fputcsv($fp, $fields);
        }
        
        fclose($fp);
      }
    } catch(Exception $e) {
      Mage::getSingleton('core/session')->addError(Mage::helper('maxfurniture')->__('Error processing file.<br/>%s', $e->getMessage()));
      $this->_redirect('*/*/');
      return;
    }
    Mage::getSingleton('core/session')->addSuccess(Mage::helper('maxfurniture')->__('File uploaded and conferted to CSV files successfully.'));
    $this->_redirect('*/*/');
  }
}
