<?php
/**
 * Magento
 *
 * PHP version 5
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category Totm
 * @package  Totm_SagePayRecurringRewrite
 * @author   Altexsoft <sales@altexsoft.com>
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link     http://www.magentocommerce.com/knowledge-base/
 */

/**
 * Helper.
 *
 * @category Totm
 * @package  Totm_SagePayRecurringRewrite
 * @author   Altexsoft <sales@altexsoft.com>
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link     http://www.magentocommerce.com/knowledge-base/
 */
class Totm_SagePayRecurringRewrite_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Action to change frequency cycle.
     *
     * @param int $profileId    Profile Id.
     * @param int $newFrequency New frequency.
     *
     * @return void
     */
    public function changeBillingPeriod($profileId, $newFrequency=null)
    {
        $profileModel = Mage::getModel('sagepay_recurring/recurring_profile')
            ->load($profileId);

        if (!$profileModel->getId()) {
            Mage::throwException($this->__('Specified profile does not exist.'));
        }

        $profileId = $profileModel->getId();
        $frequency = $newFrequency ? $newFrequency : $profileModel->getPeriodFrequency();

        // Get Last Scheduled Payment.
        $profileLastScheduledPayment = Mage::getModel('sagepay_recurring/recurring_profile_payment')->getCollection()
            ->addFieldToFilter('profile_id', $profileId)
            ->addFieldToFilter('transaction_id', array('notnull' => true))
            ->setOrder('scheduled_at', 'DESC')
            ->setCurPage(1)
            ->setPageSize(1)
            ->getFirstItem();

        if (!$profileLastScheduledPayment->getId()) {
            Mage::throwException($this->__('Specified last scheduled payment does not exist.'));
        }

        // Get Last Scheduled Data.
        $lastScheduledData = $profileLastScheduledPayment->getScheduledAt();

        if (!$lastScheduledData) {
            Mage::throwException($this->__('Error to retrieve last scheduled data.'));
        }

        $profilePaymentCollection = Mage::getModel('sagepay_recurring/recurring_profile_payment')->getCollection()
            ->addFieldToFilter('profile_id', $profileId)
            ->addFieldToFilter('transaction_id', array('null' => true));

        $span = '+' . $frequency . ' day';
        $lastScheduledTime = strtotime($lastScheduledData);

        foreach ($profilePaymentCollection->getItems() as $profilePayment) {
            $nextScheduledTime = strtotime($span, $lastScheduledTime);
            $nextScheduledData = Mage::getModel('core/date')->gmtDate(null, $nextScheduledTime);

            // Set new Scheduled At
            $profilePayment->setScheduledAt($nextScheduledData)
                ->save();

            $lastScheduledTime = $nextScheduledTime;
        }

        // Set new frequency.
        $profileModel->setPeriodFrequency($frequency)
            ->save();
    }
}
