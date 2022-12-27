<?php
/**
 * Class Consent
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

class Consent extends \Magento\Framework\View\Element\Template
{
    /**
     * GdprHelper
     *
     * @var Data
     */
    protected $helper;

    /**
     * Consent constructor.
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
     * Get system configuration value of Show consent checkbox
     *
     * @return array
     */
    public function getConsentCheckboxData()
    {
        $consentInfo = $this->helper->getConsentConfig('show_consent_checkbox_in');
        $consentInfo = explode(',', $consentInfo);
        return $consentInfo;
    }

    /**
     * Get system configuration value of checkbox content
     *
     * @return string
     */
    public function getConsentContent()
    {
        $consentContentInfo = $this->helper->getConsentConfig('checkbox_content');
        return $consentContentInfo;
    }

    /**
     * Get system configuration value of Message before the checkbox
     *
     * @return string
     */
    public function getMsgContent()
    {
        $msgInfo = $this->helper->getConsentConfig('msg_before_checkbox');
        return $msgInfo;
    }
}
