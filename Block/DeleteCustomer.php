<?php
/**
 * Class DeleteCustomer
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */

namespace Risecommerce\Gdpr\Block;

use Risecommerce\Gdpr\Helper\Data;
use Magento\Framework\View\Element\Template\Context;

class DeleteCustomer extends \Magento\Framework\View\Element\Template
{
    /**
     * GdprHelper
     *
     * @var Data
     */
    protected $helper;

    /**
     * DeleteCustomer constructor.
     *
     * @param Context $context context
     * @param Data    $helper  helper
     * @param array   $data    data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get system config data, when customer delete account also delete
     *
     * @return string
     */
    public function getCustomerDeleteData()
    {
        $deleteInfo = $this->helper->getCustomerConfig('customer_account_delete_also_delete');
        $deleteInfo = explode(',', $deleteInfo);
        return $deleteInfo;
    }

    /**
     * Get system config data, order status
     *
     * @return string
     */
    public function getCustomerOrderStatusData()
    {
        $deleteInfo = $this->helper->getCustomerConfig('order_status');
        $deleteInfo = explode(',', $deleteInfo);
        return $deleteInfo;
    }

    /**
     * Get system config data, warning message
     *
     * @return string
     */
    public function getWarningMessage()
    {
        $warningMessage = $this->helper->getCustomerConfig('delete_account_warning_message');
        return $warningMessage;
    }
}
