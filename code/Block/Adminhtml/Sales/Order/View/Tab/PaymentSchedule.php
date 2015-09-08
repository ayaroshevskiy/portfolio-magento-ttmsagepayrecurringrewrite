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
 * Tab contents for order view in Admin panel
 *
 * @category Totm
 * @package  Totm_SagePayRecurringRewrite
 * @author   Altexsoft <sales@altexsoft.com>
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link     http://www.magentocommerce.com/knowledge-base/
 */
class Totm_SagePayRecurringRewrite_Block_Adminhtml_Sales_Order_View_Tab_PaymentSchedule
	extends Ebizmarts_SagePayRecurring_Block_Adminhtml_Sales_Order_View_Tab_PaymentSchedule
{

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('totm/sagepayrecurringrewrite/profiles.phtml');
    }

}