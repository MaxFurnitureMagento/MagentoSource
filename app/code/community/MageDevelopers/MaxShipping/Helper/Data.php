<?php
class MageDevelopers_MaxShipping_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function getTrackingInfo($carrier_title, $tracking_number)
    {
      $v = array('number' => '', 'url' => '', 'title' => '', 'track' => '');
      $read = Mage::getSingleton('core/resource')->getConnection('core_read');
      $v['number'] = $tracking_number;
      $tracking_number = mysql_escape_string($tracking_number);
      $sql = "SELECT * FROM sales_flat_shipment_track WHERE track_number='$tracking_number'";
      if($row = $read->fetchRow($sql)) {
	$carrier_title = $row['carrier_code'];
      }
      $carrier_title = mysql_escape_string($carrier_title);
      $sql = "SELECT * FROM max_carrier";
      $sql.= " LEFT JOIN max_carrier_code ON max_carrier_code.carrier_id=max_carrier.id";
      $sql.= " WHERE max_carrier_code.code='$carrier_title'";
      if($row = $read->fetchRow($sql))
        {
          $nice_url = preg_replace('/^http:\/\//', '', $row['url']);
          $nice_url = trim($nice_url, '/');
          $v['url'] = $nice_url;
          $v['title'] = $row['name'];
          if($track_url = $row['track'])
            {
              $track_url = preg_replace('/\{\{track\}\}/', $tracking_number, $track_url);
              $v['track'] = $track_url;
            }
          elseif($track_url = $row['url'])
            $v['track'] = $row['url'];
        }
      return $v;
   }
}
