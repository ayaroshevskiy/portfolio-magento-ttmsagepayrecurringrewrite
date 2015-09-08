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
 * Controller in Admin panel
 *
 * @category Totm
 * @package  Totm_SagePayRecurringRewrite
 * @author   Altexsoft <sales@altexsoft.com>
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link     http://www.magentocommerce.com/knowledge-base/
 */
class Totm_SagePayRecurringRewrite_Adminhtml_TsrecurringrewriteController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Action to change frequency cycle.
     *
     * @return $this
     */
    public function changeFrequencyCycleAction()
    {
        $postData = $this->getRequest()->getPost();

        try {
            // Make Logic to set new payments and Frequency.
            Mage::helper('sagepayrecurringrewrite')->changeBillingPeriod($postData['profile_id'], $postData['frequency']);

            $this->_getSession()->addSuccess($this->__('New Frequency Cycle has been set.'));

        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Failed to update the frequency. %s', $e->getMessage()));
            Mage::logException($e);
        }

        unset($postData['profile_id']);
        unset($postData['form_key']);
        unset($postData['frequency']);
        $this->_redirect('*/sales_order/view', $postData);
    }
}
