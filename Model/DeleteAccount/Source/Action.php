<?php
/**
 * Class Action
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\Gdpr\Model\DeleteAccount\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Action implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['label' => 'Accept', 'value' => 'Accepted'],
            ['label' => 'Reject', 'value' => 'Rejected']
        ];
        return $options;
    }
}
