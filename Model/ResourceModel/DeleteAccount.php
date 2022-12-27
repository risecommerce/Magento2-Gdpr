<?php
/**
 * Class DeleteAccount
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\Gdpr\Model\ResourceModel;


class DeleteAccount extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize ResourceModel
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('risecommerce_gdpr_delete_account_history', 'account_id');
    }
}
