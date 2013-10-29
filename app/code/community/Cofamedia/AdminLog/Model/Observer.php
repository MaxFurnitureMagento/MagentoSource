<?php
class Cofamedia_AdminLog_Model_Observer
{    
    public function hookIntoSalesOrderPlaceAfter($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $order_id = $order->getId();
        $number = $order->getIncrementId();
        $state = $order->getState();
        $status = $order->getStatus();
		 
		 /**start addition cart record**/
		$tmp = Mage::app()->getRequest()->getPost('order');
		if (!empty ($tmp['additional_carts_number']))$order->setAdditionalCartsNumber($tmp['additional_carts_number'])->save;
		unset($tmp);
		/**end**/
        if(empty($state)) return;
        
        if(Mage::registry("hookIntoSalesOrderPlaceAfter-$order_id-$state")) return $this;
        
        if(!$posted_user_id = Mage::getSingleton('core/app')->getRequest()->getPost('adminlog_chooser'))
					return $this;
//         $admin = Mage::getSingleton('admin/session')->getUser();
				$admin = Mage::getModel('admin/user')->load($posted_user_id);
        $user_id = $admin->getUserId();
        $user_email = $admin->getEmail();
        $user_name = $admin->getUsername();
        $user_firstname = $admin->getFirstname();
        $user_lastname = $admin->getLastname();

				$table = Mage::getSingleton('core/resource')->getTableName('adminlog');
				$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			
				$sql = "INSERT INTO $table SET";
				$sql.= " user_id=$user_id";
				$sql.= ",user_name='$user_name'";
				$sql.= ",user_email='$user_email'";
				$sql.= ",user_firstname='$user_firstname'";
				$sql.= ",user_lastname='$user_lastname'";
				$sql.= ",order_id=$order_id";
				$sql.= ",order_number='$number'";
				$sql.= ",order_state='$state'";
				$sql.= ",order_status='$status'";
				$sql.= ",date=NOW()";
				
				$write->query($sql);
				
				Mage::register("hookIntoSalesOrderPlaceAfter-$order_id-$state", true);

        return $this;
    } 
}