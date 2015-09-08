<?php

/**
 * Ebizmarts main observer model for events
 *
 * @category    Ebizmarts
 * @package    Ebizmarts_SagePayRecurring
 */
class Totm_SagePayRecurringRewrite_Model_Observer extends Ebizmarts_SagePayRecurring_Model_Observer {


	public function submitSagePayRecurringItems($observer) {

        if(!Mage::helper('sagepay_recurring')->isActive()) {
            return $observer;
        }

        $cartId = Mage::getSingleton('checkout/session')->getQuoteId();
        $collection = Mage::getModel('delivery/delivery')->getCollection();
        $collection->addFieldToFilter('cart_id', array('eq' => $cartId));
        $data = $collection->getData();
        if (empty($data) || empty($data[0])) {
            return $this;
        }

		$trn   = $observer->getEvent()->getTransaction();
		$order = $observer->getEvent()->getOrder();

        $profile = Mage::getModel('sagepay_recurring/recurring_profile');
        
        $trn->setTxType('REPEAT');

        $description = array();
        $cycles = array();

		//Importar items a recurring items table
		foreach ($order->getAllVisibleItems() as $item) {

            $product = Mage::getModel("catalog/product")->load($item->getProduct()->getId());
            $description[] = $product->getName();
            if (is_object($product) && ((int)$product->getSagepayIsRecurring()) ) {

                $profile->importProduct($product);

                $maxCycles = $profile->getPeriodMaxCycles();
                if ($maxCycles !== null) {
                    $cycles[] = $maxCycles;
                }

            	if($profile) {
	                $profile->importOrder($order);
	                $profile->importOrderItem($item);

            		if (!$profile->isValid()) {
                		Mage::throwException($profile->getValidationErrors(true, true));
            		}

            	}

            }

        }

        // here we use MIN of products cycles
        $minCycle = min($cycles);
        $profile->setPeriodMaxCycles($minCycle);

        // setting grand total
        $order->load($order->getId());
        $_totalData =$order->getData();
        $_grand = $_totalData['grand_total'];
        $profile->setBillingAmount($_grand);

        // setting description - all products are used here
        $profile->setScheduleDescription(implode(', ', $description));

        // setting schedule params for all products
        $profile->setPeriodUnit(Mage_Payment_Model_Recurring_Profile::PERIOD_UNIT_DAY);
        $profile->setPeriodFrequency($data[0]['interval']);
        $profile->setPeriodMaxCycle($minCycle);

        $_profile = $profile->submit($trn);

        if($_profile->getId()) {

            Mage::getModel('sagepay_recurring/recurring_profile_order')
                ->setOrderId($order->getId())
                ->setProfileId($_profile->getId())
                ->save();

            //Generate payment rows
            Mage::getModel('sagepay_recurring/recurring_profile_payment')
                ->generateCycle($_profile, $trn);
        }

	}


}