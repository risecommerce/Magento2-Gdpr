<?php
/**
 * Class Collection
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\Gdpr\Model\ResourceModel\AnonymousAccount;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'anonymous_account_id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'anonymous_antity_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'anonymous_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Risecommerce\Gdpr\Model\AnonymousAccount::class,
            \Risecommerce\Gdpr\Model\ResourceModel\AnonymousAccount::class
        );
    }
}
