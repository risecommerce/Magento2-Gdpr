<?php
/**
 * Class AnonymousAccount
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\Gdpr\Model;

use Magento\Framework\Model\AbstractModel;

class AnonymousAccount extends AbstractModel
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Risecommerce\Gdpr\Model\ResourceModel\AnonymousAccount::class);
    }
}
