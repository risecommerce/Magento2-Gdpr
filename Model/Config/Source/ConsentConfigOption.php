<?php
/**
 * Class ConsentConfigOption
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

class ConsentConfigOption implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Path to configuration, set multiselect options for consent checkbox
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'registrationpage', 'label' => __('Registration Page')],
            ['value' => 'contactpage', 'label' => __('Contact Page')],
            ['value' => 'newsletterform', 'label' => __('Newsletter Form')]
        ];
    }
}
