<?php
class Cofamedia_AdminLog_Block_List extends Mage_Core_Block_Template
{
	public $_list = false;
	public $_lastuser = '';
	public function __construct()
		{
			parent::__construct();
			
			$order = Mage::registry('sales_order');
			$order_id = $order->getEntityId();
			
			$table = Mage::getSingleton('core/resource')->getTableName('adminlog');
			$read = Mage::getSingleton('core/resource')->getConnection('core_write');
			
			$sql = "SELECT * FROM $table";
			$sql.= " WHERE order_id=$order_id";
			$result = $read->query($sql);
			foreach($result as $row)
				{
// 					qq($row);
					$id = $row['adminlog_id'];
					$this->_list[$id] = $row;
					
					$this->_lastuser = "<b>";
					if($row['user_firstname']) $this->_lastuser.= $row['user_firstname'];
					if($row['user_firstname'] && $row['user_lastname']) $this->_lastuser.= ' ';
					if($row['user_lastname']) $this->_lastuser.= $row['user_lastname'];
					$this->_lastuser.= "</b>";
					$this->_lastuser.= '<br/>'.$row['user_email'];
				}
    }
	
	public function getList()
		{
			return $this->_list;
		}
	
	public function getLastUser()
		{
			return $this->_lastuser;
		}
} 
