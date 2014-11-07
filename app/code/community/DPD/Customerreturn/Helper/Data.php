<?php
/**
 * @package            DPD
 * @subpackage       Customer Return
 * @category            Returns
 * @author               Michiel Van Gucht
 */
class DPD_Customerreturn_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * Check if order can be returned by customer
     *
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function canReturn(Mage_Sales_Model_Order $order)
    {
		// If order return is disabled in system configuration: return false
        if(!Mage::getStoreConfigFlag('shipping/dpd_customerreturn/enabled')) {
            return false;
        }
		
		// If the order has no shipments nothing can be returned.
		if(!$order->hasShipments()){
			return false;
		}
		
        // If Magento decides that this order cannot be returned
        /*
		if(!$order->canReturn()) {
            return false;
        }
		*/
		
		// If order has shipment(s) but can still be shipped, it means that is partially ship.
        // If order is partially shipped and that return of partially shipped orders is disabled in system config: return false
        if($order->hasShipments() && $order->canShip() && !Mage::getStoreConfigFlag('shipping/dpd_customerreturn/return_partially_shipped')) {
            return false;
        }

        // Calculate the number of days since the order's datetime
        $dateModel = Mage::getModel('core/date');
        $createdAt = $order->getCreatedAtStoreDate();
        $deltaDays = ($dateModel->gmtTimestamp() - $dateModel->gmtTimestamp($createdAt)) / 86400;

        // If the number of days since order's datetime is larger than the cancelation leadtime in system config: return false
		if(Mage::getStoreConfig('shipping/dpd_customerreturn/leadtime') !== '' && $deltaDays > Mage::getStoreConfig('shipping/dpd_customerreturn/leadtime')) {
            return false;
        }

        // Else... return true
        return true;
    }
}