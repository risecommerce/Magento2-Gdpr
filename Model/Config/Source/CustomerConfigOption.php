<?php
/**
 * Class CustomerConfigOption
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\Gdpr\Model\Config\Source;

class CustomerConfigOption implements \Magento\Framework\Data\OptionSourceInterface
{
    public const UNDEFINED_OPTION_LABEL = '-- Please Select --';
    /**
     * Path to configuration, set multiselect options for customer policy
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('-- Please Select --')],
            ['value' => 'order', 'label' => __('Order')],
            ['value' => 'newsletter', 'label' => __('Newsletter')]
        ];
    }
}
