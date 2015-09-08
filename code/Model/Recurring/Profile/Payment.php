<?php

class Totm_SagePayRecurringRewrite_Model_Recurring_Profile_Payment extends Ebizmarts_SagePayRecurring_Model_Recurring_Profile_Payment {

    public function generateCycle(Ebizmarts_SagePayRecurring_Model_Recurring_Profile $profile, Ebizmarts_SagePaySuite_Model_Sagepaysuite_Transaction $transaction) {

        /*
			ISSUE: The current product setup shows billing frequency as the maximum number of cycles.
			I reckon the billing frequency should be how often to take payment for a defined billing period.
			E.g. billing period = Monthly and billing frequency = 2 it will take 2 payments for this period.
			If set to 1 will take one payment. There should be a maximum billing cycle / payment option which in the current state is the frequency.
			It is more around terminology as can be confusing. See core magento recurring.
		 */

        //Generate payment cycle
        $cartId = Mage::getSingleton('checkout/session')->getQuoteId();
        $collection = Mage::getModel('delivery/delivery')->getCollection();
        $collection->addFieldToFilter('cart_id', array('eq' => $cartId));
        $data = $collection->getData();
        if (empty($data) || empty($data[0])) {
            return;
        }

        //Step
        $span = '+' . $data[0]['interval'] . ' day';
        $current = strtotime( $data[0]['start_delivery_date'] );

        $maxCycles = ((int)$profile->getPeriodMaxCycles() > 0) ? (int)$profile->getPeriodMaxCycles() : 100;

        for($i = 0; $i < $maxCycles; $i++) {
            $nextCycle = Mage::getModel('core/date')->gmtDate(null, $current);
            $current = strtotime($span, $current);

            $this->setId(null)
                ->setProfileId($profile->getId())
                ->setScheduledAt($nextCycle)
                ->save();
        }
    }
}